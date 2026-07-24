@extends('layouts.dashboard')

@section('content')
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative">
            
            <div class="max-w-5xl mx-auto w-full flex-1">
                
                <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Antrean Verifikasi</h2>
                        <p class="text-sm text-gray-500 mt-1">Tinjau logbook, absensi flag, dan pengajuan izin dari mahasiswa bimbingan Anda.</p>
                    </div>
                </div>

                <!-- QUICK FILTER TABS -->
                <div class="flex overflow-x-auto gap-2 mb-6 custom-scrollbar pb-2">
                    <button class="bg-vokasi-primary text-white px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap shadow-sm">
                        Semua (4)
                    </button>
                    <button class="bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors">
                        Logbook Harian (2)
                    </button>
                    <button class="bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors flex items-center">
                        Izin / Sakit (1)
                    </button>
                    <button class="bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors flex items-center">
                        Flag Absensi (1)
                    </button>
                </div>

                <div class="space-y-6">
                    
                    <!-- ITEM 1: LOGBOOK HARIAN -->
                    <div class="bg-white rounded-xl shadow-sm border border-yellow-200 overflow-hidden">
                        <div class="bg-yellow-50/50 border-b border-yellow-100 p-4 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <span class="bg-blue-100 text-blue-700 p-1.5 rounded-lg"><i class="fas fa-book text-sm"></i></span>
                                <h3 class="font-bold text-gray-800">Pengajuan Logbook Harian</h3>
                            </div>
                            <span class="text-xs font-semibold text-gray-500"><i class="far fa-clock mr-1"></i> Diajukan: Kemarin, 17:15 WITA</span>
                        </div>
                        <div class="p-5 flex flex-col md:flex-row gap-6">
                            <!-- Info Mahasiswa & Konten -->
                            <div class="flex-1 space-y-4">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name=Fadehl+Thristansyah&background=f3f4f6&color=37A7AC" alt="Mahasiswa" class="w-10 h-10 rounded-full border border-gray-200">
                                    <div>
                                        <p class="font-bold text-gray-800 text-sm">Fadehl Thristansyah <span class="text-gray-400 font-normal ml-1">H071231012</span></p>
                                        <p class="text-xs text-gray-500">PT. SmartPlay Inovasi - Selasa, 21 Jul 2026</p>
                                    </div>
                                </div>
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-700 leading-relaxed text-justify">
                                    <p>Melakukan riset mengenai tren mainan edukasi berbasis sensorik. Mengikuti rapat divisi bersama Supervisor Lapangan untuk membahas konsep awal produk SmartBlocks. Membuat sketsa kasar dari hasil diskusi.</p>
                                    <!-- Attachment if any -->
                                    <div class="mt-3 flex items-center gap-2">
                                        <span class="text-xs font-semibold text-gray-500"><i class="fas fa-paperclip"></i> Lampiran Foto:</span>
                                        <a href="#" class="text-vokasi-primary hover:underline text-xs font-medium"><i class="fas fa-image mr-1"></i>sketsa_smartblock.jpg</a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Area -->
                            <div class="w-full md:w-64 shrink-0 flex flex-col justify-end space-y-2 border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-6">
                                <textarea rows="2" placeholder="Tulis catatan (opsional jika setuju, wajib jika revisi)..." class="w-full text-xs p-2 border border-gray-300 rounded focus:outline-none focus:border-vokasi-primary resize-none"></textarea>
                                <button class="w-full bg-green-500 hover:bg-green-600 text-white text-sm font-bold py-2 rounded transition-colors shadow-sm">
                                    <i class="fas fa-check mr-1"></i> Approve (-8 Jam)
                                </button>
                                <button class="w-full bg-white hover:bg-red-50 text-red-500 border border-red-200 text-sm font-bold py-2 rounded transition-colors shadow-sm">
                                    <i class="fas fa-undo mr-1"></i> Minta Revisi
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- ITEM 2: SURAT IZIN / SAKIT -->
                    <div class="bg-white rounded-xl shadow-sm border border-orange-200 overflow-hidden relative">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-orange-500"></div>
                        <div class="bg-orange-50/30 border-b border-orange-100 p-4 pl-6 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <span class="bg-orange-100 text-orange-700 p-1.5 rounded-lg"><i class="fas fa-notes-medical text-sm"></i></span>
                                <h3 class="font-bold text-gray-800">Pengajuan Izin Sakit</h3>
                            </div>
                            <span class="text-xs font-semibold text-gray-500"><i class="far fa-clock mr-1"></i> Diajukan: Hari Ini, 07:30 WITA</span>
                        </div>
                        <div class="p-5 pl-6 flex flex-col md:flex-row gap-6">
                            <div class="flex-1 space-y-4">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name=Siti+Nurhaliza&background=f3f4f6&color=6b7280" alt="Mahasiswa" class="w-10 h-10 rounded-full border border-gray-200">
                                    <div>
                                        <p class="font-bold text-gray-800 text-sm">Siti Nurhaliza <span class="text-gray-400 font-normal ml-1">H071231088</span></p>
                                        <p class="text-xs text-gray-500">PT. Agro Nusantara - Tanggal Izin: 22 Jul 2026</p>
                                    </div>
                                </div>
                                <div class="bg-orange-50 border border-orange-100 rounded-lg p-4 text-sm text-gray-700">
                                    <p class="mb-2"><strong>Alasan:</strong> Demam Berdarah (Tifus)</p>
                                    <p class="text-xs text-gray-600 mb-3">Sesuai pemeriksaan dokter, mahasiswa perlu istirahat total selama 3 hari.</p>
                                    <a href="#" class="inline-flex items-center text-xs font-bold bg-white text-orange-600 border border-orange-200 px-3 py-1.5 rounded hover:bg-orange-50 transition-colors">
                                        <i class="fas fa-file-medical-alt mr-2"></i> Lihat Surat Keterangan Dokter (PDF)
                                    </a>
                                </div>
                            </div>
                            
                            <div class="w-full md:w-64 shrink-0 flex flex-col justify-end space-y-2 border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-6">
                                <p class="text-[10px] text-gray-500 mb-1 leading-tight">Persetujuan ini <strong class="text-red-500">TIDAK</strong> memotong kuota 8 jam mahasiswa.</p>
                                <button class="w-full bg-vokasi-primary hover:bg-vokasi-dark text-white text-sm font-bold py-2 rounded transition-colors shadow-sm">
                                    <i class="fas fa-check-circle mr-1"></i> Approve Izin
                                </button>
                                <button class="w-full bg-white hover:bg-gray-50 text-gray-600 border border-gray-300 text-sm font-bold py-2 rounded transition-colors shadow-sm">
                                    Tolak Alasan
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- ITEM 3: FLAG ABSENSI LUPA PULANG -->
                    <div class="bg-white rounded-xl shadow-sm border border-red-200 overflow-hidden relative">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-red-500"></div>
                        <div class="bg-red-50/30 border-b border-red-100 p-4 pl-6 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <span class="bg-red-100 text-red-700 p-1.5 rounded-lg"><i class="fas fa-flag text-sm"></i></span>
                                <h3 class="font-bold text-gray-800">Flag: Lupa Absen Pulang</h3>
                            </div>
                            <span class="text-xs font-semibold text-gray-500"><i class="far fa-clock mr-1"></i> Sistem Generate: 17 Jul 2026</span>
                        </div>
                        <div class="p-5 pl-6 flex flex-col md:flex-row gap-6">
                            <div class="flex-1 space-y-4">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name=Andi+Reza&background=f3f4f6&color=6b7280" alt="Mahasiswa" class="w-10 h-10 rounded-full border border-gray-200">
                                    <div>
                                        <p class="font-bold text-gray-800 text-sm">Andi Reza Syahputra <span class="text-gray-400 font-normal ml-1">H071231045</span></p>
                                        <p class="text-xs text-gray-500">BRIDA Kota Makassar - Jumat, 17 Jul 2026</p>
                                    </div>
                                </div>
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 flex gap-4 items-center">
                                    <div class="text-center px-4 border-r border-gray-200">
                                        <p class="text-[10px] font-bold text-gray-500 uppercase">Absen Pagi</p>
                                        <p class="font-mono font-bold text-green-600">08:02</p>
                                    </div>
                                    <div class="text-center px-4">
                                        <p class="text-[10px] font-bold text-gray-500 uppercase">Absen Pulang</p>
                                        <p class="font-mono font-bold text-red-500">-- : --</p>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-700 leading-tight">
                                            <strong>Klarifikasi Mahasiswa:</strong> "Maaf Pak, handphone saya kehabisan baterai saat jam pulang. Logbook harian sudah saya kerjakan. Mohon kebijakannya."
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="w-full md:w-64 shrink-0 flex flex-col justify-end space-y-2 border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-6">
                                <button class="w-full bg-green-500 hover:bg-green-600 text-white text-sm font-bold py-2 rounded transition-colors shadow-sm">
                                    <i class="fas fa-check-double mr-1"></i> Sahkan & Potong Jam
                                </button>
                                <button class="w-full bg-white hover:bg-red-50 text-red-500 border border-red-200 text-sm font-bold py-2 rounded transition-colors shadow-sm">
                                    <i class="fas fa-times mr-1"></i> Tolak Absensi
                                </button>
                            </div>
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