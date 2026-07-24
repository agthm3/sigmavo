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
                    <a href="#" class="hover:text-vokasi-primary">Verifikasi</a>
                    <span>/</span>
                    <span class="text-gray-800">Laporan Menunggu</span>
                </nav>
            </div>
            <div class="flex items-center space-x-4">
                <button class="text-gray-500 hover:text-vokasi-primary transition-colors relative" title="Notifikasi">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">18</span>
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
                        <h2 class="text-2xl font-bold text-gray-800">Antrean Verifikasi Laporan</h2>
                        <p class="text-sm text-gray-500 mt-1">Daftar laporan akhir mahasiswa yang membutuhkan persetujuan atau tindak lanjut Admin Prodi.</p>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 px-4 py-2 rounded-lg text-yellow-800 font-bold text-sm flex items-center shadow-sm">
                        <i class="fas fa-hourglass-half text-yellow-600 mr-2 animate-pulse"></i> 18 Laporan Menunggu Eksekusi
                    </div>
                </div>

                <!-- CARDS VIEW FOR PENDING VERIFICATIONS (Interactive & Fast Action) -->
                <div class="space-y-4">
                    
                    <!-- Pending Item 1 -->
                    <div class="bg-white rounded-xl shadow-sm border border-yellow-200 overflow-hidden hover:shadow-md transition-shadow">
                        <div class="bg-yellow-50/60 p-4 border-b border-yellow-100 flex flex-wrap justify-between items-center gap-2">
                            <div class="flex items-center gap-2">
                                <span class="bg-yellow-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">
                                    <i class="fas fa-clock mr-1"></i> Menunggu Verifikasi
                                </span>
                                <span class="text-xs font-semibold text-gray-500">Submisi: 22 Juli 2026, 10:45 WITA</span>
                            </div>
                            <span class="text-xs font-bold text-vokasi-primary bg-white px-2.5 py-1 rounded border border-vokasi-light">
                                D4 Teknologi Produksi Pertanian
                            </span>
                        </div>

                        <div class="p-5 flex flex-col lg:flex-row gap-6">
                            <!-- Main Info -->
                            <div class="flex-1 space-y-3">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name=Fadehl+Thristansyah&background=f3f4f6&color=37A7AC" alt="Avatar" class="w-12 h-12 rounded-full border border-gray-200 shrink-0">
                                    <div>
                                        <h3 class="font-bold text-gray-800 text-base">Fadehl Thristansyah <span class="text-xs text-gray-500 font-normal ml-1">(NIM: H071231012)</span></h3>
                                        <p class="text-xs text-gray-600"><i class="fas fa-building mr-1 text-vokasi-primary"></i> PT. SmartPlay Inovasi — <span class="text-gray-500">Pengembang Edukasi STEM</span></p>
                                    </div>
                                </div>

                                <div class="bg-gray-50 p-3.5 rounded-lg border border-gray-200">
                                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">Judul Laporan Magang</p>
                                    <p class="text-sm font-bold text-gray-800 leading-snug">"Pengembangan Alat Peraga Edukasi STEM Berbasis Sensorik untuk Anak Usia Dini pada PT SmartPlay Inovasi"</p>
                                </div>

                                <div class="flex flex-wrap gap-3 items-center">
                                    <span class="text-xs font-medium text-gray-500"><i class="fas fa-paperclip mr-1"></i> Dokumen Terlampir:</span>
                                    <a href="#" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200 px-3 py-1.5 rounded hover:bg-blue-100 transition-colors">
                                        <i class="fas fa-file-pdf text-red-500 text-sm"></i> Laporan_Akhir_Fadehl.pdf (3.2 MB)
                                    </a>
                                    <a href="#" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-700 bg-gray-100 border border-gray-300 px-3 py-1.5 rounded hover:bg-gray-200 transition-colors">
                                        <i class="fas fa-file-alt text-gray-500"></i> Form_Nilai_Supervisor.pdf
                                    </a>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="w-full lg:w-72 shrink-0 flex flex-col justify-between border-t lg:border-t-0 lg:border-l border-gray-100 pt-4 lg:pt-0 lg:pl-6 space-y-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Catatan Verifikasi (Opsional):</label>
                                    <textarea rows="2" placeholder="Catatan untuk mahasiswa jika ada perbaikan..." class="w-full text-xs p-2 bg-gray-50 border border-gray-300 rounded focus:outline-none focus:border-vokasi-primary resize-none"></textarea>
                                </div>
                                <div class="space-y-2">
                                    <button class="w-full bg-vokasi-primary hover:bg-vokasi-dark text-white font-bold text-sm py-2 px-4 rounded-lg transition-colors shadow-sm flex items-center justify-center">
                                        <i class="fas fa-check-circle mr-2"></i> Approve Laporan
                                    </button>
                                    <button class="w-full bg-white hover:bg-red-50 text-red-600 border border-red-200 font-bold text-sm py-2 px-4 rounded-lg transition-colors flex items-center justify-center">
                                        <i class="fas fa-undo mr-2"></i> Minta Revisi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Item 2 -->
                    <div class="bg-white rounded-xl shadow-sm border border-yellow-200 overflow-hidden hover:shadow-md transition-shadow">
                        <div class="bg-yellow-50/60 p-4 border-b border-yellow-100 flex flex-wrap justify-between items-center gap-2">
                            <div class="flex items-center gap-2">
                                <span class="bg-yellow-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">
                                    <i class="fas fa-clock mr-1"></i> Menunggu Verifikasi
                                </span>
                                <span class="text-xs font-semibold text-gray-500">Submisi: 21 Juli 2026, 16:20 WITA</span>
                            </div>
                            <span class="text-xs font-bold text-vokasi-primary bg-white px-2.5 py-1 rounded border border-vokasi-light">
                                D4 Agribisnis
                            </span>
                        </div>

                        <div class="p-5 flex flex-col lg:flex-row gap-6">
                            <div class="flex-1 space-y-3">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name=Nurul+Hidayah&background=f3f4f6&color=37A7AC" alt="Avatar" class="w-12 h-12 rounded-full border border-gray-200 shrink-0">
                                    <div>
                                        <h3 class="font-bold text-gray-800 text-base">Nurul Hidayah <span class="text-xs text-gray-500 font-normal ml-1">(NIM: H071231033)</span></h3>
                                        <p class="text-xs text-gray-600"><i class="fas fa-building mr-1 text-vokasi-primary"></i> PT. Agro Nusantara — <span class="text-gray-500">Staf Pemasaran Hasil Tani</span></p>
                                    </div>
                                </div>

                                <div class="bg-gray-50 p-3.5 rounded-lg border border-gray-200">
                                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">Judul Laporan Magang</p>
                                    <p class="text-sm font-bold text-gray-800 leading-snug">"Analisis Rantai Pasok dan Strategi Pemasaran Produk Agrikultur Unggulan pada PT Agro Nusantara Kabupaten Maros"</p>
                                </div>

                                <div class="flex flex-wrap gap-3 items-center">
                                    <span class="text-xs font-medium text-gray-500"><i class="fas fa-paperclip mr-1"></i> Dokumen Terlampir:</span>
                                    <a href="#" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200 px-3 py-1.5 rounded hover:bg-blue-100 transition-colors">
                                        <i class="fas fa-file-pdf text-red-500 text-sm"></i> Laporan_Magang_Nurul.pdf (4.1 MB)
                                    </a>
                                </div>
                            </div>

                            <div class="w-full lg:w-72 shrink-0 flex flex-col justify-between border-t lg:border-t-0 lg:border-l border-gray-100 pt-4 lg:pt-0 lg:pl-6 space-y-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Catatan Verifikasi (Opsional):</label>
                                    <textarea rows="2" placeholder="Catatan untuk mahasiswa jika ada perbaikan..." class="w-full text-xs p-2 bg-gray-50 border border-gray-300 rounded focus:outline-none focus:border-vokasi-primary resize-none"></textarea>
                                </div>
                                <div class="space-y-2">
                                    <button class="w-full bg-vokasi-primary hover:bg-vokasi-dark text-white font-bold text-sm py-2 px-4 rounded-lg transition-colors shadow-sm flex items-center justify-center">
                                        <i class="fas fa-check-circle mr-2"></i> Approve Laporan
                                    </button>
                                    <button class="w-full bg-white hover:bg-red-50 text-red-600 border border-red-200 font-bold text-sm py-2 px-4 rounded-lg transition-colors flex items-center justify-center">
                                        <i class="fas fa-undo mr-2"></i> Minta Revisi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Pagination Controls -->
                <div class="p-4 border border-gray-200 rounded-xl flex items-center justify-between bg-white text-sm shadow-sm">
                    <span class="text-gray-500">Menampilkan 1 hingga 2 dari 18 laporan antrean</span>
                    <div class="flex space-x-1">
                        <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-400 bg-gray-50 cursor-not-allowed">Sebelummya</button>
                        <button class="px-3 py-1 border border-vokasi-primary rounded-lg text-white bg-vokasi-primary font-medium">1</button>
                        <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">2</button>
                        <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">Selanjutnya</button>
                    </div>
                </div>

            </div>

            <!-- FOOTER -->
            <footer class="mt-8 py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
                Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
            </footer>

        </main>
    </div>

@endsection