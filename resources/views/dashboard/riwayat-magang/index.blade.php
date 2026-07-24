@extends('layouts.dashboard')
@section('content')
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col">
            <div class="flex-1">
                
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">Riwayat Program Magang</h3>
                        <p class="text-sm text-gray-500 mt-1">Pantau status pengajuan dan histori magang yang pernah Anda ikuti.</p>
                    </div>
                    <!-- Filter / Sort (Optional) -->
                    <div class="flex items-center space-x-2">
                        <select class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary focus:border-vokasi-primary block p-2 outline-none">
                            <option>Semua Status</option>
                            <option>Sedang Berjalan</option>
                            <option>Selesai</option>
                            <option>Menunggu Diterima</option>
                            <option>Ditolak</option>
                        </select>
                    </div>
                </div>

                <!-- CARD GRID -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    <!-- Card 1: Sedang Berjalan / Aktif -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow flex flex-col">
                        <div class="p-5 flex-1 border-b border-gray-100">
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-12 h-12 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-500 shrink-0">
                                    <i class="fas fa-cogs text-xl"></i>
                                </div>
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">
                                    Sedang Berjalan
                                </span>
                            </div>
                            <h4 class="font-bold text-lg text-gray-800 mb-1 leading-tight">Pengembang Edukasi STEM</h4>
                            <p class="text-vokasi-primary font-semibold text-sm mb-3">SmartPlay</p>
                            
                            <div class="space-y-2 mt-4 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt w-5 text-gray-400"></i>
                                    <span>Jul 2026 - Des 2026</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt w-5 text-gray-400"></i>
                                    <span>Makassar, Sulawesi Selatan</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-clock w-5 text-gray-400"></i>
                                    <span>Progres Jam: <strong class="text-gray-800">120 / 900 Jam</strong></span>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 flex justify-between items-center">
                            <button class="text-vokasi-primary hover:text-vokasi-dark text-sm font-semibold transition-colors">
                                Lihat Logbook <i class="fas fa-arrow-right ml-1 text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Card 2: Selesai (Riwayat 1) -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow flex flex-col">
                        <div class="p-5 flex-1 border-b border-gray-100">
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-12 h-12 rounded-lg bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-600 shrink-0">
                                    <i class="fas fa-building text-xl"></i>
                                </div>
                                <span class="bg-gray-100 text-gray-600 text-xs font-bold px-3 py-1 rounded-full">
                                    Selesai
                                </span>
                            </div>
                            <h4 class="font-bold text-lg text-gray-800 mb-1 leading-tight">Staf Evaluasi & Inovasi</h4>
                            <p class="text-gray-600 font-semibold text-sm mb-3">BRIDA Kota Makassar</p>
                            
                            <div class="space-y-2 mt-4 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt w-5 text-gray-400"></i>
                                    <span>Jan 2026 - Apr 2026</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt w-5 text-gray-400"></i>
                                    <span>Makassar (Instansi Pemerintah)</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle w-5 text-green-500"></i>
                                    <span>Laporan Disetujui</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 flex justify-between items-center gap-2">
                            <button class="flex-1 bg-white border border-gray-200 hover:bg-gray-100 text-gray-700 text-sm font-semibold py-2 rounded-lg transition-colors">
                                <i class="fas fa-file-alt mr-1"></i> Detail
                            </button>
                            <button class="flex-1 bg-vokasi-primary hover:bg-vokasi-dark text-white text-sm font-semibold py-2 rounded-lg transition-colors">
                                <i class="fas fa-download mr-1"></i> Sertifikat
                            </button>
                        </div>
                    </div>

                    <!-- Card 3: Selesai (Riwayat 2) -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow flex flex-col">
                        <div class="p-5 flex-1 border-b border-gray-100">
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-12 h-12 rounded-lg bg-orange-50 border border-orange-100 flex items-center justify-center text-orange-500 shrink-0">
                                    <i class="fas fa-globe-asia text-xl"></i>
                                </div>
                                <span class="bg-gray-100 text-gray-600 text-xs font-bold px-3 py-1 rounded-full">
                                    Selesai
                                </span>
                            </div>
                            <h4 class="font-bold text-lg text-gray-800 mb-1 leading-tight">Volunteer Divisi Keilmuan</h4>
                            <p class="text-gray-600 font-semibold text-sm mb-3">SM-IAGI UPN</p>
                            
                            <div class="space-y-2 mt-4 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt w-5 text-gray-400"></i>
                                    <span>Feb 2026 - Jun 2026</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt w-5 text-gray-400"></i>
                                    <span>Organisasi Geologi</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle w-5 text-green-500"></i>
                                    <span>Penilaian Akhir: 92/100</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 flex justify-between items-center gap-2">
                            <button class="flex-1 bg-white border border-gray-200 hover:bg-gray-100 text-gray-700 text-sm font-semibold py-2 rounded-lg transition-colors">
                                <i class="fas fa-file-alt mr-1"></i> Detail
                            </button>
                            <button class="flex-1 bg-vokasi-primary hover:bg-vokasi-dark text-white text-sm font-semibold py-2 rounded-lg transition-colors">
                                <i class="fas fa-download mr-1"></i> Sertifikat
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <!-- FOOTER -->
            <footer class="mt-8 py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
                Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
            </footer>
        </main>
@endsection