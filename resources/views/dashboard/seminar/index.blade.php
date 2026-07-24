@extends('layouts.dashboard')

@section('content')
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative">
            
            <div class="max-w-6xl mx-auto w-full flex-1">
                
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Seminar Hasil Magang</h2>
                        <p class="text-sm text-gray-500 mt-1">Informasi pendaftaran, jadwal pelaksanaan, dan penilaian seminar Anda.</p>
                    </div>
                    <!-- Status Badge Utama -->
                    <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg font-bold shadow-sm border border-blue-200 flex items-center">
                        <i class="fas fa-calendar-check mr-2"></i> Jadwal Ditetapkan
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    
                    <!-- AREA KIRI: SYARAT & BERKAS -->
                    <div class="lg:col-span-1 space-y-6">
                        
                        <!-- Syarat Kelayakan Seminar -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="border-b border-gray-100 p-4 bg-gray-50/50">
                                <h3 class="font-bold text-gray-800 flex items-center text-sm">
                                    <i class="fas fa-tasks text-vokasi-primary mr-2"></i> Syarat Kelayakan Seminar
                                </h3>
                            </div>
                            <div class="p-5 space-y-4">
                                <!-- Progress 900 Jam -->
                                <div class="flex items-start gap-3">
                                    <div class="text-green-500 mt-0.5"><i class="fas fa-check-circle"></i></div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">Pemenuhan 900 Jam</p>
                                        <p class="text-xs text-gray-500 mt-0.5">Tercapai 900 / 900 Jam</p>
                                    </div>
                                </div>
                                <!-- Penilaian Supervisor -->
                                <div class="flex items-start gap-3">
                                    <div class="text-green-500 mt-0.5"><i class="fas fa-check-circle"></i></div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">Nilai Supervisor Lapangan</p>
                                        <p class="text-xs text-gray-500 mt-0.5">Nilai telah dikirim oleh supervisor</p>
                                    </div>
                                </div>
                                <!-- Laporan Draft -->
                                <div class="flex items-start gap-3">
                                    <div class="text-green-500 mt-0.5"><i class="fas fa-check-circle"></i></div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">Draft Laporan Akhir</p>
                                        <p class="text-xs text-gray-500 mt-0.5">Disetujui Dosen Pembimbing</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-green-50 p-3 border-t border-green-100 text-center">
                                <span class="text-xs font-bold text-green-700">Persyaratan Lengkap. Anda siap seminar.</span>
                            </div>
                        </div>

                        <!-- Upload Presentasi PPT -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <h3 class="font-bold text-gray-800 text-sm mb-4 border-b border-gray-100 pb-2">
                                <i class="fas fa-file-powerpoint text-orange-500 mr-2"></i> Bahan Presentasi (PPT)
                            </h3>
                            <p class="text-xs text-gray-500 mb-3">Unggah file presentasi (Maks 10MB) agar penguji dapat mengunduhnya sebelum hari-H.</p>
                            
                            <div class="flex items-center justify-center w-full mb-3">
                                <label class="flex flex-col items-center justify-center w-full h-20 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-cloud-upload-alt text-gray-400 text-lg mb-1"></i>
                                        <p class="text-[10px] text-gray-500"><span class="font-semibold text-vokasi-primary">Klik upload</span> presentasi.pptx</p>
                                    </div>
                                    <input type="file" class="hidden" accept=".ppt,.pptx,.pdf" />
                                </label>
                            </div>

                            <!-- File yang sudah diupload -->
                            <div class="bg-blue-50 border border-blue-100 rounded p-2 flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-file-powerpoint text-orange-500"></i>
                                    <span class="text-xs font-medium text-blue-900 truncate w-32">Presentasi_Fadehl.pptx</span>
                                </div>
                                <button class="text-red-500 hover:text-red-700 text-xs" title="Hapus"><i class="fas fa-times"></i></button>
                            </div>
                        </div>

                    </div>

                    <!-- AREA KANAN: JADWAL & DOSEN PENGUJI -->
                    <div class="lg:col-span-2 space-y-6">
                        
                        <!-- Informasi Jadwal Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-vokasi-dark p-6 text-white relative overflow-hidden">
                                <!-- Aksen pattern latar belakang -->
                                <i class="fas fa-chalkboard-teacher absolute text-9xl right-0 -bottom-4 opacity-10"></i>
                                <div class="relative z-10">
                                    <h3 class="text-lg font-bold opacity-90 uppercase tracking-wide text-vokasi-light mb-1">Jadwal Ujian Anda</h3>
                                    <p class="text-3xl font-extrabold mb-1">Jumat, 31 Juli 2026</p>
                                    <div class="flex flex-wrap items-center gap-4 text-sm mt-3 font-medium">
                                        <span class="bg-black/20 px-3 py-1 rounded-full"><i class="far fa-clock mr-1.5"></i> 09:00 - 10:30 WITA</span>
                                        <span class="bg-black/20 px-3 py-1 rounded-full"><i class="fas fa-map-marker-alt mr-1.5"></i> Ruang Sidang Vokasi (Lantai 2)</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <h4 class="font-bold text-gray-800 border-b border-gray-100 pb-2 mb-4">Informasi Tambahan</h4>
                                <ul class="list-disc list-inside text-sm text-gray-600 space-y-2 mb-6">
                                    <li>Gunakan jas almamater dan pakaian kemeja putih berdasi.</li>
                                    <li>Hadir 15 menit sebelum jadwal yang ditentukan.</li>
                                    <li>Bawa salinan <em>hardcopy</em> draft laporan (dijilid rapi) sebanyak 3 rangkap untuk Dosen Pembimbing dan Penguji.</li>
                                </ul>

                                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded text-sm text-yellow-800">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Jangan lupa memastikan alat presentasi (laptop/pointer) sudah siap sebelum dosen memasuki ruangan.
                                </div>
                            </div>
                        </div>

                        <!-- Dewan Penguji & Pembimbing -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-3 mb-5 flex items-center">
                                <i class="fas fa-users text-vokasi-primary mr-2"></i> Tim Penilai & Penguji
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Dosen Pembimbing -->
                                <div class="border border-gray-100 rounded-lg p-4 bg-gray-50/50 hover:border-vokasi-light transition-colors">
                                    <span class="text-[10px] font-bold text-vokasi-primary uppercase tracking-wider mb-2 block">Dosen Pembimbing</span>
                                    <div class="flex items-center">
                                        <img src="https://ui-avatars.com/api/?name=Budi+Santoso&background=37A7AC&color=fff" alt="Dosen" class="w-12 h-12 rounded-full mr-3 border-2 border-white shadow-sm">
                                        <div>
                                            <p class="font-bold text-gray-800 text-sm">Dr. Ir. Budi Santoso, M.T.</p>
                                            <p class="text-xs text-gray-500">Ketua Sidang</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Penguji 1 -->
                                <div class="border border-gray-100 rounded-lg p-4 bg-gray-50/50 hover:border-vokasi-light transition-colors">
                                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2 block">Dosen Penguji I</span>
                                    <div class="flex items-center">
                                        <img src="https://ui-avatars.com/api/?name=Andi+Rahman&background=f3f4f6&color=6b7280" alt="Penguji 1" class="w-12 h-12 rounded-full mr-3 border-2 border-white shadow-sm">
                                        <div>
                                            <p class="font-bold text-gray-800 text-sm">Andi Rahman, S.T., M.Eng.</p>
                                            <p class="text-xs text-gray-500">Anggota Penguji</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Penguji 2 -->
                                <div class="border border-gray-100 rounded-lg p-4 bg-gray-50/50 hover:border-vokasi-light transition-colors">
                                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2 block">Dosen Penguji II</span>
                                    <div class="flex items-center">
                                        <img src="https://ui-avatars.com/api/?name=Siti+Aisyah&background=f3f4f6&color=6b7280" alt="Penguji 2" class="w-12 h-12 rounded-full mr-3 border-2 border-white shadow-sm">
                                        <div>
                                            <p class="font-bold text-gray-800 text-sm">Siti Aisyah, S.Kom., M.Cs.</p>
                                            <p class="text-xs text-gray-500">Anggota Penguji</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Placeholder Nilai -->
                                <div class="border border-dashed border-gray-300 rounded-lg p-4 flex flex-col items-center justify-center text-center bg-gray-50/30">
                                    <i class="fas fa-award text-2xl text-gray-300 mb-2"></i>
                                    <p class="text-sm font-bold text-gray-500">Nilai Akhir Seminar</p>
                                    <p class="text-xs text-gray-400">Akan muncul setelah sidang selesai</p>
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
@endsection