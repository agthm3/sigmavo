@extends('layouts.dashboard')

@section('content')
    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50">
        
        <!-- TOPBAR -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 lg:px-6 shrink-0 shadow-sm">
            <div class="flex items-center">
                <button class="md:hidden text-gray-500 hover:text-vokasi-primary focus:outline-none p-2 rounded-md transition-colors">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h2 class="hidden md:block text-xl font-bold text-gray-800 ml-4">Eksplor Lowongan Magang</h2>
            </div>
            <div class="flex items-center space-x-4">
                <button class="text-gray-500 hover:text-vokasi-primary transition-colors relative" title="Notifikasi">
                    <i class="fas fa-bell text-lg"></i>
                </button>
                <div class="flex items-center space-x-2 cursor-pointer border-l pl-4 border-gray-200">
                    <img src="https://ui-avatars.com/api/?name=Fadehl+Thristansyah&background=37A7AC&color=fff" alt="Profile" class="w-8 h-8 rounded-full border border-gray-200 shadow-sm">
                    <div class="hidden md:block text-sm text-right">
                        <p class="font-bold text-gray-700 leading-tight">Fadehl Thristansyah</p>
                        <p class="text-xs text-gray-500">Mahasiswa</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 flex flex-col relative">
            
            <!-- Hero Search Section -->
            <div class="bg-vokasi-dark px-4 py-8 lg:px-8 shadow-inner relative overflow-hidden">
                <!-- Background Pattern (Optional) -->
                <div class="absolute inset-0 opacity-10 pointer-events-none">
                    <i class="fas fa-briefcase absolute text-9xl -right-10 -bottom-10"></i>
                </div>
                
                <div class="relative z-10 max-w-4xl mx-auto">
                    <h3 class="text-2xl font-bold text-white mb-2 text-center md:text-left">Temukan Tempat Magang Terbaikmu</h3>
                    <p class="text-vokasi-light mb-6 text-sm text-center md:text-left">Terdapat lebih dari 40+ lowongan magang yang tersedia khusus untuk prodimu.</p>
                    
                    <!-- Search Bar -->
                    <div class="flex flex-col md:flex-row gap-2 bg-white p-2 rounded-xl shadow-lg">
                        <div class="flex-1 flex items-center bg-gray-50 rounded-lg border border-gray-200 px-3 py-2">
                            <i class="fas fa-search text-gray-400 mr-2"></i>
                            <input type="text" placeholder="Cari posisi, perusahaan, atau kata kunci..." class="bg-transparent border-none outline-none w-full text-sm text-gray-700">
                        </div>
                        <div class="flex-1 flex items-center bg-gray-50 rounded-lg border border-gray-200 px-3 py-2">
                            <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                            <select class="bg-transparent border-none outline-none w-full text-sm text-gray-700 cursor-pointer">
                                <option value="">Semua Lokasi</option>
                                <option value="Makassar">Makassar</option>
                                <option value="Gowa">Gowa</option>
                                <option value="Maros">Maros</option>
                                <option value="Luar Sulsel">Luar Sulawesi Selatan</option>
                            </select>
                        </div>
                        <button class="bg-vokasi-primary hover:bg-vokasi-dark text-white font-bold py-3 md:py-2 px-6 rounded-lg transition-colors w-full md:w-auto">
                            Cari
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex-1 p-4 lg:p-6 max-w-7xl mx-auto w-full">
                
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-bold text-gray-800 text-lg">Rekomendasi Untukmu</h4>
                    <span class="text-sm text-gray-500 font-medium">Menampilkan 12 lowongan</span>
                </div>

                <!-- JOB CARDS GRID -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                    
                    <!-- Job Card 1 -->
                    <div class="job-card bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 flex flex-col h-full cursor-pointer hover:border-vokasi-primary">
                        <div class="p-5 flex-1">
                            <div class="flex justify-between items-start mb-4">
                                <!-- Logo Perusahaan -->
                                <div class="w-12 h-12 rounded bg-gray-50 border border-gray-200 flex items-center justify-center p-1">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2f/Google_2015_logo.svg/1200px-Google_2015_logo.svg.png" alt="Company Logo" class="object-contain w-full h-full opacity-80">
                                </div>
                                <button class="text-gray-300 hover:text-red-500 transition-colors" title="Simpan Lowongan">
                                    <i class="far fa-bookmark text-lg"></i>
                                </button>
                            </div>
                            
                            <h4 class="font-bold text-lg text-gray-800 leading-tight mb-1">Frontend Web Developer</h4>
                            <p class="text-vokasi-primary font-medium text-sm mb-3">Google Indonesia (Partner)</p>
                            
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-2 py-1 rounded">WFO / On-site</span>
                                <span class="bg-green-50 text-green-600 text-[10px] font-bold px-2 py-1 rounded">Paid Internship</span>
                            </div>
                            
                            <ul class="text-sm text-gray-500 space-y-1.5">
                                <li class="flex items-center"><i class="fas fa-map-marker-alt w-5 text-gray-400"></i> Makassar, ID</li>
                                <li class="flex items-center"><i class="fas fa-clock w-5 text-gray-400"></i> 6 Bulan (Agustus - Jan)</li>
                                <li class="flex items-center"><i class="fas fa-users w-5 text-gray-400"></i> Kuota: 3 Orang (2 tersisa)</li>
                            </ul>
                        </div>
                        <div class="p-4 bg-gray-50 border-t border-gray-100 mt-auto flex items-center justify-between">
                            <span class="text-xs text-gray-400">Diunggah 2 hari lalu</span>
                            <button class="text-vokasi-primary hover:text-vokasi-dark font-semibold text-sm transition-colors">
                                Lihat Detail <i class="fas fa-chevron-right ml-1 text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Job Card 2 -->
                    <div class="job-card bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 flex flex-col h-full cursor-pointer hover:border-vokasi-primary">
                        <div class="p-5 flex-1">
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-12 h-12 rounded bg-red-50 border border-red-100 flex items-center justify-center p-1 text-red-500">
                                    <i class="fas fa-chart-bar text-2xl"></i>
                                </div>
                                <button class="text-vokasi-primary transition-colors" title="Simpan Lowongan">
                                    <i class="fas fa-bookmark text-lg"></i>
                                </button>
                            </div>
                            
                            <h4 class="font-bold text-lg text-gray-800 leading-tight mb-1">Staf Analis Data</h4>
                            <p class="text-vokasi-primary font-medium text-sm mb-3">BRIDA Kota Makassar</p>
                            
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-2 py-1 rounded">WFO / On-site</span>
                                <span class="bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-1 rounded">Instansi Pemerintah</span>
                            </div>
                            
                            <ul class="text-sm text-gray-500 space-y-1.5">
                                <li class="flex items-center"><i class="fas fa-map-marker-alt w-5 text-gray-400"></i> Kantor Walkot Makassar</li>
                                <li class="flex items-center"><i class="fas fa-clock w-5 text-gray-400"></i> 4 Bulan (Sep - Des)</li>
                                <li class="flex items-center"><i class="fas fa-users w-5 text-gray-400"></i> Kuota: 5 Orang (Penuh)</li>
                            </ul>
                        </div>
                        <div class="p-4 bg-gray-50 border-t border-gray-100 mt-auto flex items-center justify-between">
                            <span class="text-xs text-red-500 font-medium">Kuota Terpenuhi</span>
                            <button class="text-gray-400 font-semibold text-sm transition-colors cursor-not-allowed">
                                Lihat Detail
                            </button>
                        </div>
                    </div>

                    <!-- Job Card 3 -->
                    <div class="job-card bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 flex flex-col h-full cursor-pointer hover:border-vokasi-primary">
                        <div class="p-5 flex-1">
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-12 h-12 rounded bg-purple-50 border border-purple-100 flex items-center justify-center p-1 text-purple-600">
                                    <i class="fas fa-shapes text-2xl"></i>
                                </div>
                                <button class="text-gray-300 hover:text-red-500 transition-colors" title="Simpan Lowongan">
                                    <i class="far fa-bookmark text-lg"></i>
                                </button>
                            </div>
                            
                            <h4 class="font-bold text-lg text-gray-800 leading-tight mb-1">STEM Product Designer</h4>
                            <p class="text-vokasi-primary font-medium text-sm mb-3">PT. SmartPlay Inovasi</p>
                            
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="bg-yellow-50 text-yellow-600 text-[10px] font-bold px-2 py-1 rounded">Hybrid</span>
                                <span class="bg-purple-50 text-purple-600 text-[10px] font-bold px-2 py-1 rounded">Creative</span>
                            </div>
                            
                            <ul class="text-sm text-gray-500 space-y-1.5">
                                <li class="flex items-center"><i class="fas fa-map-marker-alt w-5 text-gray-400"></i> Tamalanrea, Makassar</li>
                                <li class="flex items-center"><i class="fas fa-clock w-5 text-gray-400"></i> 6 Bulan (Jul - Des)</li>
                                <li class="flex items-center"><i class="fas fa-users w-5 text-gray-400"></i> Kuota: 2 Orang (1 tersisa)</li>
                            </ul>
                        </div>
                        <div class="p-4 bg-gray-50 border-t border-gray-100 mt-auto flex items-center justify-between">
                            <span class="text-xs text-gray-400">Diunggah hari ini</span>
                            <button class="text-vokasi-primary hover:text-vokasi-dark font-semibold text-sm transition-colors">
                                Lihat Detail <i class="fas fa-chevron-right ml-1 text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Job Card 4 -->
                    <div class="job-card bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 flex flex-col h-full cursor-pointer hover:border-vokasi-primary">
                        <div class="p-5 flex-1">
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-12 h-12 rounded bg-orange-50 border border-orange-100 flex items-center justify-center p-1 text-orange-500">
                                    <i class="fas fa-tractor text-2xl"></i>
                                </div>
                                <button class="text-gray-300 hover:text-red-500 transition-colors" title="Simpan Lowongan">
                                    <i class="far fa-bookmark text-lg"></i>
                                </button>
                            </div>
                            
                            <h4 class="font-bold text-lg text-gray-800 leading-tight mb-1">Quality Control Pertanian</h4>
                            <p class="text-vokasi-primary font-medium text-sm mb-3">PT. Agro Nusantara</p>
                            
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-2 py-1 rounded">On-site</span>
                                <span class="bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-1 rounded">Akomodasi Disediakan</span>
                            </div>
                            
                            <ul class="text-sm text-gray-500 space-y-1.5">
                                <li class="flex items-center"><i class="fas fa-map-marker-alt w-5 text-gray-400"></i> Maros, Sulsel</li>
                                <li class="flex items-center"><i class="fas fa-clock w-5 text-gray-400"></i> 6 Bulan (Agustus - Jan)</li>
                                <li class="flex items-center"><i class="fas fa-users w-5 text-gray-400"></i> Kuota: 10 Orang (8 tersisa)</li>
                            </ul>
                        </div>
                        <div class="p-4 bg-gray-50 border-t border-gray-100 mt-auto flex items-center justify-between">
                            <span class="text-xs text-gray-400">Diunggah 1 minggu lalu</span>
                            <button class="text-vokasi-primary hover:text-vokasi-dark font-semibold text-sm transition-colors">
                                Lihat Detail <i class="fas fa-chevron-right ml-1 text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="flex justify-center mb-8">
                    <nav class="flex items-center space-x-2">
                        <button class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 disabled:opacity-50" disabled>
                            <i class="fas fa-chevron-left text-sm"></i>
                        </button>
                        <button class="w-10 h-10 flex items-center justify-center rounded-lg bg-vokasi-primary text-white font-medium shadow-sm">1</button>
                        <button class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 font-medium transition-colors">2</button>
                        <button class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 font-medium transition-colors">3</button>
                        <span class="text-gray-500">...</span>
                        <button class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-chevron-right text-sm"></i>
                        </button>
                    </nav>
                </div>

            </div>

            <!-- FOOTER -->
            <footer class="mt-auto py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
                Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
            </footer>

        </main>
    </div>
@endsection