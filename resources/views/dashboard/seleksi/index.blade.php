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
                    <span class="text-gray-800">Seleksi Pelamar</span>
                </nav>
            </div>
            <div class="flex items-center space-x-4">
                <button class="text-gray-500 hover:text-vokasi-primary transition-colors relative" title="Notifikasi">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">8</span>
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
                        <h2 class="text-2xl font-bold text-gray-800">Seleksi & Penempatan Pelamar</h2>
                        <p class="text-sm text-gray-500 mt-1">Tinjau berkas pendaftaran mahasiswa, tetapkan status seleksi, dan alokasikan Dosen Pendamping.</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition-colors shadow-sm flex items-center">
                            <i class="fas fa-file-excel mr-2"></i> Export Data Pelamar
                        </button>
                    </div>
                </div>

                <!-- SUMMARY CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-user-group text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Total Pelamar</p>
                            <p class="text-xl font-bold text-gray-800 leading-none mt-1">68 Orang</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-yellow-50 text-yellow-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-hourglass-start text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Belum Diseleksi</p>
                            <p class="text-xl font-bold text-yellow-600 leading-none mt-1">8 Pelamar</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-user-check text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Diterima Magang</p>
                            <p class="text-xl font-bold text-green-600 leading-none mt-1">45 Orang</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-user-xmark text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Ditolak / Gugur</p>
                            <p class="text-xl font-bold text-red-600 leading-none mt-1">15 Orang</p>
                        </div>
                    </div>
                </div>

                <!-- TABLE SECTION -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    
                    <!-- Table Toolbar -->
                    <div class="p-5 border-b border-gray-100 flex flex-col lg:flex-row justify-between lg:items-center gap-4 bg-gray-50/50">
                        <div class="relative w-full lg:w-96">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                            <input type="text" placeholder="Cari nama mahasiswa, NIM, atau posisi..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary">
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            <select class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option>Filter Lowongan: Semua</option>
                                <option>STEM Product Designer (SmartPlay)</option>
                                <option>Staf Analis Data (BRIDA)</option>
                                <option>Quality Control (Agro Nusantara)</option>
                            </select>

                            <select class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option>Status: Menunggu Seleksi</option>
                                <option>Status: Diterima</option>
                                <option>Status: Ditolak</option>
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
                                    <th class="p-4 w-60">Mahasiswa Pelamar</th>
                                    <th class="p-4 w-56">Posisi & Perusahaan</th>
                                    <th class="p-4 w-48">Dosen Pendamping</th>
                                    <th class="p-4 w-32">Tgl Melamar</th>
                                    <th class="p-4 w-32 text-center">Status Seleksi</th>
                                    <th class="p-4 w-36 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                
                                <!-- Baris 1: Menunggu Seleksi -->
                                <tr class="hover:bg-gray-50 transition-colors bg-yellow-50/20">
                                    <td class="p-4 text-center text-gray-500 font-medium">1</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name=Fadehl+Thristansyah&background=f3f4f6&color=37A7AC" alt="Avatar" class="w-9 h-9 rounded-full border border-gray-200">
                                            <div>
                                                <p class="font-bold text-gray-800">Fadehl Thristansyah</p>
                                                <p class="text-xs text-gray-500">NIM: H071231012</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-bold text-gray-800">STEM Product Designer</p>
                                        <p class="text-xs text-vokasi-primary font-medium">PT. SmartPlay Inovasi</p>
                                    </td>
                                    <td class="p-4">
                                        <span class="text-xs text-gray-400 italic"><i class="fas fa-user-plus mr-1"></i> Belum Ditetapkan</span>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-medium text-gray-700">22 Jul 2026</p>
                                        <p class="text-xs text-gray-400">11:00 WITA</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-yellow-100 text-yellow-700 text-[10px] font-bold rounded-full border border-yellow-200">
                                            <i class="fas fa-spinner fa-spin mr-1"></i> Menunggu
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button onclick="toggleSeleksiModal(true)" class="bg-vokasi-primary hover:bg-vokasi-dark text-white text-xs font-bold py-1.5 px-3 rounded shadow-sm transition-colors flex items-center justify-center mx-auto">
                                            <i class="fas fa-file-signature mr-1.5"></i> Tinjau Berkas
                                        </button>
                                    </td>
                                </tr>

                                <!-- Baris 2: Diterima & Dosen Ditetapkan -->
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-center text-gray-500 font-medium">2</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name=Andi+Reza&background=f3f4f6&color=37A7AC" alt="Avatar" class="w-9 h-9 rounded-full border border-gray-200">
                                            <div>
                                                <p class="font-bold text-gray-800">Andi Reza Syahputra</p>
                                                <p class="text-xs text-gray-500">NIM: H071231045</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-bold text-gray-800">Staf Analis Data</p>
                                        <p class="text-xs text-vokasi-primary font-medium">BRIDA Kota Makassar</p>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-bold text-gray-800 text-xs">Dr. Ir. Budi Santoso, M.T.</p>
                                        <p class="text-[10px] text-green-600 font-semibold"><i class="fas fa-check-circle"></i> Assigned</p>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-medium text-gray-700">10 Jul 2026</p>
                                        <p class="text-xs text-gray-400">09:30 WITA</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full border border-green-200">
                                            <i class="fas fa-check-circle mr-1"></i> Diterima
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold py-1.5 px-3 rounded transition-colors" title="Lihat Detail">
                                            Detail
                                        </button>
                                    </td>
                                </tr>

                                <!-- Baris 3: Ditolak -->
                                <tr class="hover:bg-gray-50 transition-colors bg-gray-50/50">
                                    <td class="p-4 text-center text-gray-500 font-medium">3</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name=Dimas+Saputra&background=f3f4f6&color=37A7AC" alt="Avatar" class="w-9 h-9 rounded-full border border-gray-200">
                                            <div>
                                                <p class="font-bold text-gray-800">Dimas Saputra</p>
                                                <p class="text-xs text-gray-500">NIM: H071231022</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-bold text-gray-800">Quality Control Pertanian</p>
                                        <p class="text-xs text-vokasi-primary font-medium">PT. Agro Nusantara</p>
                                    </td>
                                    <td class="p-4">
                                        <span class="text-xs text-gray-400 font-mono">--</span>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-medium text-gray-700">05 Jul 2026</p>
                                        <p class="text-xs text-gray-400">14:15 WITA</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-red-100 text-red-700 text-[10px] font-bold rounded-full border border-red-200">
                                            <i class="fas fa-times-circle mr-1"></i> Ditolak
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold py-1.5 px-3 rounded transition-colors" title="Lihat Detail">
                                            Detail
                                        </button>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Controls -->
                    <div class="p-4 border-t border-gray-100 flex items-center justify-between bg-white text-sm">
                        <span class="text-gray-500">Menampilkan 1 hingga 3 dari 68 pelamar</span>
                        <div class="flex space-x-1">
                            <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-400 bg-gray-50 cursor-not-allowed">Sebelummya</button>
                            <button class="px-3 py-1 border border-vokasi-primary rounded-lg text-white bg-vokasi-primary font-medium">1</button>
                            <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">2</button>
                            <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">3</button>
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
    <!-- MODAL POPUP: TINJAU BERKAS & SELEKSI PELAMAR -->
    <!-- ========================================== -->
    <div id="modalSeleksiPelamar" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 transition-opacity duration-300">
        <div class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-3xl overflow-hidden transform transition-all scale-95 duration-300 max-h-[90vh] flex flex-col">
            
            <!-- Modal Header -->
            <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center shrink-0">
                <div class="flex items-center gap-2">
                    <i class="fas fa-user-check text-lg"></i>
                    <h3 class="font-bold text-lg">Tinjau Berkas & Keputusan Seleksi</h3>
                </div>
                <button onclick="toggleSeleksiModal(false)" class="text-white/80 hover:text-white hover:bg-black/10 p-1.5 rounded-lg transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-6 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                
                <!-- Profile Pelamar Summary -->
                <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <img src="https://ui-avatars.com/api/?name=Fadehl+Thristansyah&background=37A7AC&color=fff" alt="Pelamar" class="w-16 h-16 rounded-full border-2 border-white shadow-sm shrink-0">
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-bold text-lg text-gray-800">Fadehl Thristansyah</h4>
                                <p class="text-xs text-gray-500">NIM: H071231012 — D4 Teknologi Produksi Tanaman</p>
                            </div>
                            <span class="text-xs font-bold text-vokasi-primary bg-blue-50 px-2.5 py-1 rounded border border-blue-200">IPK: 3.85</span>
                        </div>
                        <div class="mt-2 text-xs text-gray-600 space-y-1">
                            <p><i class="fas fa-briefcase text-gray-400 mr-1.5"></i> Melamar Posisi: <strong>STEM Product Designer</strong></p>
                            <p><i class="fas fa-building text-gray-400 mr-1.5"></i> Instansi: <strong>PT. SmartPlay Inovasi</strong></p>
                        </div>
                    </div>
                </div>

                <!-- Dokumen Lampiran Pelamar -->
                <div>
                    <h5 class="font-bold text-gray-800 text-sm mb-3 border-b border-gray-100 pb-2 flex items-center">
                        <i class="fas fa-folder-open text-vokasi-primary mr-2"></i> Dokumen Persyaratan
                    </h5>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <a href="#" class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200 hover:border-vokasi-primary transition-colors group">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-file-pdf text-red-500 text-xl"></i>
                                <div>
                                    <p class="text-xs font-bold text-gray-800 group-hover:text-vokasi-primary">Curriculum Vitae (CV)</p>
                                    <p class="text-[10px] text-gray-400">PDF • 1.2 MB</p>
                                </div>
                            </div>
                            <i class="fas fa-download text-xs text-gray-400 group-hover:text-vokasi-primary"></i>
                        </a>

                        <a href="#" class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200 hover:border-vokasi-primary transition-colors group">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-file-pdf text-red-500 text-xl"></i>
                                <div>
                                    <p class="text-xs font-bold text-gray-800 group-hover:text-vokasi-primary">Transkrip Nilai</p>
                                    <p class="text-[10px] text-gray-400">PDF • 850 KB</p>
                                </div>
                            </div>
                            <i class="fas fa-download text-xs text-gray-400 group-hover:text-vokasi-primary"></i>
                        </a>
                    </div>
                </div>

                <!-- Form Keputusan Seleksi -->
                <form action="#" method="POST" class="space-y-4 pt-2">
                    <h5 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-2 flex items-center">
                        <i class="fas fa-user-gear text-vokasi-primary mr-2"></i> Form Keputusan & Penugasan
                    </h5>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Keputusan Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Keputusan Seleksi <span class="text-red-500">*</span></label>
                            <select class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                                <option value="diterima" selected>Diterima Magang</option>
                                <option value="ditolak">Ditolak / Tidak Lolos</option>
                                <option value="wawancara">Panggil Wawancara</option>
                            </select>
                        </div>

                        <!-- Assign Dosen Pendamping -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Assign Dosen Pendamping <span class="text-red-500">*</span></label>
                            <select class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                                <option value="" disabled>Pilih Dosen Pendamping</option>
                                <option value="1" selected>Dr. Ir. Budi Santoso, M.T.</option>
                                <option value="2">Andi Rahman, S.T., M.Eng.</option>
                                <option value="3">Siti Aisyah, S.Kom., M.Cs.</option>
                            </select>
                        </div>

                        <!-- Catatan Seleksi -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan / Alasan Penolakan</label>
                            <textarea rows="2" placeholder="Tuliskan catatan khusus untuk mahasiswa..." class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-6 shrink-0">
                        <button type="button" onclick="toggleSeleksiModal(false)" class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 font-medium text-sm transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-lg font-bold text-sm transition-colors shadow-sm flex items-center">
                            <i class="fas fa-check mr-2"></i> Simpan Keputusan
                        </button>
                    </div>
                </form>

            </div>

        </div>
    </div>

    <!-- JAVASCRIPT UNTUK MODAL TOGGLE -->
    <script>
        function toggleSeleksiModal(show) {
            const modal = document.getElementById('modalSeleksiPelamar');
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }
    </script>

@endsection