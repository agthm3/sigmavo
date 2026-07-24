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
                    <a href="#" class="hover:text-vokasi-primary">Pelaksanaan Magang</a>
                    <span>/</span>
                    <span class="text-gray-800">Pembekalan</span>
                </nav>
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
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative">
            
            <div class="max-w-5xl mx-auto w-full flex-1">
                
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Pembekalan Magang</h2>
                    <p class="text-sm text-gray-500 mt-1">Informasi jadwal kegiatan pembekalan dan materi yang wajib dipelajari sebelum memulai magang.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    
                    <!-- LEFT COLUMN (Jadwal & Status) -->
                    <div class="lg:col-span-2 space-y-6">
                        
                        <!-- Status Kehadiran Banner -->
                        <div class="bg-gradient-to-r from-green-500 to-vokasi-primary rounded-xl shadow-sm p-6 text-white flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center shrink-0">
                                    <i class="fas fa-check-circle text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg">Status: Telah Mengikuti Pembekalan</h3>
                                    <p class="text-sm text-green-50 opacity-90">Anda telah memenuhi syarat untuk memulai program magang.</p>
                                </div>
                            </div>
                            <button class="bg-white text-vokasi-dark font-bold text-sm py-2 px-4 rounded-lg shadow-sm hover:bg-gray-50 transition-colors w-full sm:w-auto text-center">
                                Lihat Sertifikat
                            </button>
                        </div>

                        <!-- Jadwal Pembekalan Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="border-b border-gray-100 p-5 bg-gray-50/50 flex justify-between items-center">
                                <h3 class="font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-calendar-alt text-vokasi-primary mr-2"></i> Jadwal Pembekalan Vokasi 2026
                                </h3>
                                <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2.5 py-1 rounded-full border border-gray-200">Selesai</span>
                            </div>
                            <div class="p-6">
                                <div class="flex flex-col md:flex-row gap-6">
                                    <div class="flex-1 space-y-4">
                                        <div class="flex items-start">
                                            <div class="w-8 flex justify-center text-gray-400 mt-0.5"><i class="fas fa-clock"></i></div>
                                            <div>
                                                <p class="text-xs font-semibold text-gray-500 uppercase">Waktu Pelaksanaan</p>
                                                <p class="font-medium text-gray-800">Senin, 25 Juni 2026</p>
                                                <p class="text-sm text-gray-600">08:00 - 12:00 WITA</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="w-8 flex justify-center text-gray-400 mt-0.5"><i class="fas fa-map-marker-alt"></i></div>
                                            <div>
                                                <p class="text-xs font-semibold text-gray-500 uppercase">Lokasi / Platform</p>
                                                <p class="font-medium text-gray-800">Aula Fakultas Vokasi UNHAS / Zoom Meeting</p>
                                                <a href="#" class="text-sm text-vokasi-primary hover:underline mt-1 inline-block">Lihat Rekaman Zoom <i class="fas fa-external-link-alt text-xs ml-1"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-1 space-y-4 md:border-l md:border-gray-100 md:pl-6">
                                        <div class="flex items-start">
                                            <div class="w-8 flex justify-center text-gray-400 mt-0.5"><i class="fas fa-user-tie"></i></div>
                                            <div>
                                                <p class="text-xs font-semibold text-gray-500 uppercase">Pemateri</p>
                                                <p class="font-medium text-gray-800">Dr. Ir. Budi Santoso, M.T.</p>
                                                <p class="text-sm text-gray-600">Ketua Panitia Magang Industri Vokasi</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="w-8 flex justify-center text-gray-400 mt-0.5"><i class="fas fa-clipboard-list"></i></div>
                                            <div>
                                                <p class="text-xs font-semibold text-gray-500 uppercase">Topik Utama</p>
                                                <ul class="list-disc list-inside text-sm text-gray-600 mt-1 space-y-1">
                                                    <li>Etika Profesi di Dunia Kerja</li>
                                                    <li>Sistem Absensi SIGMAVO</li>
                                                    <li>Tata Cara Penulisan Logbook</li>
                                                    <li>SOP Pelaporan Akhir</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN (Unduhan Materi) -->
                    <div class="space-y-6">
                        
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="border-b border-gray-100 p-5 bg-gray-50/50">
                                <h3 class="font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-folder-open text-vokasi-primary mr-2"></i> Materi Pembekalan
                                </h3>
                                <p class="text-xs text-gray-500 mt-1">Unduh pedoman dan dokumen pendukung magang.</p>
                            </div>
                            
                            <div class="divide-y divide-gray-100">
                                <!-- File 1 -->
                                <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                                            <i class="fas fa-file-pdf text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-sm text-gray-800 group-hover:text-vokasi-primary transition-colors">Buku Saku Magang 2026</p>
                                            <p class="text-xs text-gray-500">PDF • 2.4 MB</p>
                                        </div>
                                    </div>
                                    <button class="text-gray-400 hover:text-vokasi-primary transition-colors" title="Unduh">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </div>

                                <!-- File 2 -->
                                <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                                            <i class="fas fa-file-pdf text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-sm text-gray-800 group-hover:text-vokasi-primary transition-colors">Materi Etika Profesi</p>
                                            <p class="text-xs text-gray-500">PDF • 1.8 MB</p>
                                        </div>
                                    </div>
                                    <button class="text-gray-400 hover:text-vokasi-primary transition-colors" title="Unduh">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </div>

                                <!-- File 3 -->
                                <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                                            <i class="fas fa-file-word text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-sm text-gray-800 group-hover:text-vokasi-primary transition-colors">Format Logbook Manual</p>
                                            <p class="text-xs text-gray-500">DOCX • 500 KB</p>
                                        </div>
                                    </div>
                                    <button class="text-gray-400 hover:text-vokasi-primary transition-colors" title="Unduh">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <!-- FOOTER -->
            <footer class="mt-auto py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
                Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
            </footer>

        </main>
    </div>
@endsection