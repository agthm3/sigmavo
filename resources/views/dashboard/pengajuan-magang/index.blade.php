@extends('layouts.dashboard')

@section('content')

    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50">
        
        <!-- TOPBAR -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 lg:px-6 shrink-0 shadow-sm">
            <div class="flex items-center">
                <button class="md:hidden text-gray-500 hover:text-vokasi-primary focus:outline-none p-2 rounded-md transition-colors">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <nav class="hidden md:flex ml-4 text-sm font-medium text-gray-500 space-x-2">
                    <a href="#" class="hover:text-vokasi-primary">Daftar Lowongan</a>
                    <span>/</span>
                    <span class="text-gray-800">Pengajuan Magang</span>
                </nav>
            </div>
            <div class="flex items-center space-x-4">
                <button class="text-gray-500 hover:text-vokasi-primary transition-colors relative" title="Notifikasi">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">6</span>
                </button>
                <div class="flex items-center space-x-2 cursor-pointer border-l pl-4 border-gray-200">
                    <img src="https://ui-avatars.com/api/?name=Admin+Prodi&background=37A7AC&color=fff" alt="Profile" class="w-8 h-8 rounded-full border border-gray-200 shadow-sm">
                    <div class="hidden md:block text-sm text-right">
                        <p class="font-bold text-gray-700 leading-tight">Admin Prodi</p>
                        <p class="text-xs text-vokasi-primary font-semibold">Vokasi UNHAS</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative">
            
            <div class="max-w-7xl mx-auto w-full flex-1 space-y-6">
                
                <!-- Page Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Pengajuan Magang & Penerbitan Surat</h2>
                        <p class="text-sm text-gray-500 mt-1">Verifikasi permohonan pengajuan magang dan cetak Surat Pengantar/Tugas Pengabdian.</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-semibold py-2 px-4 rounded-lg transition-colors shadow-sm flex items-center">
                            <i class="fas fa-print mr-2"></i> Cetak Rekap Surat
                        </button>
                    </div>
                </div>

                <!-- SUMMARY CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-paper-plane text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Total Pengajuan</p>
                            <p class="text-xl font-bold text-gray-800 leading-none mt-1">112 Berkas</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-yellow-50 text-yellow-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-clock text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Perlu Surat Pengantar</p>
                            <p class="text-xl font-bold text-yellow-600 leading-none mt-1">6 Pengajuan</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-file-circle-check text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Surat Diterbitkan</p>
                            <p class="text-xl font-bold text-green-600 leading-none mt-1">98 Surat</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-route text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Pengajuan Mandiri</p>
                            <p class="text-xl font-bold text-purple-600 leading-none mt-1">34 Berkas</p>
                        </div>
                    </div>
                </div>

                <!-- TABLE SECTION -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    
                    <!-- Table Toolbar -->
                    <div class="p-5 border-b border-gray-100 flex flex-col lg:flex-row justify-between lg:items-center gap-4 bg-gray-50/50">
                        <div class="relative w-full lg:w-96">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                            <input type="text" placeholder="Cari nama mahasiswa, NIM, atau perusahaan..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary">
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            <select class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option>Jalur: Semua Jalur</option>
                                <option>Lowongan Reguler</option>
                                <option>Magang Mandiri</option>
                            </select>

                            <select class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option>Status Surat: Menunggu Penerbitan</option>
                                <option>Status Surat: Terbit (Siap Ambil)</option>
                                <option>Status Surat: Ditolak</option>
                                <option>Semua Status</option>
                            </select>
                        </div>
                    </div>

                    <!-- Responsive Table -->
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[1100px]">
                            <thead>
                                <tr class="bg-gray-100/60 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <th class="p-4 w-12 text-center">No</th>
                                    <th class="p-4 w-60">Mahasiswa Pemohon</th>
                                    <th class="p-4 w-32 text-center">Jalur Magang</th>
                                    <th class="p-4 min-w-[220px]">Instansi / Perusahaan Tujuan</th>
                                    <th class="p-4 w-36">Tgl Pengajuan</th>
                                    <th class="p-4 w-36 text-center">Status Surat</th>
                                    <th class="p-4 w-40 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                
                                <!-- Baris 1: Menunggu Surat (Mandiri) -->
                                <tr class="hover:bg-gray-50 transition-colors bg-yellow-50/20">
                                    <td class="p-4 text-center text-gray-500 font-medium">1</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name=Fadehl+Thristansyah&background=f3f4f6&color=37A7AC" alt="Avatar" class="w-9 h-9 rounded-full border border-gray-200">
                                            <div>
                                                <p class="font-bold text-gray-800">Fadehl Thristansyah</p>
                                                <p class="text-xs text-gray-500">H071231012</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-purple-100 text-purple-700 text-[10px] font-bold rounded-full border border-purple-200">
                                            MANDIRI
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-bold text-gray-800">PT. SmartPlay Inovasi</p>
                                        <p class="text-xs text-gray-500">Divisi R&D Produk STEM</p>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-medium text-gray-700">23 Jul 2026</p>
                                        <p class="text-xs text-gray-400">09:10 WITA</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-yellow-100 text-yellow-700 text-[10px] font-bold rounded-full border border-yellow-200">
                                            <i class="fas fa-spinner fa-spin mr-1"></i> Perlu Surat
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button onclick="toggleSuratModal(true)" class="bg-vokasi-primary hover:bg-vokasi-dark text-white text-xs font-bold py-1.5 px-3 rounded shadow-sm transition-colors flex items-center justify-center mx-auto">
                                            <i class="fas fa-file-export mr-1.5"></i> Terbit Surat
                                        </button>
                                    </td>
                                </tr>

                                <!-- Baris 2: Surat Terbit (Reguler) -->
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-center text-gray-500 font-medium">2</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name=Andi+Reza&background=f3f4f6&color=37A7AC" alt="Avatar" class="w-9 h-9 rounded-full border border-gray-200">
                                            <div>
                                                <p class="font-bold text-gray-800">Andi Reza Syahputra</p>
                                                <p class="text-xs text-gray-500">H071231045</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-blue-100 text-blue-700 text-[10px] font-bold rounded-full border border-blue-200">
                                            REGULER
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-bold text-gray-800">BRIDA Kota Makassar</p>
                                        <p class="text-xs text-gray-500">Staf Analis Data</p>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-medium text-gray-700">12 Jul 2026</p>
                                        <p class="text-xs text-gray-400">14:00 WITA</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full border border-green-200">
                                            <i class="fas fa-check-circle mr-1"></i> Surat Terbit
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="#" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold py-1.5 px-3 rounded transition-colors" title="Unduh Surat Pengantar PDF">
                                                <i class="fas fa-download mr-1"></i> PDF
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Baris 3: Surat Terbit (Mandiri) -->
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-center text-gray-500 font-medium">3</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name=Siti+Nurhaliza&background=f3f4f6&color=37A7AC" alt="Avatar" class="w-9 h-9 rounded-full border border-gray-200">
                                            <div>
                                                <p class="font-bold text-gray-800">Siti Nurhaliza</p>
                                                <p class="text-xs text-gray-500">H071231088</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-purple-100 text-purple-700 text-[10px] font-bold rounded-full border border-purple-200">
                                            MANDIRI
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-bold text-gray-800">PT. Agro Nusantara</p>
                                        <p class="text-xs text-gray-500">Quality Control</p>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-medium text-gray-700">08 Jul 2026</p>
                                        <p class="text-xs text-gray-400">10:30 WITA</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full border border-green-200">
                                            <i class="fas fa-check-circle mr-1"></i> Surat Terbit
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="#" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold py-1.5 px-3 rounded transition-colors" title="Unduh Surat Pengantar PDF">
                                                <i class="fas fa-download mr-1"></i> PDF
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Controls -->
                    <div class="p-4 border-t border-gray-100 flex items-center justify-between bg-white text-sm">
                        <span class="text-gray-500">Menampilkan 1 hingga 3 dari 112 pengajuan</span>
                        <div class="flex space-x-1">
                            <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-400 bg-gray-50 cursor-not-allowed">Sebelummya</button>
                            <button class="px-3 py-1 border border-vokasi-primary rounded-lg text-white bg-vokasi-primary font-medium">1</button>
                            <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">2</button>
                            <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">Selanjutnya</button>
                        </div>
                    </div>

                </div>

            </div>

            <!-- FOOTER -->
            <footer class="mt-8 py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
                Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
            </footer>

        </main>
    </div>

    <!-- ========================================== -->
    <!-- MODAL POPUP: TERBITKAN SURAT PENGANTAR -->
    <!-- ========================================== -->
    <div id="modalTerbitSurat" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 transition-opacity duration-300">
        <div class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-2xl overflow-hidden transform transition-all scale-95 duration-300">
            
            <!-- Modal Header -->
            <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <i class="fas fa-file-signature text-lg"></i>
                    <h3 class="font-bold text-lg">Penerbitan Surat Pengantar Magang</h3>
                </div>
                <button onclick="toggleSuratModal(false)" class="text-white/80 hover:text-white hover:bg-black/10 p-1.5 rounded-lg transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form action="#" method="POST" class="p-6 space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-xs text-blue-800 flex items-center gap-3">
                    <i class="fas fa-circle-info text-blue-500 text-base shrink-0"></i>
                    <p>Sistem akan membuat nomor surat otomatis dan meng-generate PDF Surat Pengantar Resmi bertanda tangan digital Koprodi.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    <!-- Pemohon -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Mahasiswa</label>
                        <input type="text" value="Fadehl Thristansyah (H071231012)" readonly class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-sm text-gray-600 font-medium cursor-not-allowed">
                    </div>

                    <!-- Perusahaan Tujuan -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Instansi / Perusahaan Tujuan</label>
                        <input type="text" value="PT. SmartPlay Inovasi" readonly class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-sm text-gray-600 font-medium cursor-not-allowed">
                    </div>

                    <!-- Nomor Surat -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Surat Pengantar <span class="text-red-500">*</span></label>
                        <input type="text" value="1024/UN4.15/TU.02/2026" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                    </div>

                    <!-- Perihal -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Perihal Surat</label>
                        <input type="text" value="Permohonan Pelaksanaan Magang Industri Mahasiswa Vokasi" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors">
                    </div>

                    <!-- Tanggal Mulai -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Magang</label>
                        <input type="date" value="2026-08-01" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors">
                    </div>

                    <!-- Tanggal Selesai -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai Magang</label>
                        <input type="date" value="2027-01-31" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors">
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-6">
                    <button type="button" onclick="toggleSuratModal(false)" class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 font-medium text-sm transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-lg font-bold text-sm transition-colors shadow-sm flex items-center">
                        <i class="fas fa-print mr-2"></i> Terbitkan & Generate PDF
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- JAVASCRIPT UNTUK MODAL TOGGLE -->
    <script>
        function toggleSuratModal(show) {
            const modal = document.getElementById('modalTerbitSurat');
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }
    </script>
@endsection