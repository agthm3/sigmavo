<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGMAVO - Dashboard</title>
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
        /* Custom scrollbar untuk sidebar agar lebih rapi */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
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
<body class="bg-gray-50 text-gray-800 font-sans antialiased flex h-screen overflow-hidden">

    <!-- SIDEBAR (Mobile Hidden by default, Desktop Visible) -->
    <aside class="bg-white w-72 h-full border-r border-gray-200 flex flex-col hidden md:flex flex-shrink-0 z-20 transition-all duration-300">
        <!-- Logo Brand -->
        <div class="h-16 flex items-center justify-center border-b border-vokasi-dark bg-vokasi-primary text-white shadow-sm">
            <h1 class="text-2xl font-bold tracking-wider">SIGMAVO<span class="text-sm font-normal ml-2">UNHAS</span></h1>
        </div>

        <!-- Menu Sidebar -->
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 custom-scrollbar">
            
            <!-- DASHBOARD ANALITIK -->
            <a href="{{ route('dashboard-analitik') }}" 
               class="flex items-center px-3 py-2 text-sm rounded-lg font-medium transition-colors {{ request()->routeIs('dashboard-analitik') ? 'text-vokasi-dark bg-[#e6f4f5] font-bold border-l-4 border-vokasi-primary' : 'text-gray-700 hover:bg-gray-100 hover:text-vokasi-primary' }}">
                <i class="fas fa-chart-pie w-5"></i>
                <span class="ml-2 flex-1">Dashboard Analitik</span>
            </a>

            <!-- SECTION: MAHASISWA -->
            <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-4 mb-2">Menu Mahasiswa</p>
            
            <!-- 1. Dashboard Mahasiswa Group -->
            @php
                $isMahasiswaActive = request()->routeIs('dashboard-mahasiswa-akun', 'dashboard-mahasiswa-riwayat-magang', 'dashboard-mahasiswa-program-magang');
            @endphp
            <details class="group" {{ $isMahasiswaActive ? 'open' : '' }}>
                <summary class="flex items-center px-3 py-2 text-gray-700 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 hover:text-vokasi-primary font-medium transition-colors {{ $isMahasiswaActive ? 'text-vokasi-primary font-bold bg-[#e6f4f5]' : '' }}">
                    <i class="fas fa-home w-5"></i>
                    <span class="ml-2 flex-1">Dashboard Mahasiswa</span>
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
                    <span class="ml-2 flex-1">Pengajuan Magang</span>
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
                    <span class="ml-2 flex-1">Pelaksanaan Magang</span>
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
                $isPelaporanActive = request()->routeIs(
                    'dashboard-pelaporan-download-template',
                    'dashboard-pelaporan-upload-dokumen'
                );
            @endphp
            <details class="group" {{ $isPelaporanActive ? 'open' : '' }}>
                <summary class="flex items-center px-3 py-2 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 hover:text-vokasi-primary font-medium transition-colors {{ $isPelaporanActive ? 'text-vokasi-primary font-bold bg-[#e6f4f5]' : '' }}">
                    <i class="fas fa-file-alt w-5"></i>
                    <span class="ml-2 flex-1">Pelaporan Magang</span>
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
                </ul>
            </details>

            
            <!-- SECTION: DOSEN / ADMIN / HR -->
            <hr class="my-4 border-gray-200">
            <p class="px-3 text-xs font-semibold text-vokasi-primary uppercase tracking-wider mb-2">Manajemen & HR</p>

            <!-- 5. Dashboard Dosen Group -->
            @php
                $isDosenActive = request()->routeIs('dashboard-dosen-mahasiswa-bimbingan', 'dashboard-dosen-perlu-verifikasi', 'dashboard-dosen-terverifikasi', 'dashboard-dosen-daftar-mahasiswa');
            @endphp
            <details class="group" {{ $isDosenActive ? 'open' : '' }}>
                <summary class="flex items-center px-3 py-2 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 hover:text-vokasi-primary font-medium transition-colors {{ $isDosenActive ? 'text-vokasi-primary font-bold bg-[#e6f4f5]' : '' }}">
                    <i class="fas fa-chalkboard-teacher w-5"></i>
                    <span class="ml-2 flex-1">Dashboard Dosen</span>
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
                        <a href="{{ route('dashboard-dosen-perlu-verifikasi') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-dosen-perlu-verifikasi') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Perlu Verifikasi
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard-dosen-terverifikasi') }}" 
                           class="block px-3 py-2 text-sm rounded-md transition-colors {{ request()->routeIs('dashboard-dosen-terverifikasi') ? 'text-vokasi-primary font-bold bg-gray-100' : 'text-gray-600 hover:text-vokasi-primary' }}">
                            Terverifikasi
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

            <!-- 6. Verifikasi Group -->
            @php
                $isVerifikasiActive = request()->routeIs('dashboard-verifikasi-daftar-mahasiswa-semua-laporan', 'dashboard-verifikasi-daftar-mahasiswa-perlu-verifikasi', 'dashboard-verifikasi-daftar-mahasiswa-terverifikasi');
            @endphp
            <details class="group" {{ $isVerifikasiActive ? 'open' : '' }}>
                <summary class="flex items-center px-3 py-2 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 hover:text-vokasi-primary font-medium transition-colors {{ $isVerifikasiActive ? 'text-vokasi-primary font-bold bg-[#e6f4f5]' : '' }}">
                    <i class="fas fa-check-double w-5"></i>
                    <span class="ml-2 flex-1">Verifikasi</span>
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

            <!-- 7. Penilaian Group -->
            @php
                $isPenilaianActive = request()->routeIs('dashboard-penilaian-listing-mahasiswa');
            @endphp
            <details class="group" {{ $isPenilaianActive ? 'open' : '' }}>
                <summary class="flex items-center px-3 py-2 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 hover:text-vokasi-primary font-medium transition-colors {{ $isPenilaianActive ? 'text-vokasi-primary font-bold bg-[#e6f4f5]' : '' }}">
                    <i class="fas fa-star w-5"></i>
                    <span class="ml-2 flex-1">Penilaian</span>
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

            <!-- 8. Manajemen Akun Group -->
            <details class="group">
                <summary class="flex items-center px-3 py-2 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 hover:text-vokasi-primary font-medium transition-colors">
                    <i class="fas fa-users-cog w-5"></i>
                    <span class="ml-2 flex-1">Manajemen Akun</span>
                    <i class="fas fa-chevron-down text-xs transition duration-300 group-open:-rotate-180"></i>
                </summary>
                <ul class="mt-1 ml-6 space-y-1 border-l-2 border-gray-200 pl-2">
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Aktivasi User</a></li>
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Jenis User (Role)</a></li>
                </ul>
            </details>

            <!-- 9. Daftar Lowongan (HR) Group -->
            @php
                $isDaftarLowonganHRActive = request()->routeIs(
                    'dashboard-daftar-lowongan-daftar-perusahaan',
                    'dashboard-daftar-lowongan-listing-program',
                    'dashboard-daftar-lowongan-seleksi',
                    'dashboard-daftar-lowongan-pengajuan-magang'
                );
            @endphp
            <details class="group" {{ $isDaftarLowonganHRActive ? 'open' : '' }}>
                <summary class="flex items-center px-3 py-2 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 hover:text-vokasi-primary font-medium transition-colors {{ $isDaftarLowonganHRActive ? 'text-vokasi-primary font-bold bg-[#e6f4f5]' : '' }}">
                    <i class="fas fa-briefcase w-5"></i>
                    <span class="ml-2 flex-1">Daftar Lowongan</span>
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
                </ul>
            </details>
        </nav>
    </aside>

    <!-- MAIN CONTENT AREA -->
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
                    <span class="text-gray-800">Seminar Hasil</span>
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
        @yield('content')
    </div>

</body>
</html>