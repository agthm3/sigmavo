@extends('layouts.dashboard')

@section('content')
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative">
            
            <div class="max-w-7xl mx-auto w-full flex-1 space-y-6">
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Riwayat Terverifikasi</h2>
                        <p class="text-sm text-gray-500 mt-1">Daftar laporan logbook, absensi, dan izin yang telah selesai Anda tindak lanjuti.</p>
                    </div>
                </div>

                <!-- TABEL RIWAYAT VERIFIKASI -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    
                    <!-- Table Header Controls -->
                    <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row justify-between md:items-center gap-4 bg-gray-50/50">
                        <!-- Search Box -->
                        <div class="relative w-full md:w-80">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                            <input type="text" placeholder="Cari nama mahasiswa / NIM..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary">
                        </div>
                        
                        <!-- Filters -->
                        <div class="flex flex-wrap items-center gap-2">
                            <select class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option>Semua Jenis Laporan</option>
                                <option>Logbook Harian</option>
                                <option>Izin / Sakit</option>
                                <option>Flag Absensi</option>
                            </select>

                            <select class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option>Status: Approved</option>
                                <option>Status: Ditolak / Revisi</option>
                                <option>Semua Status</option>
                            </select>
                        </div>
                    </div>

                    <!-- Responsive Table -->
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[1000px]">
                            <thead>
                                <tr class="bg-gray-100/60 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <th class="p-4 w-12 text-center">No</th>
                                    <th class="p-4 w-52">Mahasiswa</th>
                                    <th class="p-4 w-40">Jenis Laporan</th>
                                    <th class="p-4 w-32">Tgl Kegiatan</th>
                                    <th class="p-4 min-w-[280px]">Uraian / Ringkasan</th>
                                    <th class="p-4 w-36">Status Keputusan</th>
                                    <th class="p-4 w-28 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                
                                <!-- Baris 1: Approved Logbook -->
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-center text-gray-500 font-medium">1</td>
                                    <td class="p-4">
                                        <p class="font-bold text-gray-800">Fadehl Thristansyah</p>
                                        <p class="text-xs text-gray-500">H071231012</p>
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded">
                                            <i class="fas fa-book mr-1.5 text-blue-500"></i> Logbook Harian
                                        </span>
                                    </td>
                                    <td class="p-4 text-gray-600 font-medium whitespace-nowrap">
                                        20 Jul 2026
                                    </td>
                                    <td class="p-4 text-gray-700 leading-relaxed">
                                        Hari pertama magang. Orientasi lingkungan kerja, setup software AutoCAD dan SolidWorks...
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full border border-green-200">
                                            <i class="fas fa-check-double mr-1"></i> Approved (-8 Jam)
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button class="text-gray-500 hover:text-vokasi-primary bg-gray-100 hover:bg-gray-200 p-2 rounded-lg transition-colors text-xs font-semibold" title="Lihat Detail">
                                            <i class="fas fa-eye mr-1"></i> Detail
                                        </button>
                                    </td>
                                </tr>

                                <!-- Baris 2: Approved Izin -->
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-center text-gray-500 font-medium">2</td>
                                    <td class="p-4">
                                        <p class="font-bold text-gray-800">Andi Reza Syahputra</p>
                                        <p class="text-xs text-gray-500">H071231045</p>
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center text-xs font-semibold text-orange-600 bg-orange-50 px-2.5 py-1 rounded">
                                            <i class="fas fa-notes-medical mr-1.5 text-orange-500"></i> Izin Sakit
                                        </span>
                                    </td>
                                    <td class="p-4 text-gray-600 font-medium whitespace-nowrap">
                                        18 Jul 2026
                                    </td>
                                    <td class="p-4 text-gray-700 leading-relaxed">
                                        Izin sakit flu berat (Surat Keterangan Dokter terlampir).
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full border border-green-200">
                                            <i class="fas fa-check mr-1"></i> Disetujui
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button class="text-gray-500 hover:text-vokasi-primary bg-gray-100 hover:bg-gray-200 p-2 rounded-lg transition-colors text-xs font-semibold" title="Lihat Detail">
                                            <i class="fas fa-eye mr-1"></i> Detail
                                        </button>
                                    </td>
                                </tr>

                                <!-- Baris 3: Revisi Logbook -->
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-center text-gray-500 font-medium">3</td>
                                    <td class="p-4">
                                        <p class="font-bold text-gray-800">Fadehl Thristansyah</p>
                                        <p class="text-xs text-gray-500">H071231012</p>
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded">
                                            <i class="fas fa-book mr-1.5 text-blue-500"></i> Logbook Harian
                                        </span>
                                    </td>
                                    <td class="p-4 text-gray-600 font-medium whitespace-nowrap">
                                        17 Jul 2026
                                    </td>
                                    <td class="p-4 text-gray-700 leading-relaxed">
                                        Hari ini santai saja, belajar hal baru tentang material kayu...
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full border border-red-200">
                                            <i class="fas fa-undo mr-1"></i> Minta Revisi
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button class="text-gray-500 hover:text-vokasi-primary bg-gray-100 hover:bg-gray-200 p-2 rounded-lg transition-colors text-xs font-semibold" title="Lihat Detail">
                                            <i class="fas fa-eye mr-1"></i> Detail
                                        </button>
                                    </td>
                                </tr>

                                <!-- Baris 4: Approved Flag Absensi -->
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-center text-gray-500 font-medium">4</td>
                                    <td class="p-4">
                                        <p class="font-bold text-gray-800">Siti Nurhaliza</p>
                                        <p class="text-xs text-gray-500">H071231088</p>
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center text-xs font-semibold text-purple-600 bg-purple-50 px-2.5 py-1 rounded">
                                            <i class="fas fa-flag mr-1.5 text-purple-500"></i> Flag Absensi
                                        </span>
                                    </td>
                                    <td class="p-4 text-gray-600 font-medium whitespace-nowrap">
                                        15 Jul 2026
                                    </td>
                                    <td class="p-4 text-gray-700 leading-relaxed">
                                        Klarifikasi lupa absen pulang karena mati listrik di lokasi magang.
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full border border-green-200">
                                            <i class="fas fa-check-double mr-1"></i> Disahkan (-8 Jam)
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button class="text-gray-500 hover:text-vokasi-primary bg-gray-100 hover:bg-gray-200 p-2 rounded-lg transition-colors text-xs font-semibold" title="Lihat Detail">
                                            <i class="fas fa-eye mr-1"></i> Detail
                                        </button>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Controls -->
                    <div class="p-4 border-t border-gray-100 flex items-center justify-between bg-white text-sm">
                        <span class="text-gray-500">Menampilkan 1 hingga 4 dari 24 riwayat</span>
                        <div class="flex space-x-1">
                            <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-400 bg-gray-50 cursor-not-allowed">Sebelummya</button>
                            <button class="px-3 py-1 border border-vokasi-primary rounded-lg text-white bg-vokasi-primary font-medium">1</button>
                            <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">2</button>
                            <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">3</button>
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
@endsection