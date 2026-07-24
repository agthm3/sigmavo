@extends('layouts.dashboard')

@section('content')
     <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative">
            
            <div class="max-w-6xl mx-auto w-full flex-1">
                
                <!-- HEADER & DATE -->
                <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Catat Kehadiran Anda</h2>
                        <p class="text-sm text-gray-500 mt-1">PT. SmartPlay Inovasi - Pengembang Edukasi STEM</p>
                    </div>
                    <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-200 flex items-center gap-3">
                        <div class="text-vokasi-primary">
                            <i class="far fa-calendar-alt text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Hari Ini</p>
                            <p class="font-bold text-gray-800">Rabu, 22 Juli 2026</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    
                    <!-- AREA KIRI: KAMERA & GEOLOCATION (MOBILE FOCUSED) -->
                    <div class="lg:col-span-1 space-y-6">
                        
                        <!-- Panel Kamera -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 md:p-6 relative overflow-hidden flex flex-col items-center">
                            
                            <h3 class="font-bold text-gray-800 w-full mb-4 flex items-center justify-between border-b border-gray-100 pb-2">
                                <span><i class="fas fa-camera text-vokasi-primary mr-2"></i> Kamera Absensi</span>
                                <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded font-semibold animate-pulse">Live</span>
                            </h3>

                            <!-- Viewfinder Kamera -->
                            <div class="w-full aspect-[3/4] bg-gray-900 rounded-lg relative overflow-hidden border-2 border-gray-200 mb-4 flex items-center justify-center group cursor-pointer">
                                
                                <!-- Simulasi Feed Kamera / Placeholder -->
                                <div class="absolute inset-0 flex flex-col items-center justify-center text-gray-500 bg-gray-800 group-hover:bg-gray-700 transition-colors z-0">
                                    <i class="fas fa-user-circle text-6xl mb-2 opacity-50"></i>
                                    <p class="text-sm">Klik untuk mengaktifkan kamera</p>
                                </div>

                                <!-- Overlay Grid Kamera -->
                                <div class="absolute inset-0 border border-white/20 pointer-events-none z-10 flex">
                                    <div class="w-1/3 h-full border-r border-white/10"></div>
                                    <div class="w-1/3 h-full border-r border-white/10"></div>
                                </div>
                                <div class="absolute inset-0 border border-white/20 pointer-events-none z-10 flex flex-col">
                                    <div class="h-1/3 w-full border-b border-white/10"></div>
                                    <div class="h-1/3 w-full border-b border-white/10"></div>
                                </div>

                                <!-- Watermark (Akan ditempel di foto hasil jepretan) -->
                                <div class="absolute bottom-2 left-2 right-2 bg-black/60 backdrop-blur-sm text-white text-[10px] p-2 rounded z-20 font-mono">
                                    <p><i class="fas fa-map-marker-alt text-red-400 w-3"></i> -5.1322, 119.4255 (Akurat 12m)</p>
                                    <p><i class="fas fa-clock text-blue-400 w-3"></i> 22 Jul 2026, 07:49:20 WITA</p>
                                    <p><i class="fas fa-user text-green-400 w-3"></i> H071231012 - Fadehl Thristansyah</p>
                                </div>
                            </div>

                            <!-- Tombol Aksi Absen -->
                            <div class="w-full space-y-3">
                                <!-- Tombol Masuk (Biru/Hijau) -->
                                <button class="w-full bg-vokasi-primary hover:bg-vokasi-dark text-white font-bold py-3 rounded-lg shadow-md transition-colors flex items-center justify-center">
                                    <i class="fas fa-sign-in-alt mr-2"></i> Absen Masuk (08:00)
                                </button>
                                <!-- Tombol Pulang (Abu-abu, aktif nanti sore) -->
                                <button class="w-full bg-gray-100 text-gray-400 font-bold py-3 rounded-lg border border-gray-200 cursor-not-allowed flex items-center justify-center">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Absen Pulang (17:00)
                                </button>
                            </div>

                            <p class="text-[10px] text-gray-400 text-center mt-3">Pastikan wajah terlihat jelas dan fitur GPS / Lokasi pada perangkat Anda aktif.</p>
                        </div>

                        <!-- Card Izin/Sakit -->
                        <div class="bg-red-50 rounded-xl shadow-sm border border-red-100 p-4 flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-red-700 text-sm">Tidak bisa hadir?</h4>
                                <p class="text-xs text-red-600 mt-0.5">Ajukan surat izin atau sakit di sini.</p>
                            </div>
                            <button class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold py-2 px-3 rounded shadow-sm transition-colors">
                                Ajukan
                            </button>
                        </div>

                    </div>

                    <!-- AREA KANAN: STATUS JAM & RIWAYAT -->
                    <div class="lg:col-span-2 space-y-6">
                        
                        <!-- Panel Progress Jam (Krusial) -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="font-bold text-gray-800 mb-4 flex items-center border-b border-gray-100 pb-2">
                                <i class="fas fa-chart-pie text-vokasi-primary mr-2"></i> Status Kuota Jam Magang
                            </h3>
                            
                            <div class="flex flex-col md:flex-row gap-6 items-center">
                                <!-- Progress Circle (Simulated with CSS) -->
                                <div class="relative w-32 h-32 shrink-0">
                                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                        <!-- Background Circle -->
                                        <circle cx="50" cy="50" r="40" fill="transparent" stroke="#e5e7eb" stroke-width="8"></circle>
                                        <!-- Progress Circle (15.5% dari 900) -->
                                        <circle cx="50" cy="50" r="40" fill="transparent" stroke="#37A7AC" stroke-width="8" stroke-dasharray="251.2" stroke-dashoffset="212" stroke-linecap="round"></circle>
                                    </svg>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                                        <span class="text-2xl font-bold text-gray-800">140</span>
                                        <span class="text-[10px] text-gray-500 uppercase font-semibold">Jam Tercapai</span>
                                    </div>
                                </div>

                                <div class="flex-1 w-full space-y-4">
                                    <div>
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-gray-500">Total Target Magang</span>
                                            <span class="font-bold text-gray-800">900 Jam</span>
                                        </div>
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-gray-500">Sisa Jam yang Harus Ditempuh</span>
                                            <span class="font-bold text-orange-600">760 Jam</span>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-blue-50 border border-blue-100 p-3 rounded-lg flex items-start gap-3">
                                        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                                        <p class="text-xs text-blue-800 leading-relaxed">
                                            <strong>Sistem Pemotongan:</strong> Setiap absensi harian lengkap (Masuk & Pulang) beserta pengisian Logbook yang <strong>di-approve Dosen Pendamping</strong> akan memotong kuota Anda sebanyak 8 Jam.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Riwayat Absensi Minggu Ini -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                                <h3 class="font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-list text-vokasi-primary mr-2"></i> Log Absensi (Minggu Ini)
                                </h3>
                                <a href="#" class="text-sm text-vokasi-primary hover:underline font-medium">Lihat Semua</a>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                                            <th class="p-4 font-semibold">Tanggal</th>
                                            <th class="p-4 font-semibold text-center">Masuk</th>
                                            <th class="p-4 font-semibold text-center">Pulang</th>
                                            <th class="p-4 font-semibold">Status Dosen</th>
                                            <th class="p-4 font-semibold text-right">Potongan Jam</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm">
                                        
                                        <!-- Row 1: Hari Ini (Belum Absen) -->
                                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                            <td class="p-4">
                                                <p class="font-medium text-gray-800">Rabu, 22 Jul 2026</p>
                                                <p class="text-xs text-gray-500">Hari ini</p>
                                            </td>
                                            <td class="p-4 text-center text-gray-400 font-mono">-</td>
                                            <td class="p-4 text-center text-gray-400 font-mono">-</td>
                                            <td class="p-4">
                                                <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-600 text-[10px] font-bold rounded">
                                                    Belum Absen
                                                </span>
                                            </td>
                                            <td class="p-4 text-right font-medium text-gray-400">0 Jam</td>
                                        </tr>

                                        <!-- Row 2: Kemarin (Menunggu Approve Dosen / Pending) -->
                                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                            <td class="p-4">
                                                <p class="font-medium text-gray-800">Selasa, 21 Jul 2026</p>
                                            </td>
                                            <td class="p-4 text-center">
                                                <span class="text-green-600 font-mono font-medium">07:55</span>
                                                <i class="fas fa-check-circle text-green-500 text-[10px] ml-1" title="Lokasi Valid"></i>
                                            </td>
                                            <td class="p-4 text-center">
                                                <span class="text-green-600 font-mono font-medium">17:05</span>
                                                <i class="fas fa-check-circle text-green-500 text-[10px] ml-1" title="Lokasi Valid"></i>
                                            </td>
                                            <td class="p-4">
                                                <span class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-700 text-[10px] font-bold rounded">
                                                    <i class="fas fa-spinner fa-spin mr-1"></i> Pending Asistensi
                                                </span>
                                            </td>
                                            <td class="p-4 text-right font-medium text-yellow-600">+ 8 Jam (Pending)</td>
                                        </tr>

                                        <!-- Row 3: Lusa Kemarin (Approved) -->
                                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                            <td class="p-4">
                                                <p class="font-medium text-gray-800">Senin, 20 Jul 2026</p>
                                            </td>
                                            <td class="p-4 text-center">
                                                <span class="text-green-600 font-mono font-medium">07:48</span>
                                            </td>
                                            <td class="p-4 text-center">
                                                <span class="text-green-600 font-mono font-medium">17:10</span>
                                            </td>
                                            <td class="p-4">
                                                <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded">
                                                    <i class="fas fa-check-double mr-1"></i> Approved
                                                </span>
                                            </td>
                                            <td class="p-4 text-right font-bold text-green-600">- 8 Jam</td>
                                        </tr>

                                        <!-- Row 4: Flag Issue (Hanya absen pagi) -->
                                        <tr class="border-b border-gray-50 hover:bg-red-50 transition-colors bg-red-50/30">
                                            <td class="p-4">
                                                <p class="font-medium text-gray-800">Jumat, 17 Jul 2026</p>
                                            </td>
                                            <td class="p-4 text-center">
                                                <span class="text-green-600 font-mono font-medium">08:02</span>
                                            </td>
                                            <td class="p-4 text-center">
                                                <span class="text-red-500 font-mono font-bold">Lupa</span>
                                            </td>
                                            <td class="p-4">
                                                <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-700 text-[10px] font-bold rounded">
                                                    <i class="fas fa-flag mr-1"></i> Flag: Lupa Pulang
                                                </span>
                                            </td>
                                            <td class="p-4 text-right">
                                                <button class="text-xs bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">Lapor Dosen</button>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
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