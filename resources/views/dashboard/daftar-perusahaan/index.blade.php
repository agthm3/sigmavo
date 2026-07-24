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
                    <span class="text-gray-800">Daftar Perusahaan</span>
                </nav>
            </div>
            <div class="flex items-center space-x-4">
                <button class="text-gray-500 hover:text-vokasi-primary transition-colors relative" title="Notifikasi">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">3</span>
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
                        <h2 class="text-2xl font-bold text-gray-800">Perusahaan Mitra Magang</h2>
                        <p class="text-sm text-gray-500 mt-1">Kelola data instansi/perusahaan mitra penyedia lowongan magang Vokasi UNHAS.</p>
                    </div>
                    <div class="flex gap-2">
                        <!-- TRIGGER MODAL -->
                        <button onclick="toggleCompanyModal(true)" class="bg-vokasi-primary hover:bg-vokasi-dark text-white text-sm font-semibold py-2 px-4 rounded-lg transition-colors shadow-sm flex items-center">
                            <i class="fas fa-building-circle-check mr-2"></i> Tambah Perusahaan Baru
                        </button>
                    </div>
                </div>

                <!-- SUMMARY CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-building text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Total Mitra</p>
                            <p class="text-xl font-bold text-gray-800 leading-none mt-1">87 Perusahaan</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-briefcase text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Lowongan Aktif</p>
                            <p class="text-xl font-bold text-green-600 leading-none mt-1">42 Program</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-handshake text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Mitra MoU / MoA</p>
                            <p class="text-xl font-bold text-purple-600 leading-none mt-1">64 Resmi</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-user-tie text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Supervisor Terdaftar</p>
                            <p class="text-xl font-bold text-orange-600 leading-none mt-1">98 Akun</p>
                        </div>
                    </div>
                </div>

                <!-- TABLE SECTION -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    
                    <!-- Table Toolbar -->
                    <div class="p-5 border-b border-gray-100 flex flex-col lg:flex-row justify-between lg:items-center gap-4 bg-gray-50/50">
                        <div class="relative w-full lg:w-96">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                            <input type="text" placeholder="Cari nama perusahaan, sektor, atau kota..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary">
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            <select class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option>Semua Sektor Industri</option>
                                <option>Teknologi Informasi</option>
                                <option>Pertanian / Agrikultur</option>
                                <option>Pemerintahan</option>
                                <option>Kesehatan</option>
                            </select>

                            <select class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option>Semua Lokasi</option>
                                <option>Makassar</option>
                                <option>Gowa / Maros</option>
                                <option>Luar Sulawesi Selatan</option>
                            </select>
                        </div>
                    </div>

                    <!-- Responsive Table -->
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[1100px]">
                            <thead>
                                <tr class="bg-gray-100/60 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <th class="p-4 w-12 text-center">No</th>
                                    <th class="p-4 w-64">Nama Perusahaan</th>
                                    <th class="p-4 w-40">Sektor Industri</th>
                                    <th class="p-4 min-w-[200px]">Kota / Alamat & Koordinat</th>
                                    <th class="p-4 w-32 text-center">Program Aktif</th>
                                    <th class="p-4 w-32 text-center">Status Kerjasama</th>
                                    <th class="p-4 w-36 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                
                                <!-- Baris 1 -->
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-center text-gray-500 font-medium">1</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600 shrink-0 font-bold">
                                                SP
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800">PT. SmartPlay Inovasi</p>
                                                <p class="text-xs text-gray-500">smartplay.co.id</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-semibold rounded">
                                            Edu-Tech / Manufaktur
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-medium text-gray-800">Makassar, Sulsel</p>
                                        <p class="text-[10px] text-gray-400 font-mono"><i class="fas fa-map-marker-alt text-red-500 mr-1"></i> -5.1322, 119.4255</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">
                                            2 Program
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-purple-100 text-purple-700 text-[10px] font-bold rounded-full border border-purple-200">
                                            MoU Resmi
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold py-1.5 px-2.5 rounded transition-colors" title="Edit Data">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="bg-vokasi-primary hover:bg-vokasi-dark text-white text-xs font-bold py-1.5 px-2.5 rounded transition-colors shadow-sm" title="Lihat Lowongan">
                                                Lowongan
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Baris 2 -->
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-center text-gray-500 font-medium">2</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-red-50 border border-red-100 flex items-center justify-center text-red-500 shrink-0 font-bold">
                                                BR
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800">BRIDA Kota Makassar</p>
                                                <p class="text-xs text-gray-500">brida.makassarkota.go.id</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded">
                                            Pemerintahan / Riset
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-medium text-gray-800">Makassar, Sulsel</p>
                                        <p class="text-[10px] text-gray-400 font-mono"><i class="fas fa-map-marker-alt text-red-500 mr-1"></i> -5.1480, 119.4120</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">
                                            1 Program
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-purple-100 text-purple-700 text-[10px] font-bold rounded-full border border-purple-200">
                                            MoU Resmi
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold py-1.5 px-2.5 rounded transition-colors" title="Edit Data">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="bg-vokasi-primary hover:bg-vokasi-dark text-white text-xs font-bold py-1.5 px-2.5 rounded transition-colors shadow-sm" title="Lihat Lowongan">
                                                Lowongan
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Baris 3 -->
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-center text-gray-500 font-medium">3</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-green-50 border border-green-100 flex items-center justify-center text-green-600 shrink-0 font-bold">
                                                AN
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800">PT. Agro Nusantara</p>
                                                <p class="text-xs text-gray-500">agronusantara.co.id</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-green-50 text-green-700 text-xs font-semibold rounded">
                                            Pertanian / Agrikultur
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-medium text-gray-800">Maros, Sulsel</p>
                                        <p class="text-[10px] text-gray-400 font-mono"><i class="fas fa-map-marker-alt text-red-500 mr-1"></i> -5.0122, 119.5780</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded-full">
                                            0 Program
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-blue-100 text-blue-700 text-[10px] font-bold rounded-full border border-blue-200">
                                            Mandiri Partner
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold py-1.5 px-2.5 rounded transition-colors" title="Edit Data">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="bg-vokasi-primary hover:bg-vokasi-dark text-white text-xs font-bold py-1.5 px-2.5 rounded transition-colors shadow-sm" title="Lihat Lowongan">
                                                Lowongan
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Controls -->
                    <div class="p-4 border-t border-gray-100 flex items-center justify-between bg-white text-sm">
                        <span class="text-gray-500">Menampilkan 1 hingga 3 dari 87 perusahaan</span>
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
    <!-- MODAL POPUP: TAMBAH PERUSAHAAN MITRA -->
    <!-- ========================================== -->
    <div id="modalTambahPerusahaan" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 transition-opacity duration-300">
        <div class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-2xl overflow-hidden transform transition-all scale-95 duration-300">
            
            <!-- Modal Header -->
            <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <i class="fas fa-building-circle-check text-lg"></i>
                    <h3 class="font-bold text-lg">Tambah Perusahaan Mitra Baru</h3>
                </div>
                <button onclick="toggleCompanyModal(false)" class="text-white/80 hover:text-white hover:bg-black/10 p-1.5 rounded-lg transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Modal Body / Form -->
            <form action="#" method="POST" class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    <!-- Nama Perusahaan -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Instansi / Perusahaan <span class="text-red-500">*</span></label>
                        <input type="text" placeholder="Contoh: PT. Inovasi Teknologi Nusantara" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                    </div>

                    <!-- Sektor Industri -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sektor / Bidang Industri <span class="text-red-500">*</span></label>
                        <select class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                            <option value="" disabled selected>Pilih Bidang</option>
                            <option value="Teknologi Informasi">Teknologi Informasi / Software</option>
                            <option value="Pertanian">Pertanian / Agrikultur</option>
                            <option value="Pemerintahan">Instansi Pemerintah</option>
                            <option value="Kesehatan">Kesehatan / Rumah Sakit</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <!-- Status Kerjasama -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status Kerjasama</label>
                        <select class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors">
                            <option value="MoU Resmi">MoU / MoA Resmi UNHAS</option>
                            <option value="Mitra Reguler">Mitra Reguler Prodi</option>
                            <option value="Mandiri Partner">Pengajuan Mandiri Mahasiswa</option>
                        </select>
                    </div>

                    <!-- Website -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Website Perusahaan</label>
                        <input type="url" placeholder="https://www.company.com" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors">
                    </div>

                    <!-- Email Kontak / HR -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Resmi / HRD <span class="text-red-500">*</span></label>
                        <input type="email" placeholder="hrd@company.com" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                    </div>

                    <!-- Alamat Lengkap -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap Perusahaan <span class="text-red-500">*</span></label>
                        <textarea rows="2" placeholder="Jl. Perintis Kemerdekaan KM..." class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors resize-none" required></textarea>
                    </div>

                    <!-- Titik Koordinat Geofencing -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Latitude (GPS) <span class="text-red-500">*</span></label>
                        <input type="text" placeholder="-5.1322..." class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Longitude (GPS) <span class="text-red-500">*</span></label>
                        <input type="text" placeholder="119.4255..." class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-6">
                    <button type="button" onclick="toggleCompanyModal(false)" class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 font-medium text-sm transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-lg font-bold text-sm transition-colors shadow-sm flex items-center">
                        <i class="fas fa-save mr-2"></i> Simpan Perusahaan
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- JAVASCRIPT UNTUK MODAL TOGGLE -->
    <script>
        function toggleCompanyModal(show) {
            const modal = document.getElementById('modalTambahPerusahaan');
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }
    </script>

@endsection