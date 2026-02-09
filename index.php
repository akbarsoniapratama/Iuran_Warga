<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Iuran Warga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.0/lucide.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .modal { transition: opacity 0.25s ease; }
        .hidden { display: none; }

        @media print {
            nav, .no-print, button, select, #searchInput {
                display: none !important;
            }
            body { background-color: white !important; padding: 0 !important; }
            main { max-width: 100% !important; width: 100% !important; padding: 0 !important; margin: 0 !important; }
            .bg-white { box-shadow: none !important; border: none !important; }
            table { width: 100% !important; border: 1px solid #e2e8f0 !important; }
            th, td { border-bottom: 1px solid #e2e8f0 !important; }
            .print-header { display: block !important; margin-bottom: 20px; text-align: center; }
        }
        .print-header { display: none; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

    <nav class="bg-blue-600 text-white shadow-lg no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-2">
                    <i data-lucide="users" class="w-8 h-8"></i>
                    <span class="font-bold text-xl tracking-tight">KasWarga</span>
                </div>
                <div class="text-sm bg-blue-700 px-3 py-1 rounded-full border border-blue-400">
                    <span id="currentDate"></span>
                </div>
            </div>
        </div>
    </nav>

    <div class="print-header">
        <h1 class="text-2xl font-bold">LAPORAN IURAN WARGA</h1>
        <p class="text-lg" id="printMonthYear">Bulan: -</p>
        <hr class="my-4 border-slate-300">
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Stats Top -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total Warga (<span class="selected-month-text">-</span>)</p>
                        <h3 class="text-2xl font-bold text-slate-800" id="statTotalWarga">0</h3>
                    </div>
                    <div class="p-3 bg-blue-100 text-blue-600 rounded-lg no-print">
                        <i data-lucide="users"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Terkumpul (<span class="selected-month-text">Bulan Ini</span>)</p>
                        <h3 class="text-2xl font-bold text-green-600" id="statTotalUang">Rp 0</h3>
                    </div>
                    <div class="p-3 bg-green-100 text-green-600 rounded-lg no-print">
                        <i data-lucide="wallet"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Belum Bayar (<span class="selected-month-text">Bulan Ini</span>)</p>
                        <h3 class="text-2xl font-bold text-red-600" id="statBelumBayar">0</h3>
                    </div>
                    <div class="p-3 bg-red-100 text-red-600 rounded-lg no-print">
                        <i data-lucide="clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6 no-print">
            <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto flex-1">
                <div class="relative flex-1">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="text" id="searchInput" placeholder="Cari nama atau nomor rumah..." 
                        class="w-full pl-10 pr-4 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white shadow-sm transition-all">
                </div>
                <select id="statusFilter" class="bg-white border border-slate-200 px-4 py-2 rounded-lg text-sm shadow-sm outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">Semua Status</option>
                    <option value="paid">Sudah Lunas</option>
                    <option value="unpaid">Belum Bayar</option>
                </select>
            </div>
            <div class="flex flex-wrap gap-2 w-full md:w-auto">
                <button onclick="printReport()" class="bg-slate-800 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-slate-900 transition shadow-sm">
                    <i data-lucide="printer" class="w-4 h-4"></i> Cetak
                </button>
                <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-blue-700 transition shadow-sm">
                    <i data-lucide="plus" class="w-4 h-4"></i> Tambah Warga
                </button>
                <select id="monthFilter" class="bg-white border border-slate-200 px-4 py-2 rounded-lg shadow-sm outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                    <option value="0">Januari</option>
                    <option value="1">Februari</option>
                    <option value="2">Maret</option>
                    <option value="3">April</option>
                    <option value="4">Mei</option>
                    <option value="5">Juni</option>
                    <option value="6">Juli</option>
                    <option value="7">Agustus</option>
                    <option value="8">September</option>
                    <option value="9">Oktober</option>
                    <option value="10">November</option>
                    <option value="11">Desember</option>
                </select>
            </div>
        </div>

        <!-- Table Container -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama & Rumah</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-blue-600">Kas</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-green-600">Sampah</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right no-print">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="wargaTableBody" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
            <div id="emptyState" class="hidden py-16 flex flex-col items-center justify-center text-slate-400">
                <i data-lucide="search-x" class="w-12 h-12 mb-2 opacity-20"></i>
                <p>Data tidak ditemukan pada bulan ini.</p>
            </div>
        </div>
    </main>

    <!-- Modal Form -->
    <div id="wargaModal" class="modal fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 id="modalTitle" class="text-xl font-bold text-slate-800">Data Warga</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition"><i data-lucide="x"></i></button>
            </div>
            <form id="wargaForm" class="p-6 space-y-4">
                <input type="hidden" id="editIdx" value="-1">
                <div>
                    <label class="block text-sm font-semibold mb-1 text-slate-700">Nama Lengkap</label>
                    <input type="text" id="wargaNama" required placeholder="Contoh: Bpk. Joko" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1 text-slate-700">No. Rumah / Blok</label>
                    <input type="text" id="wargaRumah" required placeholder="Contoh: A-12" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1 text-slate-700">Kas (Rp)</label>
                        <input type="number" id="wargaKas" value="15000" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1 text-slate-700">Sampah (Rp)</label>
                        <input type="number" id="wargaSampah" value="15000" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeModal()" class="flex-1 py-2.5 border border-slate-200 rounded-lg font-medium hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="flex-1 py-2.5 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition shadow-md">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let dataWarga = JSON.parse(localStorage.getItem('kasWargaData')) || [];
        let selectedMonth = new Date().getMonth();
        const currentYear = new Date().getFullYear();
        const daftarBulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        const refreshIcons = () => window.lucide && window.lucide.createIcons();
        const formatRupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n);

        window.onload = () => {
            document.getElementById('currentDate').textContent = new Intl.DateTimeFormat('id-ID', { dateStyle: 'full' }).format(new Date());
            document.getElementById('monthFilter').value = selectedMonth;
            updateUI();
        };

        const updateUI = () => {
            const label = daftarBulan[selectedMonth];
            document.querySelectorAll('.selected-month-text').forEach(el => el.textContent = label);
            document.getElementById('printMonthYear').textContent = `Bulan: ${label} ${currentYear}`;
            renderTable();
            updateStats();
        };

        const renderTable = () => {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const statusQ = document.getElementById('statusFilter').value;
            const key = `${currentYear}-${selectedMonth}`;
            const body = document.getElementById('wargaTableBody');

            const filtered = dataWarga.filter(w => {
                const matchSearch = w.nama.toLowerCase().includes(query) || w.rumah.toLowerCase().includes(query);
                const isPaid = w.pembayaran?.[key];
                const matchStatus = statusQ === 'all' || (statusQ === 'paid' ? isPaid : !isPaid);
                return matchSearch && matchStatus;
            });

            if (filtered.length === 0) {
                body.innerHTML = '';
                document.getElementById('emptyState').classList.remove('hidden');
                return;
            }

            document.getElementById('emptyState').classList.add('hidden');
            body.innerHTML = filtered.map(w => {
                const idx = dataWarga.findIndex(item => item.id === w.id);
                const isLunas = w.pembayaran?.[key];
                const total = w.kas + w.sampah;
                return `
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800">${w.nama}</div>
                            <div class="text-xs text-slate-400 font-medium">${w.rumah}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-600 font-medium">${formatRupiah(w.kas)}</td>
                        <td class="px-6 py-4 text-slate-600 font-medium">${formatRupiah(w.sampah)}</td>
                        <td class="px-6 py-4 font-bold text-slate-700">${formatRupiah(total)}</td>
                        <td class="px-6 py-4">
                            <button onclick="toggleBayar(${idx})" class="group flex items-center gap-2 px-3 py-1.5 rounded-full text-[10px] font-bold transition-all ${isLunas ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700 hover:bg-amber-200'}">
                                <span class="w-1.5 h-1.5 rounded-full ${isLunas ? 'bg-green-500' : 'bg-amber-500 animate-pulse'}"></span>
                                ${isLunas ? 'LUNAS' : 'BELUM'}
                            </button>
                        </td>
                        <td class="px-6 py-4 text-right no-print">
                            <div class="flex justify-end gap-3">
                                <button onclick="openModal(${idx})" 
                                    class="flex items-center gap-1.5 text-blue-600 hover:text-blue-800 transition-colors" 
                                    title="Ubah Data">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    <span class="text-[10px] font-bold uppercase">Edit</span>
                                </button>
                                <button onclick="deleteWarga(${idx})" 
                                    class="flex items-center gap-1.5 text-red-600 hover:text-red-800 transition-colors" 
                                    title="Hapus Data">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    <span class="text-[10px] font-bold uppercase">Hapus</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
            refreshIcons();
        };

        const updateStats = () => {
            const key = `${currentYear}-${selectedMonth}`;
            const totalWarga = dataWarga.length;
            const lunas = dataWarga.filter(w => w.pembayaran?.[key]).length;
            const totalUang = dataWarga.reduce((acc, w) => acc + (w.pembayaran?.[key] ? (w.kas + w.sampah) : 0), 0);

            document.getElementById('statTotalWarga').textContent = totalWarga;
            document.getElementById('statTotalUang').textContent = formatRupiah(totalUang);
            document.getElementById('statBelumBayar').textContent = totalWarga - lunas;
        };

        const toggleBayar = (idx) => {
            const key = `${currentYear}-${selectedMonth}`;
            if(!dataWarga[idx].pembayaran) dataWarga[idx].pembayaran = {};
            dataWarga[idx].pembayaran[key] = !dataWarga[idx].pembayaran[key];
            save();
        };

        const save = () => {
            localStorage.setItem('kasWargaData', JSON.stringify(dataWarga));
            updateUI();
        };

        const openModal = (idx = -1) => {
            const m = document.getElementById('wargaModal');
            m.classList.remove('hidden');
            if(idx > -1) {
                const w = dataWarga[idx];
                document.getElementById('modalTitle').textContent = "Ubah Data Warga";
                document.getElementById('editIdx').value = idx;
                document.getElementById('wargaNama').value = w.nama;
                document.getElementById('wargaRumah').value = w.rumah;
                document.getElementById('wargaKas').value = w.kas;
                document.getElementById('wargaSampah').value = w.sampah;
            } else {
                document.getElementById('wargaForm').reset();
                document.getElementById('modalTitle').textContent = "Tambah Warga Baru";
                document.getElementById('editIdx').value = -1;
            }
        };

        const closeModal = () => document.getElementById('wargaModal').classList.add('hidden');

        document.getElementById('wargaForm').onsubmit = (e) => {
            e.preventDefault();
            const idx = parseInt(document.getElementById('editIdx').value);
            const entry = {
                nama: document.getElementById('wargaNama').value,
                rumah: document.getElementById('wargaRumah').value,
                kas: parseInt(document.getElementById('wargaKas').value) || 0,
                sampah: parseInt(document.getElementById('wargaSampah').value) || 0
            };
            if(idx > -1) { 
                dataWarga[idx] = {...dataWarga[idx], ...entry}; 
            } else { 
                dataWarga.push({ id: Date.now(), ...entry, pembayaran: {} }); 
            }
            save();
            closeModal();
        };

        const deleteWarga = (idx) => { 
            if(confirm("Apakah Anda yakin ingin menghapus data warga ini?")) { 
                dataWarga.splice(idx, 1); 
                save(); 
            } 
        };
        
        document.getElementById('monthFilter').onchange = (e) => { 
            selectedMonth = parseInt(e.target.value); 
            updateUI(); 
        };
        
        document.getElementById('statusFilter').onchange = () => renderTable();
        document.getElementById('searchInput').oninput = () => renderTable();
        const printReport = () => window.print();
    </script>
</body>
</html>