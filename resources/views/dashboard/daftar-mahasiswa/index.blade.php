@extends('layouts.dashboard')

@section('content')
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative">
            
            <div class="max-w-7xl mx-auto w-full flex-1 space-y-6">
                
                <!-- Page Header & Stats -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Direktori Mahasiswa</h2>
                        <p class="text-sm text-gray-500 mt-1">Kelola dan pantau seluruh daftar mahasiswa magang di bawah bimbingan Anda.</p>
                    </div>
                    
                    <div class="flex gap-3 bg-white px-4 py-2 border border-gray-200 rounded-lg shadow-sm text-sm">
                        <div class="flex items-center gap-2 border-r border-gray-200 pr-4">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            <span class="text-gray-600">Aktif: <strong>6</strong></span>
                        </div>
                        <div class="flex items-center gap-2 border-r border-gray-200 pr-4">
                            <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                            <span class="text-gray-600">Selesai: <strong>2</strong></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-gray-800">Total: 8</span>
                        </div>
                    </div>
                </div>

                <!-- TABEL MAHASISWA -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    
                    <!-- Table Toolbar -->
                    <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row justify-between md:items-center gap-4 bg-gray-50/50">
                        <div class="relative w-full md:w-80">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                            <input type="text" placeholder="Cari nama, NIM, atau perusahaan..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary">
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            <select class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option>Semua Status</option>
                                <option>Aktif Magang</option>
                                <option>Selesai Magang</option>
                                <option>Menunggu Penempatan</option>
                            </select>
                            <button class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-semibold py-2 px-3 rounded-lg transition-colors shadow-sm flex items-center">
                                <i class="fas fa-filter mr-1.5"></i> Filter Lanjutan
                            </button>
                            <button class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 px-3 rounded-lg transition-colors shadow-sm flex items-center">
                                <i class="fas fa-file-excel mr-1.5"></i> Export
                            </button>
                        </div>
                    </div>

                    <!-- Responsive Table -->
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[1000px]">
                            <thead>
                                <tr class="bg-gray-100/60 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <th class="p-4 w-12 text-center">No</th>
                                    <th class="p-4 w-64">Identitas Mahasiswa</th>
                                    <th class="p-4 w-64">Penempatan / Perusahaan</th>
                                    <th class="p-4 w-48">Progres 900 Jam</th>
                                    <th class="p-4 w-32">Status</th>
                                    <th class="p-4 w-28 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                
                                <!-- Baris 1: Aktif -->
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-center text-gray-500 font-medium">1</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name=Fadehl+Thristansyah&background=f3f4f6&color=37A7AC" alt="Avatar" class="w-10 h-10 rounded-full border border-gray-200">
                                            <div>
                                                <p class="font-bold text-gray-800">Fadehl Thristansyah</p>
                                                <p class="text-xs text-gray-500">H071231012</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-semibold text-gray-800">PT. SmartPlay Inovasi</p>
                                        <p class="text-xs text-gray-500">Pengembang Edukasi STEM</p>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex justify-between text-xs mb-1">
                                            <span class="font-bold text-vokasi-primary">140 Jam</span>
                                            <span class="text-gray-500">15%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-vokasi-primary h-1.5 rounded-full" style="width: 15%"></div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full border border-green-200">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span> Aktif
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button class="text-gray-500 hover:text-vokasi-primary bg-gray-100 hover:bg-gray-200 p-2 rounded-lg transition-colors text-xs font-semibold">
                                            <i class="fas fa-user-circle mr-1"></i> Profil
                                        </button>
                                    </td>
                                </tr>

                                <!-- Baris 2: Aktif -->
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-center text-gray-500 font-medium">2</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name=Andi+Reza&background=f3f4f6&color=6b7280" alt="Avatar" class="w-10 h-10 rounded-full border border-gray-200">
                                            <div>
                                                <p class="font-bold text-gray-800">Andi Reza Syahputra</p>
                                                <p class="text-xs text-gray-500">H071231045</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-semibold text-gray-800">BRIDA Kota Makassar</p>
                                        <p class="text-xs text-gray-500">Staf Analis Data</p>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex justify-between text-xs mb-1">
                                            <span class="font-bold text-vokasi-primary">320 Jam</span>
                                            <span class="text-gray-500">35%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-vokasi-primary h-1.5 rounded-full" style="width: 35%"></div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full border border-green-200">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span> Aktif
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button class="text-gray-500 hover:text-vokasi-primary bg-gray-100 hover:bg-gray-200 p-2 rounded-lg transition-colors text-xs font-semibold">
                                            <i class="fas fa-user-circle mr-1"></i> Profil
                                        </button>
                                    </td>
                                </tr>

                                <!-- Baris 3: Aktif -->
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-center text-gray-500 font-medium">3</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name=Siti+Nurhaliza&background=f3f4f6&color=6b7280" alt="Avatar" class="w-10 h-10 rounded-full border border-gray-200">
                                            <div>
                                                <p class="font-bold text-gray-800">Siti Nurhaliza</p>
                                                <p class="text-xs text-gray-500">H071231088</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-semibold text-gray-800">PT. Agro Nusantara</p>
                                        <p class="text-xs text-gray-500">Quality Control</p>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex justify-between text-xs mb-1">
                                            <span class="font-bold text-orange-600">80 Jam</span>
                                            <span class="text-gray-500">8%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-orange-500 h-1.5 rounded-full" style="width: 8%"></div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full border border-green-200">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span> Aktif
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button class="text-gray-500 hover:text-vokasi-primary bg-gray-100 hover:bg-gray-200 p-2 rounded-lg transition-colors text-xs font-semibold">
                                            <i class="fas fa-user-circle mr-1"></i> Profil
                                        </button>
                                    </td>
                                </tr>

                                <!-- Baris 4: Selesai -->
                                <tr class="hover:bg-gray-50 transition-colors bg-gray-50/50">
                                    <td class="p-4 text-center text-gray-500 font-medium">4</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name=Budi+Pratama&background=e5e7eb&color=6b7280" alt="Avatar" class="w-10 h-10 rounded-full border border-gray-200 opacity-80">
                                            <div>
                                                <p class="font-bold text-gray-700">Budi Pratama</p>
                                                <p class="text-xs text-gray-500">H071231005</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-semibold text-gray-700">PT. Inovasi Teknologi</p>
                                        <p class="text-xs text-gray-500">Frontend Developer</p>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex justify-between text-xs mb-1">
                                            <span class="font-bold text-green-600">900 Jam</span>
                                            <span class="text-gray-500">100%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-green-500 h-1.5 rounded-full" style="width: 100%"></div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-600 text-[10px] font-bold rounded-full border border-gray-300">
                                            <i class="fas fa-check-double mr-1"></i> Selesai
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button class="text-gray-500 hover:text-vokasi-primary bg-gray-100 hover:bg-gray-200 p-2 rounded-lg transition-colors text-xs font-semibold">
                                            <i class="fas fa-user-circle mr-1"></i> Profil
                                        </button>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Controls -->
                    <div class="p-4 border-t border-gray-100 flex items-center justify-between bg-white text-sm">
                        <span class="text-gray-500">Menampilkan 1 hingga 4 dari 8 mahasiswa</span>
                        <div class="flex space-x-1">
                            <button class="px-3 py-1 border border-gray-200 rounded-lg text-gray-400 bg-gray-50 cursor-not-allowed">Sebelummya</button>
                            <button class="px-3 py-1 border border-vokasi-primary rounded-lg text-white bg-vokasi-primary font-medium">1</button>
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