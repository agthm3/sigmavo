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
                    <a href="#" class="hover:text-vokasi-primary">Penilaian</a>
                    <span>/</span>
                    <span class="text-gray-800">Listing Mahasiswa</span>
                </nav>
            </div>
            <div class="flex items-center space-x-4">
                <button class="text-gray-500 hover:text-vokasi-primary transition-colors relative" title="Notifikasi">
                    <i class="fas fa-bell text-lg"></i>
                </button>
                <div class="flex items-center space-x-2 cursor-pointer border-l pl-4 border-gray-200">
                    <img src="https://ui-avatars.com/api/?name=Admin+Prodi&background=37A7AC&color=fff" alt="Profile" class="w-8 h-8 rounded-full border border-gray-200 shadow-sm">
                    <div class="hidden md:block text-sm text-right">
                        <p class="font-bold text-gray-700 leading-tight">Admin Prodi / Dosen</p>
                        <p class="text-xs text-vokasi-primary font-semibold">Vokasi UNHAS</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative">
            
            <div class="max-w-7xl mx-auto w-full flex-1 space-y-6">
                
                <!-- Page Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Rekapitulasi Penilaian Akhir Magang</h2>
                        <p class="text-sm text-gray-500 mt-1">Sistem otomatis menghitung gabungan nilai Supervisor Lapangan dan Dosen Penguji.</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition-colors shadow-sm flex items-center">
                            <i class="fas fa-file-excel mr-2"></i> Export Transkrip Nilai
                        </button>
                    </div>
                </div>

                <!-- SUMMARY CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-user-graduate text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Total Mahasiswa</p>
                            <p class="text-xl font-bold text-gray-800 leading-none mt-1">45</p>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-check-circle text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Nilai Lengkap</p>
                            <p class="text-xl font-bold text-green-600 leading-none mt-1">38</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-yellow-50 text-yellow-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-clock text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Belum Lengkap</p>
                            <p class="text-xl font-bold text-yellow-600 leading-none mt-1">7</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-chart-line text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Rata-rata Nilai</p>
                            <p class="text-xl font-bold text-purple-600 leading-none mt-1">87.5 <span class="text-xs text-gray-400 font-normal">(A)</span></p>
                        </div>
                    </div>
                </div>

                <!-- TABLE SECTION -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    
                    <!-- Toolbar -->
                    <div class="p-5 border-b border-gray-100 flex flex-col lg:flex-row justify-between lg:items-center gap-4 bg-gray-50/50">
                        <div class="relative w-full lg:w-96">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                            <input type="text" placeholder="Cari nama, NIM, atau perusahaan..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary">
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            <select class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option>Semua Program Studi</option>
                                <option>D4 Teknologi Produksi Pertanian</option>
                                <option>D4 Terapi Gigi</option>
                                <option>D4 Agribisnis</option>
                            </select>

                            <select class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option>Semua Status Penilaian</option>
                                <option>Lengkap (Siap Transkrip)</option>
                                <option>Belum Input Dosen</option>
                                <option>Belum Input Supervisor</option>
                            </select>
                        </div>
                    </div>

                    <!-- Responsive Table -->
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[1100px]">
                            <thead>
                                <tr class="bg-gray-100/60 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <th class="p-4 w-12 text-center">No</th>
                                    <th class="p-4 w-56">Mahasiswa</th>
                                    <th class="p-4 w-48">Instansi Penempatan</th>
                                    <th class="p-4 w-32 text-center">Nilai Supervisor<br><span class="text-[10px] text-gray-400 font-normal">(Bobot 40%)</span></th>
                                    <th class="p-4 w-32 text-center">Nilai Dosen/Seminar<br><span class="text-[10px] text-gray-400 font-normal">(Bobot 60%)</span></th>
                                    <th class="p-4 w-32 text-center">Nilai Akhir</th>
                                    <th class="p-4 w-32 text-center">Status</th>
                                    <th class="p-4 w-28 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                
                                <!-- Baris 1: Lengkap (Nilai A) -->
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-center text-gray-500 font-medium">1</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name=Fadehl+Thristansyah&background=f3f4f6&color=37A7AC" alt="Avatar" class="w-9 h-9 rounded-full border border-gray-200">
                                            <div>
                                                <p class="font-bold text-gray-800">Fadehl Thristansyah</p>
                                                <p class="text-xs text-gray-500">H071231012</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-semibold text-gray-800">PT. SmartPlay Inovasi</p>
                                        <p class="text-xs text-gray-500">Supervisor: Andi Setiawan</p>
                                    </td>
                                    <td class="p-4 text-center font-bold text-gray-700 bg-gray-50/50">
                                        92.00
                                    </td>
                                    <td class="p-4 text-center font-bold text-gray-700 bg-gray-50/50">
                                        88.50
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="inline-block px-3 py-1 bg-green-50 border border-green-200 rounded-lg">
                                            <span class="font-extrabold text-green-700 text-base">89.90</span>
                                            <span class="ml-1 font-bold text-green-800 text-xs">(A)</span>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full border border-green-200">
                                            <i class="fas fa-check-circle mr-1"></i> Final
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button class="bg-vokasi-primary hover:bg-vokasi-dark text-white text-xs font-bold p-2 rounded-lg transition-colors shadow-sm" title="Edit / Lihat Lembar Nilai">
                                            <i class="fas fa-edit"></i> Input/Edit
                                        </button>
                                    </td>
                                </tr>

                                <!-- Baris 2: Lengkap (Nilai A-) -->
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-center text-gray-500 font-medium">2</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name=Andi+Reza&background=f3f4f6&color=37A7AC" alt="Avatar" class="w-9 h-9 rounded-full border border-gray-200">
                                            <div>
                                                <p class="font-bold text-gray-800">Andi Reza Syahputra</p>
                                                <p class="text-xs text-gray-500">H071231045</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-semibold text-gray-800">BRIDA Kota Makassar</p>
                                        <p class="text-xs text-gray-500">Supervisor: Kadis BRIDA</p>
                                    </td>
                                    <td class="p-4 text-center font-bold text-gray-700 bg-gray-50/50">
                                        85.00
                                    </td>
                                    <td class="p-4 text-center font-bold text-gray-700 bg-gray-50/50">
                                        82.00
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="inline-block px-3 py-1 bg-green-50 border border-green-200 rounded-lg">
                                            <span class="font-extrabold text-green-700 text-base">83.20</span>
                                            <span class="ml-1 font-bold text-green-800 text-xs">(A-)</span>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full border border-green-200">
                                            <i class="fas fa-check-circle mr-1"></i> Final
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button class="bg-vokasi-primary hover:bg-vokasi-dark text-white text-xs font-bold p-2 rounded-lg transition-colors shadow-sm" title="Edit / Lihat Lembar Nilai">
                                            <i class="fas fa-edit"></i> Input/Edit
                                        </button>
                                    </td>
                                </tr>

                                <!-- Baris 3: Belum Lengkap (Nilai Dosen Belum Masuk) -->
                                <tr class="hover:bg-gray-50 transition-colors bg-yellow-50/20">
                                    <td class="p-4 text-center text-gray-500 font-medium">3</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name=Siti+Nurhaliza&background=f3f4f6&color=37A7AC" alt="Avatar" class="w-9 h-9 rounded-full border border-gray-200">
                                            <div>
                                                <p class="font-bold text-gray-800">Siti Nurhaliza</p>
                                                <p class="text-xs text-gray-500">H071231088</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-semibold text-gray-800">PT. Agro Nusantara</p>
                                        <p class="text-xs text-gray-500">Supervisor: Hendra S.</p>
                                    </td>
                                    <td class="p-4 text-center font-bold text-gray-700 bg-gray-50/50">
                                        88.00
                                    </td>
                                    <td class="p-4 text-center font-bold text-red-500 italic bg-gray-50/50">
                                        Belum Ada
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="text-xs text-gray-400 font-mono">--</span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-yellow-100 text-yellow-700 text-[10px] font-bold rounded-full border border-yellow-200">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> Belum Lengkap
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button class="bg-vokasi-primary hover:bg-vokasi-dark text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-colors shadow-sm">
                                            Input Nilai
                                        </button>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Controls -->
                    <div class="p-4 border-t border-gray-100 flex items-center justify-between bg-white text-sm">
                        <span class="text-gray-500">Menampilkan 1 hingga 3 dari 45 mahasiswa</span>
                        <div class="flex space-x-1">
                            <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-400 bg-gray-50 cursor-not-allowed">Sebelummya</button>
                            <button class="px-3 py-1 border border-vokasi-primary rounded-lg text-white bg-vokasi-primary font-medium">1</button>
                            <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">2</button>
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
    </div>
@endsection