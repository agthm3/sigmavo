<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGMAVO - Dashboard Portal Magang Vokasi UNHAS</title>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Palet Warna Khas Vokasi UNHAS
                        'vokasi-primary': '#37A7AC',
                        'vokasi-dark': '#29868a',
                        'vokasi-light': '#62c2c6',
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbar untuk sidebar agar rapi */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #e5e7eb;
            border-radius: 4px;
        }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb {
            background-color: #d1d5db;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased flex h-screen overflow-hidden" 
      x-data="{ sidebarOpen: false }">

    <!-- BACKDROP MOBILE SIDEBAR OVERLAY -->
    <div x-show="sidebarOpen" 
         x-cloak 
         @click="sidebarOpen = false" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 md:hidden"></div>

    <!-- SIDEBAR (Desktop Fixed, Mobile Slide-over) -->
    <aside class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-200 flex flex-col flex-shrink-0 transition-transform duration-300 ease-in-out md:static md:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
        
        <!-- Logo Brand Header Sidebar -->
        <div class="h-16 flex items-center justify-between px-6 border-b border-vokasi-dark bg-vokasi-primary text-white shadow-sm shrink-0">
            <h1 class="text-2xl font-bold tracking-wider flex items-center gap-1">
                SIGMAVO<span class="text-xs font-semibold bg-white/20 px-2 py-0.5 rounded-full ml-1">UNHAS</span>
            </h1>
            <!-- Close Button khusus Mobile -->
            <button @click="sidebarOpen = false" class="md:hidden text-white/80 hover:text-white focus:outline-none p-1">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Menu Navigation Sidebar -->
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 custom-scrollbar">
            
            <!-- DASHBOARD ANALITIK (UNTUK SEMUA ROLE) -->
            <a href="{{ route('dashboard-analitik') }}" 
               class="flex items-center px-3 py-2 text-sm rounded-lg font-medium transition-colors {{ request()->routeIs('dashboard-analitik') ? 'text-vokasi-dark bg-[#e6f4f5] font-bold border-l-4 border-vokasi-primary' : 'text-gray-700 hover:bg-gray-100 hover:text-vokasi-primary' }}">
                <i class="fas fa-chart-pie w-5"></i>
                <span class="ml-2 flex-1">Dashboard Analitik</span>
            </a>

            <!-- ======================================================== -->
            <!-- SECTION: MAHASISWA ONLY                                 -->
            <!-- ======================================================== -->
            @hasanyrole('mahasiswa')
            <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-4 mb-2">Menu Mahasiswa</p>
            
            <!-- 1. Dashboard Mahasiswa Group -->
            @php
                $isMahasiswaActive = request()->routeIs('dashboard-mahasiswa-akun', 'dashboard-mahasiswa-riwayat-magang', 'dashboard-mahasiswa-program-magang');
            @endphp
            <details class="group" {{ $isMahasiswaActive ? 'open' : '' }}>
                <summary class="flex items-center px-3 py-2 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 hover:text-vokasi-primary font-medium transition-colors {{ $isMahasiswaActive ? 'text-vokasi-primary font-bold bg-[#e6f4f5]' : '' }}">
                    <i class="fas fa-home w-5"></i>
                    <span class="ml-2 flex-1 text-sm">Dashboard Mahasiswa</span>
                    <i class="fas fa-chevron-down text-xs transition duration-300 group-open:-rotate-180"></i>
                </summary>
                <ul class="mt-1 ml-6 space-y-1 border-l-2 border-gray-200 pl-2">
                    <li>
                        <a href="{{ route('dashboard-mahasiswa-akun') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-mahasiswa-akun') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Akun
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard-mahasiswa-riwayat-magang') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-mahasiswa-riwayat-magang') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Riwayat Magang
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard-mahasiswa-program-magang') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-mahasiswa-program-magang') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Program Magang
                        </a>
                    </li>
                </ul>
            </details>

            <!-- 2. Pengajuan Magang Group -->
            @php
                $isPengajuanActive = request()->routeIs('dashboard-mahasiswa-daftar-lowongan', 'dashboard-mahasiswa-ajukan-mandiri', 'dashboard-mahasiswa-status-pengajuan');
            @endphp
            <details class="group" {{ $isPengajuanActive ? 'open' : '' }}>
                <summary class="flex items-center px-3 py-2 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 hover:text-vokasi-primary font-medium transition-colors {{ $isPengajuanActive ? 'text-vokasi-primary font-bold bg-[#e6f4f5]' : '' }}">
                    <i class="fas fa-paper-plane w-5"></i>
                    <span class="ml-2 flex-1 text-sm">Pengajuan Magang</span>
                    <i class="fas fa-chevron-down text-xs transition duration-300 group-open:-rotate-180"></i>
                </summary>
                <ul class="mt-1 ml-6 space-y-1 border-l-2 border-gray-200 pl-2">
                    <li>
                        <a href="{{ route('dashboard-mahasiswa-daftar-lowongan') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-mahasiswa-daftar-lowongan') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Daftar Lowongan
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard-mahasiswa-ajukan-mandiri') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-mahasiswa-ajukan-mandiri') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Ajukan Mandiri
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard-mahasiswa-status-pengajuan') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-mahasiswa-status-pengajuan') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Status Pengajuan
                        </a>
                    </li>
                </ul>
            </details>

            <!-- 3. Pelaksanaan Magang Group -->
            @php
                $isPelaksanaanActive = request()->routeIs('dashboard-mahasiswa-pembekalan-magang', 'dashboard-mahasiswa-absensi', 'dashboard-mahasiswa-logbook', 'dashboard-mahasiswa-seminar');
            @endphp
            <details class="group" {{ $isPelaksanaanActive ? 'open' : '' }}>
                <summary class="flex items-center px-3 py-2 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 hover:text-vokasi-primary font-medium transition-colors {{ $isPelaksanaanActive ? 'text-vokasi-primary font-bold bg-[#e6f4f5]' : '' }}">
                    <i class="fas fa-running w-5"></i>
                    <span class="ml-2 flex-1 text-sm">Pelaksanaan Magang</span>
                    <i class="fas fa-chevron-down text-xs transition duration-300 group-open:-rotate-180"></i>
                </summary>
                <ul class="mt-1 ml-6 space-y-1 border-l-2 border-gray-200 pl-2">
                    <li>
                        <a href="{{ route('dashboard-mahasiswa-pembekalan-magang') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-mahasiswa-pembekalan-magang') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Pembekalan Magang
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard-mahasiswa-absensi') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-mahasiswa-absensi') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Absensi
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard-mahasiswa-logbook') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-mahasiswa-logbook') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Logbook
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard-mahasiswa-seminar') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-mahasiswa-seminar') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Seminar
                        </a>
                    </li>
                </ul>
            </details>

            <!-- 4. Pelaporan Magang Group -->
            @php
                $isPelaporanActive = request()->routeIs('dashboard-pelaporan-download-template', 'dashboard-pelaporan-upload-dokumen', 'dashboard-mahasiswa-laporan-akhir');
            @endphp
            <details class="group" {{ $isPelaporanActive ? 'open' : '' }}>
                <summary class="flex items-center px-3 py-2 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 hover:text-vokasi-primary font-medium transition-colors {{ $isPelaporanActive ? 'text-vokasi-primary font-bold bg-[#e6f4f5]' : '' }}">
                    <i class="fas fa-file-alt w-5"></i>
                    <span class="ml-2 flex-1 text-sm">Pelaporan Magang</span>
                    <i class="fas fa-chevron-down text-xs transition duration-300 group-open:-rotate-180"></i>
                </summary>
                <ul class="mt-1 ml-6 space-y-1 border-l-2 border-gray-200 pl-2">
                    <li>
                        <a href="{{ route('dashboard-pelaporan-download-template') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-pelaporan-download-template') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Download Template
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard-pelaporan-upload-dokumen') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-pelaporan-upload-dokumen') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Upload Dokumen
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard-mahasiswa-laporan-akhir') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-mahasiswa-laporan-akhir') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Laporan Akhir
                        </a>
                    </li>
                </ul>
            </details>
            @endhasanyrole

            <!-- ======================================================== -->
            <!-- SECTION: SUPERVISOR LAPANGAN (SPV) & DOSEN               -->
            <!-- ======================================================== -->
            @hasanyrole('dosen|dosen_pembimbing|spv')
            <hr class="my-4 border-gray-200">
            <p class="px-3 text-xs font-semibold text-vokasi-primary uppercase tracking-wider mb-2">Pemeriksaan & Bimbingan</p>

            @php
                $isVerifikasiActive = request()->routeIs('dashboard-dosen-perlu-verifikasi', 'dashboard-dosen-terverifikasi');
            @endphp
            <a href="{{ route('dashboard-dosen-perlu-verifikasi') }}" 
               class="flex items-center px-3 py-2 text-sm rounded-lg font-medium transition-colors {{ $isVerifikasiActive ? 'text-vokasi-dark bg-[#e6f4f5] font-bold border-l-4 border-vokasi-primary' : 'text-gray-700 hover:bg-gray-100 hover:text-vokasi-primary' }}">
                <i class="fas fa-check-circle w-5"></i>
                <span class="ml-2 flex-1">Perlu Verifikasi</span>
                <span class="px-2 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-800 rounded-full">Antrean</span>
            </a>

            @hasanyrole('dosen|dosen_pembimbing')
            @php
                $isDosenActive = request()->routeIs('dashboard-dosen-mahasiswa-bimbingan', 'dashboard-dosen-daftar-mahasiswa');
            @endphp
            <details class="group mt-1" {{ $isDosenActive ? 'open' : '' }}>
                <summary class="flex items-center px-3 py-2 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 hover:text-vokasi-primary font-medium transition-colors {{ $isDosenActive ? 'text-vokasi-primary font-bold bg-[#e6f4f5]' : '' }}">
                    <i class="fas fa-chalkboard-teacher w-5"></i>
                    <span class="ml-2 flex-1 text-sm">Dashboard Dosen</span>
                    <i class="fas fa-chevron-down text-xs transition duration-300 group-open:-rotate-180"></i>
                </summary>
                <ul class="mt-1 ml-6 space-y-1 border-l-2 border-gray-200 pl-2">
                    <li>
                        <a href="{{ route('dashboard-dosen-mahasiswa-bimbingan') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-dosen-mahasiswa-bimbingan') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Mahasiswa Bimbingan
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard-dosen-daftar-mahasiswa') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-dosen-daftar-mahasiswa') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Daftar Mahasiswa
                        </a>
                    </li>
                </ul>
            </details>
            @endhasanyrole  
            @endhasanyrole

            <!-- ======================================================== -->
            <!-- SECTION: DOSEN, ADMIN PRODI, ADMIN & SUPERADMIN           -->
            <!-- ======================================================== -->
            @hasanyrole('dosen|admin_prodi|admin|superadmin')
            @unlessrole('mahasiswa')
            <hr class="my-4 border-gray-200">
            <p class="px-3 text-xs font-semibold text-vokasi-primary uppercase tracking-wider mb-2">Pemeriksaan & Penilaian</p>

            <!-- Verifikasi Group -->
            @php
                $isVerifikasiActive = request()->routeIs('dashboard-verifikasi-daftar-mahasiswa-semua-laporan', 'dashboard-verifikasi-daftar-mahasiswa-perlu-verifikasi', 'dashboard-verifikasi-daftar-mahasiswa-terverifikasi');
            @endphp
            <details class="group" {{ $isVerifikasiActive ? 'open' : '' }}>
                <summary class="flex items-center px-3 py-2 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 hover:text-vokasi-primary font-medium transition-colors {{ $isVerifikasiActive ? 'text-vokasi-primary font-bold bg-[#e6f4f5]' : '' }}">
                    <i class="fas fa-check-double w-5"></i>
                    <span class="ml-2 flex-1 text-sm">Verifikasi</span>
                    <i class="fas fa-chevron-down text-xs transition duration-300 group-open:-rotate-180"></i>
                </summary>
                <ul class="mt-1 ml-6 space-y-1 border-l-2 border-gray-200 pl-2">
                    <li>
                        <a href="{{ route('dashboard-verifikasi-daftar-mahasiswa-semua-laporan') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-verifikasi-daftar-mahasiswa-semua-laporan') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Semua Laporan
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard-verifikasi-daftar-mahasiswa-perlu-verifikasi') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-verifikasi-daftar-mahasiswa-perlu-verifikasi') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Perlu Verifikasi
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard-verifikasi-daftar-mahasiswa-terverifikasi') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-verifikasi-daftar-mahasiswa-terverifikasi') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Terverifikasi
                        </a>
                    </li>
                </ul>
            </details>

            <!-- Penilaian Group -->
            @php
                $isPenilaianActive = request()->routeIs('dashboard-penilaian-listing-mahasiswa');
            @endphp
            <details class="group" {{ $isPenilaianActive ? 'open' : '' }}>
                <summary class="flex items-center px-3 py-2 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 hover:text-vokasi-primary font-medium transition-colors {{ $isPenilaianActive ? 'text-vokasi-primary font-bold bg-[#e6f4f5]' : '' }}">
                    <i class="fas fa-star w-5"></i>
                    <span class="ml-2 flex-1 text-sm">Penilaian</span>
                    <i class="fas fa-chevron-down text-xs transition duration-300 group-open:-rotate-180"></i>
                </summary>
                <ul class="mt-1 ml-6 space-y-1 border-l-2 border-gray-200 pl-2">
                    <li>
                        <a href="{{ route('dashboard-penilaian-listing-mahasiswa') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-penilaian-listing-mahasiswa') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Listing Mahasiswa
                        </a>
                    </li>
                </ul>
            </details>
            @endunlessrole
            @endhasanyrole

            <!-- ======================================================== -->
            <!-- SECTION: ADMIN PRODI, ADMIN & SUPERADMIN ONLY             -->
            <!-- ======================================================== -->
            @hasanyrole('admin_prodi|admin|superadmin')
            <hr class="my-4 border-gray-200">
            <p class="px-3 text-xs font-semibold text-vokasi-primary uppercase tracking-wider mb-2">Sistem & Kemitraan</p>

            <!-- Manajemen Akun Group -->
            @php
                $isManajemenAkunActive = request()->routeIs('dashboard-manajemen-aktivasi-user', 'dashboard-manajemen-jenis-role', 'dashboard-manajemen-pengaturan', 'dashboard-manajemen-rubrik-penilaian');
            @endphp
            <details class="group" {{ $isManajemenAkunActive ? 'open' : '' }}>
                <summary class="flex items-center px-3 py-2 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 hover:text-vokasi-primary font-medium transition-colors {{ $isManajemenAkunActive ? 'text-vokasi-primary font-bold bg-[#e6f4f5]' : '' }}">
                    <i class="fas fa-users-cog w-5"></i>
                    <span class="ml-2 flex-1 text-sm">Manajemen Akun</span>
                    <i class="fas fa-chevron-down text-xs transition duration-300 group-open:-rotate-180"></i>
                </summary>
                <ul class="mt-1 ml-6 space-y-1 border-l-2 border-gray-200 pl-2">
                    <li>
                        <a href="{{ route('dashboard-manajemen-aktivasi-user') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-manajemen-aktivasi-user') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Aktivasi User
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard-manajemen-jenis-role') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-manajemen-jenis-role') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Jenis User (Role)
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard-manajemen-pengaturan') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-manajemen-pengaturan') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Pengaturan
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard-manajemen-rubrik-penilaian') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-manajemen-rubrik-penilaian') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Rubrik Penilaian
                        </a>
                    </li>
                </ul>
            </details>

            <!-- Daftar Lowongan & Pelaksanaan Group (Admin) -->
            @php
                $isDaftarLowonganHRActive = request()->routeIs('dashboard-daftar-lowongan-daftar-perusahaan', 'dashboard-daftar-lowongan-listing-program', 'dashboard-daftar-lowongan-seleksi', 'dashboard-daftar-lowongan-pengajuan-magang', 'dashboard-mahasiswa-pembekalan-magang');
            @endphp
            <details class="group" {{ $isDaftarLowonganHRActive ? 'open' : '' }}>
                <summary class="flex items-center px-3 py-2 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 hover:text-vokasi-primary font-medium transition-colors {{ $isDaftarLowonganHRActive ? 'text-vokasi-primary font-bold bg-[#e6f4f5]' : '' }}">
                    <i class="fas fa-briefcase w-5"></i>
                    <span class="ml-2 flex-1 text-sm">Daftar Lowongan & Agenda</span>
                    <i class="fas fa-chevron-down text-xs transition duration-300 group-open:-rotate-180"></i>
                </summary>
                <ul class="mt-1 ml-6 space-y-1 border-l-2 border-gray-200 pl-2">
                    <li>
                        <a href="{{ route('dashboard-daftar-lowongan-daftar-perusahaan') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-daftar-lowongan-daftar-perusahaan') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Daftar Perusahaan
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard-daftar-lowongan-listing-program') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-daftar-lowongan-listing-program') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Listing Program
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard-daftar-lowongan-seleksi') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-daftar-lowongan-seleksi') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Seleksi
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard-daftar-lowongan-pengajuan-magang') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-daftar-lowongan-pengajuan-magang') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Pengajuan Magang
                        </a>
                    </li>
                    <!-- TAMBAHAN TERBARU: MENU PEMBEKALAN MAGANG UNTUK ADMIN PRODI / ADMIN -->
                    <li>
                        <a href="{{ route('dashboard-mahasiswa-pembekalan-magang') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-mahasiswa-pembekalan-magang') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            <i class="fas fa-calendar-alt mr-1 text-vokasi-primary text-xs"></i> Pembekalan Magang
                        </a>
                    </li>
                </ul>
            </details>
            @endhasanyrole

            <!-- TOMBOL LOGOUT DI FOOTER SIDEBAR -->
            <div class="p-4 border-t border-gray-200/80 bg-white">
                <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari sistem?')">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs rounded-xl transition-colors border border-red-200/60 shadow-sm">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Keluar / Logout</span>
                    </button>
                </form>
            </div>

        </nav>
    </aside>

    <!-- MAIN CONTENT CONTAINER -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50 min-w-0">
        
        <!-- RESPONSIVE TOPBAR HEADER -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 lg:px-6 shrink-0 shadow-sm z-30">
            
            <div class="flex items-center gap-3">
                <!-- Hamburger Menu Button untuk Mobile -->
                <button @click="sidebarOpen = !sidebarOpen" 
                        class="md:hidden text-gray-600 hover:text-vokasi-primary focus:outline-none p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                
                <!-- Dynamic Breadcrumb Navigation -->
                <nav class="flex items-center text-xs md:text-sm font-medium text-gray-500 space-x-1.5 md:space-x-2 truncate">
                    <span class="hover:text-vokasi-primary hidden sm:inline">Dashboard</span>
                    <span class="hidden sm:inline">/</span>
                    <span class="text-gray-800 font-semibold truncate">Portal Magang</span>
                </nav>
            </div>

            <!-- Profile & Notifications Area -->
            <div class="flex items-center space-x-3 md:space-x-4">
                <button class="text-gray-500 hover:text-vokasi-primary transition-colors p-2 rounded-lg hover:bg-gray-100 relative" title="Notifikasi">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                </button>

