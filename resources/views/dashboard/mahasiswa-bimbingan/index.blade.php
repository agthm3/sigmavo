@extends('layouts.dashboard')

@section('content')

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative">
            
            <div class="max-w-7xl mx-auto w-full flex-1">
                
                <!-- HEADER PAGE -->
                <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Mahasiswa Bimbingan</h2>
                        <p class="text-sm text-gray-500 mt-1">Pantau progres dan kelola persetujuan laporan harian mahasiswa magang Anda.</p>
                    </div>
                    
                    <!-- Quick Stats Badges -->
                    <div class="flex gap-3">
                        <div class="bg-white border border-gray-200 px-4 py-2 rounded-lg shadow-sm flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center">
                                <i class="fas fa-users text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Total Bimbingan</p>
                                <p class="text-lg font-bold text-gray-800 leading-none">8</p>
                            </div>
                        </div>
                        <div class="bg-white border border-gray-200 px-4 py-2 rounded-lg shadow-sm flex items-center gap-3 cursor-pointer hover:border-orange-300 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center">
                                <i class="fas fa-exclamation-circle text-sm animate-pulse"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Perlu Asistensi</p>
                                <p class="text-lg font-bold text-orange-600 leading-none">3</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TOOLBAR (Search & Filter) -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6 flex flex-col md:flex-row gap-4 justify-between items-center">
                    <div class="relative w-full md:w-96">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        <input type="text" placeholder="Cari nama mahasiswa atau NIM..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary text-sm">
                    </div>
                    <div class="flex gap-2 w-full md:w-auto">
                        <select class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 w-full md:w-auto">
                            <option>Semua Status Laporan</option>
                            <option>Ada Logbook Pending</option>
                            <option>Up to Date</option>
                            <option>Perlu Revisi</option>
                        </select>
                        <select class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 w-full md:w-auto">
                            <option>Periode: 2026</option>
                            <option>Periode: 2025</option>
                        </select>
                    </div>
                </div>

                <!-- LISTING MAHASISWA (Rich Cards) -->
                <div class="space-y-4">
                    
                    <!-- Mahasiswa Card 1 -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:border-vokasi-primary transition-colors flex flex-col lg:flex-row gap-6 items-start lg:items-center">
                        
                        <!-- Profile & Info Utama -->
                        <div class="flex items-start gap-4 flex-1">
                            <div class="relative">
                                <img src="https://ui-avatars.com/api/?name=Fadehl+Thristansyah&background=f3f4f6&color=37A7AC" alt="Mahasiswa" class="w-14 h-14 rounded-full border border-gray-200 shadow-sm">
                                <span class="absolute bottom-0 right-0 w-4 h-4 bg-green-500 border-2 border-white rounded-full" title="Sedang Aktif Magang"></span>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-800 leading-tight"><a href="#" class="hover:text-vokasi-primary">Fadehl Thristansyah</a></h3>
                                <p class="text-sm font-medium text-gray-500 mb-1">NIM: H071231012</p>
                                <div class="flex items-center text-xs text-gray-600 mt-2">
                                    <i class="fas fa-building text-vokasi-primary mr-1.5"></i> 
                                    <span class="font-semibold mr-3">PT. SmartPlay Inovasi</span>
                                    <i class="fas fa-map-marker-alt text-gray-400 mr-1.5"></i> Makassar
                                </div>
                            </div>
                        </div>

                        <!-- Progress Section -->
                        <div class="w-full lg:w-64 shrink-0">
                            <div class="flex justify-between items-end mb-1.5">
                                <span class="text-xs font-semibold text-gray-600">Progres 900 Jam</span>
                                <span class="text-xs font-bold text-vokasi-primary">140 Jam <span class="text-gray-400 font-normal">(15%)</span></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-vokasi-primary h-2 rounded-full" style="width: 15%"></div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1 text-right">Sisa: 760 Jam</p>
                        </div>

                        <!-- Status Alert & Actions -->
                        <div class="flex flex-col sm:flex-row items-center gap-4 w-full lg:w-auto border-t lg:border-t-0 lg:border-l border-gray-100 pt-4 lg:pt-0 lg:pl-6 shrink-0">
                            <!-- Status Indicator -->
                            <div class="flex items-center justify-center bg-orange-50 border border-orange-100 rounded-lg px-3 py-2 w-full sm:w-auto">
                                <i class="fas fa-file-signature text-orange-500 mr-2"></i>
                                <div>
                                    <p class="text-[10px] text-orange-800 font-semibold uppercase tracking-wider">Status Laporan</p>
                                    <p class="text-xs text-orange-600 font-bold">2 Menunggu Approval</p>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="flex gap-2 w-full sm:w-auto">
                                <button class="flex-1 sm:flex-none bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:text-vokasi-primary font-semibold text-sm py-2 px-4 rounded-lg transition-colors shadow-sm" title="Profil Lengkap">
                                    <i class="fas fa-user"></i>
                                </button>
                                <button class="flex-1 sm:flex-none bg-vokasi-primary hover:bg-vokasi-dark text-white font-semibold text-sm py-2 px-5 rounded-lg transition-colors shadow-sm flex items-center justify-center">
                                    Asistensi Logbook
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Mahasiswa Card 2 -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:border-vokasi-primary transition-colors flex flex-col lg:flex-row gap-6 items-start lg:items-center">
                        <div class="flex items-start gap-4 flex-1">
                            <div class="relative">
                                <img src="https://ui-avatars.com/api/?name=Andi+Reza&background=f3f4f6&color=6b7280" alt="Mahasiswa" class="w-14 h-14 rounded-full border border-gray-200 shadow-sm">
                                <span class="absolute bottom-0 right-0 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></span>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-800 leading-tight"><a href="#" class="hover:text-vokasi-primary">Andi Reza Syahputra</a></h3>
                                <p class="text-sm font-medium text-gray-500 mb-1">NIM: H071231045</p>
                                <div class="flex items-center text-xs text-gray-600 mt-2">
                                    <i class="fas fa-building text-vokasi-primary mr-1.5"></i> 
                                    <span class="font-semibold mr-3">BRIDA Kota Makassar</span>
                                    <i class="fas fa-map-marker-alt text-gray-400 mr-1.5"></i> Makassar
                                </div>
                            </div>
                        </div>

                        <div class="w-full lg:w-64 shrink-0">
                            <div class="flex justify-between items-end mb-1.5">
                                <span class="text-xs font-semibold text-gray-600">Progres 900 Jam</span>
                                <span class="text-xs font-bold text-vokasi-primary">320 Jam <span class="text-gray-400 font-normal">(35%)</span></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-vokasi-primary h-2 rounded-full" style="width: 35%"></div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1 text-right">Sisa: 580 Jam</p>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-4 w-full lg:w-auto border-t lg:border-t-0 lg:border-l border-gray-100 pt-4 lg:pt-0 lg:pl-6 shrink-0">
                            <div class="flex items-center justify-center bg-green-50 border border-green-100 rounded-lg px-3 py-2 w-full sm:w-auto">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                <div>
                                    <p class="text-[10px] text-green-800 font-semibold uppercase tracking-wider">Status Laporan</p>
                                    <p class="text-xs text-green-600 font-bold">Up to Date (Aman)</p>
                                </div>
                            </div>
                            <div class="flex gap-2 w-full sm:w-auto">
                                <button class="flex-1 sm:flex-none bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:text-vokasi-primary font-semibold text-sm py-2 px-4 rounded-lg transition-colors shadow-sm" title="Profil Lengkap">
                                    <i class="fas fa-user"></i>
                                </button>
                                <button class="flex-1 sm:flex-none bg-white border border-vokasi-primary text-vokasi-primary hover:bg-vokasi-primary hover:text-white font-semibold text-sm py-2 px-5 rounded-lg transition-colors shadow-sm flex items-center justify-center">
                                    Lihat Riwayat
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Mahasiswa Card 3 (Warning Issue) -->
                    <div class="bg-white rounded-xl shadow-sm border border-red-200 p-5 relative overflow-hidden flex flex-col lg:flex-row gap-6 items-start lg:items-center">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-red-500"></div>
                        
                        <div class="flex items-start gap-4 flex-1 ml-2">
                            <div class="relative">
                                <img src="https://ui-avatars.com/api/?name=Siti+Nurhaliza&background=f3f4f6&color=6b7280" alt="Mahasiswa" class="w-14 h-14 rounded-full border border-gray-200 shadow-sm">
                                <span class="absolute bottom-0 right-0 w-4 h-4 bg-yellow-400 border-2 border-white rounded-full" title="Izin/Sakit"></span>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-800 leading-tight"><a href="#" class="hover:text-vokasi-primary">Siti Nurhaliza</a></h3>
                                <p class="text-sm font-medium text-gray-500 mb-1">NIM: H071231088</p>
                                <div class="flex items-center text-xs text-gray-600 mt-2">
                                    <i class="fas fa-building text-vokasi-primary mr-1.5"></i> 
                                    <span class="font-semibold mr-3">PT. Agro Nusantara</span>
                                    <i class="fas fa-map-marker-alt text-gray-400 mr-1.5"></i> Maros
                                </div>
                            </div>
                        </div>

                        <div class="w-full lg:w-64 shrink-0">
                            <div class="flex justify-between items-end mb-1.5">
                                <span class="text-xs font-semibold text-gray-600">Progres 900 Jam</span>
                                <span class="text-xs font-bold text-vokasi-primary">80 Jam <span class="text-gray-400 font-normal">(8%)</span></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-vokasi-primary h-2 rounded-full" style="width: 8%"></div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1 text-right">Sisa: 820 Jam</p>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-4 w-full lg:w-auto border-t lg:border-t-0 lg:border-l border-gray-100 pt-4 lg:pt-0 lg:pl-6 shrink-0">
                            <div class="flex items-center justify-center bg-red-50 border border-red-100 rounded-lg px-3 py-2 w-full sm:w-auto">
                                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                                <div>
                                    <p class="text-[10px] text-red-800 font-semibold uppercase tracking-wider">Perhatian Khusus</p>
                                    <p class="text-xs text-red-600 font-bold">1 Pengajuan Sakit</p>
                                </div>
                            </div>
                            <div class="flex gap-2 w-full sm:w-auto">
                                <button class="flex-1 sm:flex-none bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:text-vokasi-primary font-semibold text-sm py-2 px-4 rounded-lg transition-colors shadow-sm" title="Profil Lengkap">
                                    <i class="fas fa-user"></i>
                                </button>
                                <button class="flex-1 sm:flex-none bg-red-600 hover:bg-red-700 text-white font-semibold text-sm py-2 px-5 rounded-lg transition-colors shadow-sm flex items-center justify-center">
                                    Tinjau Surat
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Pagination (Optional) -->
                <div class="mt-6 flex justify-center">
                    <button class="bg-white border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-semibold py-2 px-6 rounded-lg transition-colors shadow-sm">
                        Muat Lebih Banyak
                    </button>
                </div>

            </div>

            <!-- FOOTER -->
            <footer class="mt-auto py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
                Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
            </footer>

        </main>
@endsection