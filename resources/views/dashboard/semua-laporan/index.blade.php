@extends('layouts.dashboard')

@section('content')
     <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative">
            
            <div class="max-w-7xl mx-auto w-full flex-1 space-y-6">
                
                <!-- Page Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Semua Laporan Akhir</h2>
                        <p class="text-sm text-gray-500 mt-1">Pantau dan verifikasi dokumen laporan akhir magang seluruh mahasiswa.</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-semibold py-2 px-4 rounded-lg transition-colors shadow-sm flex items-center">
                            <i class="fas fa-print mr-2"></i> Cetak Rekap
                        </button>
                        <button class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition-colors shadow-sm flex items-center">
                            <i class="fas fa-file-excel mr-2"></i> Export Data
                        </button>
                    </div>
                </div>

                <!-- SUMMARY CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Total Laporan Masuk</p>
                                <h3 class="text-3xl font-bold text-gray-800">124</h3>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center">
                                <i class="fas fa-folder-open"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Menunggu Verifikasi</p>
                                <h3 class="text-3xl font-bold text-yellow-600">18</h3>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-yellow-50 text-yellow-500 flex items-center justify-center">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Laporan Approved</p>
                                <h3 class="text-3xl font-bold text-green-600">95</h3>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-green-50 text-green-500 flex items-center justify-center">
                                <i class="fas fa-check-double"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Perlu Revisi</p>
                                <h3 class="text-3xl font-bold text-red-600">11</h3>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-red-50 text-red-500 flex items-center justify-center">
                                <i class="fas fa-undo"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABLE SECTION -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    
                    <!-- Toolbar -->
                    <div class="p-5 border-b border-gray-100 flex flex-col lg:flex-row justify-between lg:items-center gap-4 bg-gray-50/50">
                        <div class="relative w-full lg:w-96">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                            <input type="text" placeholder="Cari nama atau judul laporan..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary">
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            <select class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option>Filter Prodi: Semua</option>
                                <option>D4 Teknologi Produksi Pertanian</option>
                                <option>D4 Terapi Gigi</option>
                                <option>D4 Agribisnis</option>
                            </select>

                            <select class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option>Semua Status</option>
                                <option>Menunggu</option>
                                <option>Approved</option>
                                <option>Revisi</option>
                            </select>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[1100px]">
                            <thead>
                                <tr class="bg-gray-100/60 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <th class="p-4 w-12 text-center">No</th>
                                    <th class="p-4 w-60">Mahasiswa & Prodi</th>
                                    <th class="p-4 w-56">Tempat Magang</th>
                                    <th class="p-4 min-w-[200px]">Dokumen Laporan</th>
                                    <th class="p-4 w-32">Tgl Submit</th>
                                    <th class="p-4 w-32 text-center">Status</th>
                                    <th class="p-4 w-32 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                
                                <!-- Baris 1: Menunggu -->
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-center text-gray-500 font-medium">1</td>
                                    <td class="p-4">
                                        <p class="font-bold text-gray-800">Fadehl Thristansyah</p>
                                        <p class="text-xs text-gray-500">H071231012</p>
                                        <p class="text-[10px] text-vokasi-primary mt-1 font-semibold">D4 Teknologi Produksi Pertanian</p>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-medium text-gray-800">PT. SmartPlay Inovasi</p>
                                        <p class="text-xs text-gray-500">Makassar</p>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-2 bg-blue-50 border border-blue-100 p-2 rounded w-max hover:bg-blue-100 transition-colors cursor-pointer">
                                            <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                                            <div>
                                                <p class="text-xs font-bold text-blue-900 leading-tight">Laporan_Akhir_Fadehl.pdf</p>
                                                <p class="text-[10px] text-blue-600">3.2 MB</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-medium text-gray-700">22 Jul 2026</p>
                                        <p class="text-xs text-gray-400">10:45 WITA</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-yellow-100 text-yellow-700 text-[10px] font-bold rounded-full border border-yellow-200">
                                            <i class="fas fa-spinner fa-spin mr-1.5"></i> Menunggu
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button class="bg-vokasi-primary hover:bg-vokasi-dark text-white text-xs font-bold py-1.5 px-3 rounded shadow-sm transition-colors">
                                            Verifikasi
                                        </button>
                                    </td>
                                </tr>

                                <!-- Baris 2: Approved -->
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-center text-gray-500 font-medium">2</td>
                                    <td class="p-4">
                                        <p class="font-bold text-gray-800">Andi Reza Syahputra</p>
                                        <p class="text-xs text-gray-500">H071231045</p>
                                        <p class="text-[10px] text-vokasi-primary mt-1 font-semibold">D4 Terapi Gigi</p>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-medium text-gray-800">RSUD Kota Makassar</p>
                                        <p class="text-xs text-gray-500">Makassar</p>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-2 bg-blue-50 border border-blue-100 p-2 rounded w-max hover:bg-blue-100 transition-colors cursor-pointer">
                                            <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                                            <div>
                                                <p class="text-xs font-bold text-blue-900 leading-tight">Laporan_Final_Andi.pdf</p>
                                                <p class="text-[10px] text-blue-600">5.1 MB</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-medium text-gray-700">18 Jul 2026</p>
                                        <p class="text-xs text-gray-400">14:20 WITA</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full border border-green-200">
                                            <i class="fas fa-check-double mr-1.5"></i> Approved
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button class="bg-white border border-gray-300 text-gray-600 hover:bg-gray-50 text-xs font-bold py-1.5 px-3 rounded shadow-sm transition-colors">
                                            Detail
                                        </button>
                                    </td>
                                </tr>

                                <!-- Baris 3: Revisi -->
                                <tr class="bg-red-50/20 hover:bg-red-50/40 transition-colors">
                                    <td class="p-4 text-center text-gray-500 font-medium">3</td>
                                    <td class="p-4">
                                        <p class="font-bold text-gray-800">Siti Nurhaliza</p>
                                        <p class="text-xs text-gray-500">H071231088</p>
                                        <p class="text-[10px] text-vokasi-primary mt-1 font-semibold">D4 Agribisnis</p>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-medium text-gray-800">PT. Agro Nusantara</p>
                                        <p class="text-xs text-gray-500">Maros</p>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-2 bg-blue-50 border border-blue-100 p-2 rounded w-max hover:bg-blue-100 transition-colors cursor-pointer">
                                            <i class="fas fa-file-word text-blue-500 text-lg"></i>
                                            <div>
                                                <p class="text-xs font-bold text-blue-900 leading-tight">Draft_Laporan_Siti.docx</p>
                                                <p class="text-[10px] text-blue-600">2.8 MB</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-medium text-gray-700">15 Jul 2026</p>
                                        <p class="text-xs text-gray-400">09:10 WITA</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-red-100 text-red-700 text-[10px] font-bold rounded-full border border-red-200">
                                            <i class="fas fa-undo mr-1.5"></i> Revisi
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button class="bg-white border border-gray-300 text-gray-600 hover:bg-gray-50 text-xs font-bold py-1.5 px-3 rounded shadow-sm transition-colors">
                                            Detail
                                        </button>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="p-4 border-t border-gray-100 flex items-center justify-between bg-white text-sm">
                        <span class="text-gray-500">Menampilkan 1 hingga 3 dari 124 laporan</span>
                        <div class="flex space-x-1">
                            <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-400 bg-gray-50 cursor-not-allowed">Sebelummya</button>
                            <button class="px-3 py-1 border border-vokasi-primary rounded-lg text-white bg-vokasi-primary font-medium">1</button>
                            <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">2</button>
                            <span class="px-2 py-1 text-gray-400">...</span>
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