@php
                    $authUser = Auth::user();
                    $userName = $authUser->name ?? 'Pengguna';
                    $userRole = $authUser ? ($authUser->getRoleNames()->first() ?? 'Pengguna') : 'Pengguna';
                    
                    // Ambil nama prodi dari relasi Eloquent
                    $namaProdi = null;
                    if ($authUser) {
                        if ($authUser->hasRole('mahasiswa')) {
                            $namaProdi = $authUser->mahasiswaProfile?->prodi?->nama_prodi;
                        } elseif ($authUser->hasRole('dosen')) {
                            $namaProdi = $authUser->dosenProfile?->prodi?->nama_prodi;
                        } elseif ($authUser->hasRole('admin_prodi')) {
                            $namaProdi = $authUser->adminProdiProfile?->prodi?->nama_prodi;
                        }
                    }
                @endphp

                <div class="flex items-center space-x-2.5 border-l pl-3 md:pl-4 border-gray-200">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($userName) }}&background=37A7AC&color=fff" 
                         alt="Profile" 
                         class="w-8 h-8 md:w-9 md:h-9 rounded-full border border-gray-200 shadow-sm shrink-0">
                    
                    <div class="hidden sm:block text-sm text-right">
                        <p class="font-bold text-gray-800 leading-tight text-xs md:text-sm">{{ $userName }}</p>
                        <p class="text-[10px] font-semibold text-vokasi-primary capitalize mt-0.5">
                            {{ $namaProdi ?? str_replace('_', ' ', $userRole) }}
                        </p>
                    </div>
                </div>
            </div>

        </header>

        <!-- CONTENT VIEW -->
        @yield('content')

    </div>
@if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{!! session('success') !!}",
            confirmButtonColor: '#37A7AC'
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal / Ditolak!',
            text: "{!! session('error') !!}",
            confirmButtonColor: '#37A7AC'
        });
    </script>
    @endif

    @if($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan!',
            html: `
                <ul class="text-left text-sm text-red-600 mt-2">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            `,
            confirmButtonColor: '#37A7AC'
        });
    </script>
    @endif
</body>
</html>