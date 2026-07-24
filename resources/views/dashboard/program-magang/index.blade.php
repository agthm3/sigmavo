@extends('layouts.dashboard')

@section('content')
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col">
            <div class="flex-1">
                
                <!-- PROGRESS & STATUS CARD -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <div class="flex flex-col md:flex-row justify-between md:items-center mb-6 gap-4">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">Magang Industri Vokasi 2026</h3>
                            <div class="flex items-center mt-2 space-x-3">
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full flex items-center">
                                    <span class="w-2 h-2 rounded-full bg-green-500 mr-2 animate-pulse"></span> Sedang Berjalan
                                </span>
                                <span class="text-sm text-gray-500"><i class="fas fa-calendar-alt mr-1"></i> 1 Juli 2026 - 31 Des 2026</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button class="bg-vokasi-primary hover:bg-vokasi-dark text-white text-sm font-semibold py-2 px-4 rounded-lg transition-colors shadow-sm flex items-center">
                                <i class="fas fa-camera mr-2"></i> Absen Hari Ini
                            </button>
                            <button class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-semibold py-2 px-4 rounded-lg transition-colors flex items-center">
                                <i class="fas fa-pen mr-2"></i> Isi Logbook
                            </button>
                        </div>
                    </div>

                    <!-- 900 Hours Progress Bar -->
                    <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                        <div class="flex justify-between items-end mb-2">
                            <div>
                                <p class="text-sm font-semibold text-gray-600">Progres Pemenuhan Jam Magang</p>
                                <p class="text-xs text-gray-500 mt-1">Sisa jam akan berkurang otomatis saat absen di-approve Dosen.</p>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-bold text-vokasi-primary">120</span>
                                <span class="text-gray-500"> / 900 Jam</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 mb-2 overflow-hidden">
                            <!-- Width = (120/900) * 100 = 13.3% -->
                            <div class="bg-vokasi-primary h-3 rounded-full transition-all duration-500" style="width: 13.3%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 font-medium">
                            <span>0 Jam</span>
                            <span class="text-orange-500"><i class="fas fa-info-circle mr-1"></i>Sisa: 780 Jam</span>
                            <span>900 Jam</span>
                        </div>
                    </div>
                </div>

                <!-- DETAIL CARDS GRID -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    
                    <!-- Informasi Perusahaan -->
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2 flex items-center">
                            <i class="fas fa-building text-vokasi-primary mr-2"></i> Informasi Penempatan
                        </h4>
                        
                        <div class="flex flex-col md:flex-row gap-6">
                            <div class="w-full md:w-1/3">
                                <div class="aspect-square bg-gray-50 rounded-lg border border-gray-200 flex items-center justify-center p-4">
                                    <!-- Placeholder for Company Logo -->
                                    <div class="text-center">
                                        <i class="fas fa-robot text-4xl text-vokasi-primary mb-2"></i>
                                        <p class="font-bold text-gray-700">SmartPlay Edu</p>
                                    </div>
                                </div>
                            </div>
                            <div class="w-full md:w-2/3 space-y-4">
                                <div>
                                    <p class="text-sm text-gray-500">Nama Instansi/Perusahaan</p>
                                    <p class="font-bold text-gray-800 text-lg">PT. SmartPlay Inovasi Edukasi</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Posisi/Jabatan</p>
                                    <p class="font-medium text-gray-800">Pengembang Edukasi STEM (Intern)</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Lokasi Penempatan</p>
                                    <p class="font-medium text-gray-800">Jl. Perintis Kemerdekaan, Makassar, Sulawesi Selatan</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Deskripsi Tugas</p>
                                    <p class="text-sm text-gray-700 mt-1 leading-relaxed">Merancang alat peraga edukasi berbasis STEM untuk anak-anak, menguji prototipe mainan, dan membantu penyusunan panduan perakitan untuk produksi massal.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Pembimbing -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2 flex items-center">
                            <i class="fas fa-users text-vokasi-primary mr-2"></i> Pihak Terkait
                        </h4>
                        
                        <div class="flex-1 space-y-6">
                            <!-- Dosen Pendamping -->
                            <div>
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 block">Dosen Pendamping (Fakultas)</span>
                                <div class="flex items-center">
                                    <img src="https://ui-avatars.com/api/?name=Dosen+Pendamping&background=f3f4f6&color=37A7AC" alt="Dosen" class="w-10 h-10 rounded-full mr-3 border border-gray-200">
                                    <div>
                                        <p class="font-bold text-gray-800 text-sm">Dr. Ir. Budi Santoso, M.T.</p>
                                        <p class="text-xs text-gray-500">NIP. 198001012005011001</p>
                                    </div>
                                </div>
                                <a href="#" class="mt-2 inline-flex items-center text-xs text-vokasi-primary hover:text-vokasi-dark font-medium">
                                    <i class="fab fa-whatsapp mr-1"></i> Hubungi Dosen
                                </a>
                            </div>

                            <hr class="border-gray-100">

                            <!-- Supervisor -->
                            <div>
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 block">Supervisor (Perusahaan)</span>
                                <div class="flex items-center">
                                    <img src="https://ui-avatars.com/api/?name=Supervisor+Lapangan&background=f3f4f6&color=6b7280" alt="Supervisor" class="w-10 h-10 rounded-full mr-3 border border-gray-200">
                                    <div>
                                        <p class="font-bold text-gray-800 text-sm">Andi Setiawan, S.T.</p>
                                        <p class="text-xs text-gray-500">Head of Product - SmartPlay</p>
                                    </div>
                                </div>
                                <a href="#" class="mt-2 inline-flex items-center text-xs text-gray-500 hover:text-gray-800 font-medium">
                                    <i class="far fa-envelope mr-1"></i> Kirim Email
                                </a>
                            </div>
                        </div>
                        
                        <!-- Quick Action Ajukan Izin -->
                        <div class="mt-6 pt-4 border-t border-gray-100">
                            <button class="w-full bg-red-50 hover:bg-red-100 text-red-600 text-sm font-semibold py-2 rounded-lg transition-colors border border-red-100 flex items-center justify-center">
                                <i class="fas fa-notes-medical mr-2"></i> Ajukan Izin / Sakit
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <!-- FOOTER -->
            <footer class="mt-auto py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
                Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
            </footer>

        </main>

@endsection