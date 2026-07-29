@extends('layouts.dashboard')

@section('content')
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50">
        
        <!-- MAIN PAGE CONTENT (Dashboard Analitik View) -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col custom-scrollbar">
            
            <div class="flex-1 max-w-7xl mx-auto w-full space-y-6">
                
                <!-- WELCOME BANNER EKSEKUTIF -->
                <div class="bg-gradient-to-r from-vokasi-primary to-vokasi-dark rounded-2xl p-6 text-white shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative overflow-hidden">
                    <i class="fas fa-chart-line absolute text-9xl right-0 -bottom-6 opacity-10 pointer-events-none"></i>
                    <div class="relative z-10">
                        <span class="bg-white/20 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider text-vokasi-light mb-2 inline-block">
                            Dashboard Analitik Real-Time
                        </span>
                        <h3 class="text-2xl font-extrabold">Selamat datang di Portal SIGMAVO, {{ Auth::user()->name }}!</h3>
                        <p class="text-xs text-white/80 mt-1 max-w-xl leading-relaxed">
                            Pusat pemantauan kegiatan magang vokasi Universitas Hasanuddin. Pantau progres pemenuhan jam magang, aktivitas harian, verifikasi dosen, dan kemitraan industri.
                        </p>
                    </div>
                    
                    <div class="relative z-10 flex items-center gap-3 bg-white/10 backdrop-blur-md p-3 rounded-xl border border-white/20 shrink-0">
                        <div class="w-10 h-10 rounded-lg bg-white text-vokasi-primary flex items-center justify-center font-bold text-lg shadow">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="text-xs">
                            <p class="text-white/70 uppercase font-bold text-[10px]">Target Standar Magang</p>
                            <p class="text-sm font-extrabold text-white">{{ $targetJam }} Jam / Mahasiswa</p>
                        </div>
                    </div>
                </div>

                <!-- 1. METRIC STATISTIC CARDS (4 COLS) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- Card 1: Mahasiswa Magang Aktif -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 flex items-center justify-between hover:shadow-md transition-all">
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Mahasiswa Magang Aktif</p>
                            <p class="text-2xl font-extrabold text-gray-800 mt-1">{{ number_format($totalPendaftaranAktif) }}</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">Dari total {{ number_format($totalMahasiswa) }} Mahasiswa</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-teal-50 text-vokasi-primary flex items-center justify-center text-xl shrink-0">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    </div>

                    <!-- Card 2: Perusahaan Mitra -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 flex items-center justify-between hover:shadow-md transition-all">
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Mitra Perusahaan</p>
                            <p class="text-2xl font-extrabold text-gray-800 mt-1">{{ number_format($totalPerusahaan) }}</p>
                            <p class="text-[11px] text-emerald-600 font-semibold mt-0.5"><i class="fas fa-handshake mr-1"></i>Sektor Terintegrasi</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>

                    <!-- Card 3: Rata-Rata Pemenuhan Jam Magang -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 flex items-center justify-between hover:shadow-md transition-all">
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Progres Pemenuhan Jam</p>
                            <p class="text-2xl font-extrabold text-gray-800 mt-1">{{ $rataRataJam }}%</p>
                            <p class="text-[11px] text-vokasi-primary font-semibold mt-0.5">Akumulasi: {{ number_format($totalJamTerakumulasi) }} Jam</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                    </div>

                    <!-- Card 4: Antrean Verifikasi Pending -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 flex items-center justify-between hover:shadow-md transition-all">
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Antrean Verifikasi</p>
                            <p class="text-2xl font-extrabold {{ $totalPending > 0 ? 'text-red-600' : 'text-gray-800' }} mt-1">{{ number_format($totalPending) }}</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">{{ $totalPendingLogbook }} Logbook • {{ $totalPendingAbsensi }} Absens/Izin</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl {{ $totalPending > 0 ? 'bg-red-50 text-red-500 animate-pulse' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center text-xl shrink-0">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                    </div>

                </div>

                <!-- 2. GRAPH / CHARTS SECTION (2 COLS) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <!-- Chart 1: Persebaran Mahasiswa Per Prodi -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col justify-between">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                            <h4 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                                <i class="fas fa-chart-pie text-vokasi-primary"></i> Persebaran Mahasiswa Per Prodi
                            </h4>
                            <span class="text-[10px] font-bold bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full">Dinamis</span>
                        </div>
                        <div class="relative h-64 flex items-center justify-center">
                            <canvas id="chartProdi"></canvas>
                        </div>
                    </div>

                    <!-- Chart 2: Persebaran Mitra Perusahaan Per Sektor Industri -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col justify-between">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                            <h4 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                                <i class="fas fa-industry text-vokasi-primary"></i> Persebaran Mitra Per Sektor
                            </h4>
                            <span class="text-[10px] font-bold bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full">Dinamis</span>
                        </div>
                        <div class="relative h-64 flex items-center justify-center">
                            <canvas id="chartIndustri"></canvas>
                        </div>
                    </div>

                </div>

                <!-- 3. ANTREAN VERIFIKASI LOGBOOK TERBARU -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                        <div>
                            <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                                <i class="fas fa-tasks text-vokasi-primary"></i> Logbook Terbaru Menunggu Asistensi
                            </h3>
                            <p class="text-[11px] text-gray-500 mt-0.5">Daftar entri kegiatan harian mahasiswa yang membutuhkan verifikasi dosen pendamping.</p>
                        </div>
                        @hasanyrole('dosen|admin_prodi|admin|superadmin')
                        <a href="{{ route('dashboard-dosen-perlu-verifikasi') }}" class="px-3 py-1.5 bg-vokasi-primary hover:bg-vokasi-dark text-white font-bold rounded-xl text-xs transition-colors shadow-sm">
                            Kelola Semua Antrean
                        </a>
                        @endhasanyrole
                    </div>

                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-gray-100/60 border-b border-gray-200 font-semibold text-gray-600 uppercase tracking-wider">
                                    <th class="p-3.5 w-12 text-center">No</th>
                                    <th class="p-3.5">Mahasiswa & Prodi</th>
                                    <th class="p-3.5">Perusahaan Penempatan</th>
                                    <th class="p-3.5">Tanggal & Uraian Kegiatan</th>
                                    <th class="p-3.5 w-28 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($recentPendingLogbooks as $idx => $log)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-3.5 text-center text-gray-500 font-medium">{{ $idx + 1 }}</td>
                                    <td class="p-3.5">
                                        <p class="font-bold text-gray-800 text-xs">{{ $log->user->name }}</p>
                                        <p class="text-[10px] text-gray-400">NIM: {{ $log->user->mahasiswaProfile->nim ?? '-' }} • {{ $log->user->mahasiswaProfile->prodi->nama_prodi ?? '-' }}</p>
                                    </td>
                                    <td class="p-3.5 font-semibold text-gray-700">
                                        {{ $log->pendaftaran->perusahaan->nama_perusahaan ?? 'Pengajuan Mandiri' }}
                                    </td>
                                    <td class="p-3.5 max-w-xs">
                                        <p class="font-bold text-gray-800">{{ $log->tanggal->format('d M Y') }}</p>
                                        <p class="text-gray-600 text-[11px] truncate">{{ $log->uraian_kegiatan }}</p>
                                    </td>
                                    <td class="p-3.5 text-center">
                                        <span class="px-2.5 py-1 bg-amber-100 text-amber-800 font-bold rounded-full text-[10px] inline-flex items-center gap-1">
                                            <i class="fas fa-clock text-[9px]"></i> Pending
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-gray-400">
                                        <i class="fas fa-check-double text-3xl mb-2 text-emerald-500 block"></i> Semua logbook mahasiswa telah diverifikasi!
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- FOOTER -->
            <footer class="mt-8 py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
                Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
            </footer>

        </main>
    </div>

    <!-- SCRIPT INITIALIZE CHART.JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Palet Warna Khas Vokasi
            const primaryColor = '#37A7AC';
            const secondaryColors = ['#37A7AC', '#29868a', '#62c2c6', '#f59e0b', '#10b981', '#6366f1', '#ec4899', '#8b5cf6'];

            // 1. Chart Pie: Persebaran Prodi
            const ctxProdi = document.getElementById('chartProdi')?.getContext('2d');
            if (ctxProdi) {
                new Chart(ctxProdi, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($chartProdiLabels) !!},
                        datasets: [{
                            data: {!! json_encode($chartProdiData) !!},
                            backgroundColor: secondaryColors,
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { font: { size: 10, family: 'sans-serif' } }
                            }
                        }
                    }
                });
            }

            // 2. Chart Bar: Persebaran Sektor Industri
            const ctxIndustri = document.getElementById('chartIndustri')?.getContext('2d');
            if (ctxIndustri) {
                new Chart(ctxIndustri, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($chartIndustriLabels) !!},
                        datasets: [{
                            label: 'Jumlah Perusahaan',
                            data: {!! json_encode($chartIndustriData) !!},
                            backgroundColor: primaryColor,
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1, font: { size: 10 } }
                            },
                            x: {
                                ticks: { font: { size: 10 } }
                            }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
        });
    </script>
@endsection