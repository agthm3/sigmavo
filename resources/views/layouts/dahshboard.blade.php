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
            
            <!-- SECTION: MAHASISWA -->
            <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-4 mb-2">Menu Mahasiswa</p>
            
            <details class="group">
                <summary class="flex items-center px-3 py-2 text-gray-700 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 hover:text-vokasi-primary font-medium transition-colors">
                    <i class="fas fa-home w-5"></i>
                    <span class="ml-2 flex-1">Dashboard Mahasiswa</span>
                    <i class="fas fa-chevron-down text-xs transition duration-300 group-open:-rotate-180"></i>
                </summary>
                <ul class="mt-1 ml-6 space-y-1 border-l-2 border-gray-200 pl-2">
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Akun</a></li>
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Riwayat Magang</a></li>
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Program Magang</a></li>
                </ul>
            </details>

            <details class="group">
                <summary class="flex items-center px-3 py-2 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 hover:text-vokasi-primary font-medium transition-colors">
                    <i class="fas fa-paper-plane w-5"></i>
                    <span class="ml-2 flex-1">Pengajuan Magang</span>
                    <i class="fas fa-chevron-down text-xs transition duration-300 group-open:-rotate-180"></i>
                </summary>
                <ul class="mt-1 ml-6 space-y-1 border-l-2 border-gray-200 pl-2">
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Daftar Lowongan</a></li>
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Ajukan Mandiri</a></li>
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Status Pengajuan</a></li>
                </ul>
            </details>

            <details class="group">
                <summary class="flex items-center px-3 py-2 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 hover:text-vokasi-primary font-medium transition-colors">
                    <i class="fas fa-running w-5"></i>
                    <span class="ml-2 flex-1">Pelaksanaan Magang</span>
                    <i class="fas fa-chevron-down text-xs transition duration-300 group-open:-rotate-180"></i>
                </summary>
                <ul class="mt-1 ml-6 space-y-1 border-l-2 border-gray-200 pl-2">
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Pembekalan Magang</a></li>
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Absensi</a></li>
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Logbook</a></li>
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Seminar</a></li>
                </ul>
            </details>

            <details class="group">
                <summary class="flex items-center px-3 py-2 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 hover:text-vokasi-primary font-medium transition-colors">
                    <i class="fas fa-file-alt w-5"></i>
                    <span class="ml-2 flex-1">Pelaporan Magang</span>
                    <i class="fas fa-chevron-down text-xs transition duration-300 group-open:-rotate-180"></i>
                </summary>
                <ul class="mt-1 ml-6 space-y-1 border-l-2 border-gray-200 pl-2">
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Download Template</a></li>
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Upload Dokumen</a></li>
                </ul>
            </details>

            <!-- SECTION: DOSEN / ADMIN / HR -->
            <hr class="my-4 border-gray-200">
            <p class="px-3 text-xs font-semibold text-vokasi-primary uppercase tracking-wider mb-2">Manajemen & HR</p>

            <details class="group">
                <summary class="flex items-center px-3 py-2 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 hover:text-vokasi-primary font-medium transition-colors">
                    <i class="fas fa-chalkboard-teacher w-5"></i>
                    <span class="ml-2 flex-1">Dashboard Dosen</span>
                    <i class="fas fa-chevron-down text-xs transition duration-300 group-open:-rotate-180"></i>
                </summary>
                <ul class="mt-1 ml-6 space-y-1 border-l-2 border-gray-200 pl-2">
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Mahasiswa Bimbingan</a></li>
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Perlu Verifikasi</a></li>
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Terverifikasi</a></li>
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Daftar Mahasiswa</a></li>
                </ul>
            </details>

            <details class="group">
                <summary class="flex items-center px-3 py-2 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 hover:text-vokasi-primary font-medium transition-colors">
                    <i class="fas fa-check-double w-5"></i>
                    <span class="ml-2 flex-1">Verifikasi</span>
                    <i class="fas fa-chevron-down text-xs transition duration-300 group-open:-rotate-180"></i>
                </summary>
                <ul class="mt-1 ml-6 space-y-1 border-l-2 border-gray-200 pl-2">
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Daftar Mahasiswa</a></li>
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Semua Laporan</a></li>
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Menunggu</a></li>
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Approved</a></li>
                </ul>
            </details>

            <details class="group">
                <summary class="flex items-center px-3 py-2 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 hover:text-vokasi-primary font-medium transition-colors">
                    <i class="fas fa-star w-5"></i>
                    <span class="ml-2 flex-1">Penilaian</span>
                    <i class="fas fa-chevron-down text-xs transition duration-300 group-open:-rotate-180"></i>
                </summary>
                <ul class="mt-1 ml-6 space-y-1 border-l-2 border-gray-200 pl-2">
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Listing Mahasiswa</a></li>
                </ul>
            </details>

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

            <details class="group">
                <summary class="flex items-center px-3 py-2 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 hover:text-vokasi-primary font-medium transition-colors">
                    <i class="fas fa-briefcase w-5"></i>
                    <span class="ml-2 flex-1">Daftar Lowongan</span>
                    <i class="fas fa-chevron-down text-xs transition duration-300 group-open:-rotate-180"></i>
                </summary>
                <ul class="mt-1 ml-6 space-y-1 border-l-2 border-gray-200 pl-2">
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Daftar Perusahaan</a></li>
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Listing Program</a></li>
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Seleksi</a></li>
                    <li><a href="#" class="block px-3 py-2 text-sm text-gray-600 hover:text-vokasi-primary rounded-md transition-colors">Pengajuan Magang</a></li>
                </ul>
            </details>
            
            <!-- Active Menu Highlight -->
            <a href="#" class="flex items-center px-3 py-2 mt-2 text-vokasi-dark bg-[#e6f4f5] rounded-lg cursor-pointer font-bold border-l-4 border-vokasi-primary transition-colors">
                <i class="fas fa-chart-pie w-5"></i>
                <span class="ml-2 flex-1">Dashboard Analitik</span>
            </a>

        </nav>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50">
        
        <!-- TOPBAR -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 lg:px-6 shrink-0 shadow-sm">
            <!-- Left: Mobile menu button & Breadcrumb -->
            <div class="flex items-center">
                <button class="md:hidden text-gray-500 hover:text-vokasi-primary focus:outline-none p-2 rounded-md transition-colors">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h2 class="hidden md:block text-xl font-bold text-gray-800 ml-4">Dashboard Analitik</h2>
            </div>

            <!-- Right: Global Menus -->
            <div class="flex items-center space-x-4">
                <!-- Helpdesk -->
                <button class="text-gray-500 hover:text-vokasi-primary transition-colors relative" title="Helpdesk">
                    <i class="fas fa-headset text-lg"></i>
                </button>
                <!-- Notification -->
                <button class="text-gray-500 hover:text-vokasi-primary transition-colors relative" title="Notifikasi">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">3</span>
                </button>
                <!-- Profile -->
                <div class="flex items-center space-x-2 cursor-pointer border-l pl-4 border-gray-200">
                    <img src="https://ui-avatars.com/api/?name=Super+Admin&background=37A7AC&color=fff" alt="Profile" class="w-8 h-8 rounded-full border border-gray-200 shadow-sm">
                    <div class="hidden md:block text-sm text-right">
                        <p class="font-bold text-gray-700 leading-tight">Superadmin</p>
                        <p class="text-xs text-gray-500">Vokasi UNHAS</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- MAIN PAGE CONTENT (Dashboard Analitik View) -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col">
            
            <!-- Content Wrapper (to push footer down) -->
            <div class="flex-1">
                <!-- Alert / Welcome Message -->
                <div class="bg-white rounded-lg shadow-sm border border-l-4 border-vokasi-primary p-4 mb-6 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-gray-800">Selamat datang di SIGMAVO!</h3>
                        <p class="text-sm text-gray-500">Sistem Informasi Magang Vokasi Universitas Hasanuddin.</p>
                    </div>
                    <div class="hidden sm:block text-vokasi-primary">
                        <i class="fas fa-chart-line text-3xl opacity-80"></i>
                    </div>
                </div>

                <!-- STATISTIC CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Card 1 -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 flex items-center hover:shadow-md transition-shadow">
                        <div class="p-3 rounded-full bg-[#e6f4f5] text-vokasi-primary mr-4">
                            <i class="fas fa-user-graduate text-xl w-6 h-6 flex items-center justify-center"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Mahasiswa Aktif</p>
                            <p class="text-2xl font-bold text-gray-800">452</p>
                        </div>
                    </div>
                    <!-- Card 2 -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 flex items-center hover:shadow-md transition-shadow">
                        <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                            <i class="fas fa-building text-xl w-6 h-6 flex items-center justify-center"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Jumlah Perusahaan</p>
                            <p class="text-2xl font-bold text-gray-800">87</p>
                        </div>
                    </div>
                    <!-- Card 3 -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 flex items-center hover:shadow-md transition-shadow">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                            <i class="fas fa-clock text-xl w-6 h-6 flex items-center justify-center"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Rata-rata Pemenuhan Jam</p>
                            <p class="text-2xl font-bold text-gray-800">65%</p>
                        </div>
                    </div>
                    <!-- Card 4 -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 flex items-center hover:shadow-md transition-shadow">
                        <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                            <i class="fas fa-exclamation-triangle text-xl w-6 h-6 flex items-center justify-center"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Laporan Pending</p>
                            <p class="text-2xl font-bold text-gray-800">124</p>
                        </div>
                    </div>
                </div>

                <!-- CHARTS / DETAILED SECTIONS -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Persebaran Prodi -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                        <h4 class="font-bold text-gray-800 mb-4 border-b pb-2">Persebaran Mahasiswa Per Prodi</h4>
                        <div class="h-64 flex flex-col justify-center items-center text-gray-400 border-2 border-dashed border-gray-200 rounded bg-gray-50">
                            <i class="fas fa-chart-pie text-4xl mb-2 text-gray-300"></i>
                            <p class="text-sm font-medium">Area untuk Chart.js / ApexCharts</p>
                        </div>
                    </div>

                    <!-- Persebaran Perusahaan -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                        <h4 class="font-bold text-gray-800 mb-4 border-b pb-2">Persebaran Lokasi Perusahaan</h4>
                        <div class="h-64 flex flex-col justify-center items-center text-gray-400 border-2 border-dashed border-gray-200 rounded bg-gray-50">
                            <i class="fas fa-map-marked-alt text-4xl mb-2 text-gray-300"></i>
                            <p class="text-sm font-medium">Area untuk Chart.js / Leaflet Maps</p>
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

</body>
</html>