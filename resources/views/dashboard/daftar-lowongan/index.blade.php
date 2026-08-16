@extends('layouts.dashboard')

@section('content')
    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50"
         x-data="{ openDetailModal: false, selectedJob: null, activeLamarUrl: '', isAlreadyActive: false }">

        <!-- CONTENT -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 flex flex-col relative custom-scrollbar">
            
            <!-- Hero Search Section -->
            <div class="bg-vokasi-dark px-4 py-8 lg:px-8 shadow-inner relative overflow-hidden">
                <div class="absolute inset-0 opacity-10 pointer-events-none">
                    <i class="fas fa-briefcase absolute text-9xl -right-10 -bottom-10 text-white"></i>
                </div>
                
                <div class="relative z-10 max-w-4xl mx-auto">
                    <h3 class="text-2xl font-bold text-white mb-2 text-center md:text-left">Temukan Tempat Magang & Stase Praktik</h3>
                    <p class="text-vokasi-light mb-6 text-sm text-center md:text-left">Jelajahi berbagai posisi magang industri dan stase poli resmi dari mitra Vokasi UNHAS.</p>
                    
                    <!-- Search Bar Form -->
                    <form action="{{ route('dashboard-mahasiswa-daftar-lowongan') }}" method="GET" class="flex flex-col md:flex-row gap-2 bg-white p-2 rounded-xl shadow-lg">
                        <div class="flex-1 flex items-center bg-gray-50 rounded-lg border border-gray-200 px-3 py-2">
                            <i class="fas fa-search text-gray-400 mr-2"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari posisi, poli, instansi, atau kata kunci..." class="bg-transparent border-none outline-none w-full text-sm text-gray-700 focus:outline-none">
                        </div>
                        <div class="flex-1 flex items-center bg-gray-50 rounded-lg border border-gray-200 px-3 py-2">
                            <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                            <select name="lokasi" onchange="this.form.submit()" class="bg-transparent border-none outline-none w-full text-sm text-gray-700 cursor-pointer focus:outline-none">
                                <option value="">Semua Lokasi</option>
                                <option value="Makassar" {{ request('lokasi') == 'Makassar' ? 'selected' : '' }}>Makassar</option>
                                <option value="Gowa" {{ request('lokasi') == 'Gowa' ? 'selected' : '' }}>Gowa</option>
                                <option value="Maros" {{ request('lokasi') == 'Maros' ? 'selected' : '' }}>Maros</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-vokasi-primary hover:bg-vokasi-dark text-white font-bold py-3 md:py-2 px-6 rounded-lg transition-colors w-full md:w-auto">
                            Cari
                        </button>
                    </form>
                </div>
            </div>

            <div class="flex-1 p-4 lg:p-6 max-w-7xl mx-auto w-full">
                
                <!-- NOTIFIKASI SUKSES / ERROR -->
                @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center justify-between text-sm shadow-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900"><i class="fas fa-times"></i></button>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center justify-between text-sm shadow-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-circle text-red-600 text-lg"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900"><i class="fas fa-times"></i></button>
                </div>
                @endif

                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-bold text-gray-800 text-lg">Lowongan & Stase Magang Tersedia</h4>
                    <span class="text-sm text-gray-500 font-medium">Menampilkan {{ $lowongans->count() }} dari {{ $lowongans->total() }} posisi</span>
                </div>

                <!-- JOB CARDS GRID -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6 mb-8">
                    
                    @forelse($lowongans as $job)
                    @php
                        $isActiveApplied = in_array($job->id, $activePendaftaranIds);
                        $isCompleted = in_array($job->id, $completedPendaftaranIds);
                        $totalPelamar = $job->total_pelamar ?? 0;
                        $totalDiterima = $job->total_diterima ?? 0;
                        $totalDitolak = $job->total_ditolak ?? 0;
                        $totalMenunggu = $job->total_menunggu ?? 0;
                        
                        $allPelamars = $job->pendaftarans;
                        $topPelamars = $allPelamars->take(5);
                        $hasMore = $allPelamars->count() > 5;
                    @endphp
                    <div class="job-card bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 flex flex-col h-full hover:border-vokasi-primary hover:shadow-md"
                         x-data="{ showAllApplicants: false }">
                        
                        <div class="p-5 flex-1 flex flex-col">
                            <!-- Header Logo & Badge -->
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-12 h-12 rounded-xl bg-teal-50 border border-teal-100 flex items-center justify-center text-vokasi-primary font-bold text-base shadow-sm">
                                    {{ $job->perusahaan->inisial ?? 'PT' }}
                                </div>

                                @if($isActiveApplied)
                                    <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2.5 py-1 rounded-full border border-amber-200 shadow-sm flex items-center gap-1">
                                        <i class="fas fa-clock text-[9px]"></i> Sedang Proses/Aktif
                                    </span>
                                @elseif($isCompleted)
                                    <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2.5 py-1 rounded-full border border-blue-200 shadow-sm flex items-center gap-1">
                                        <i class="fas fa-check-double text-[9px]"></i> Pernah Selesai
                                    </span>
                                @endif
                            </div>
                            
                            <h4 class="font-bold text-lg text-gray-800 leading-tight mb-1 line-clamp-1">{{ $job->judul_posisi }}</h4>
                            <p class="text-vokasi-primary font-semibold text-sm mb-3 line-clamp-1">{{ $job->perusahaan->nama_perusahaan ?? '-' }}</p>
                            
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-2.5 py-1 rounded-lg border border-blue-100">
                                    {{ $job->mode_kerja }}
                                </span>
                                <span class="bg-purple-50 text-purple-600 text-[10px] font-bold px-2.5 py-1 rounded-lg border border-purple-100 line-clamp-1">
                                    {{ $job->prodi?->nama_prodi ?? 'Semua Prodi' }}
                                </span>
                            </div>

                            <ul class="text-xs text-gray-500 space-y-1.5 mb-4">
                                <li class="flex items-center"><i class="fas fa-map-marker-alt w-5 text-gray-400"></i> {{ Str::limit($job->perusahaan->alamat ?? '-', 28) }}</li>
                                <li class="flex items-center"><i class="fas fa-calendar-alt w-5 text-gray-400"></i> Batas: {{ $job->batas_pendaftaran ? $job->batas_pendaftaran->format('d M Y') : '-' }}</li>
                            </ul>

                            <!-- STATISTIK STATUS PELAMAR -->
                            <div class="grid grid-cols-3 gap-2 p-2.5 bg-gray-50 rounded-xl border border-gray-200 text-center mb-4">
                                <div class="p-1">
                                    <span class="block text-[10px] font-semibold text-emerald-700 uppercase">Diterima</span>
                                    <span class="text-xs font-bold text-emerald-600">{{ $totalDiterima }} / {{ $job->kuota }}</span>
                                </div>
                                <div class="p-1 border-x border-gray-200">
                                    <span class="block text-[10px] font-semibold text-amber-700 uppercase">Proses</span>
                                    <span class="text-xs font-bold text-amber-600">{{ $totalMenunggu }} Mhs</span>
                                </div>
                                <div class="p-1">
                                    <span class="block text-[10px] font-semibold text-red-700 uppercase">Ditolak</span>
                                    <span class="text-xs font-bold text-red-500">{{ $totalDitolak }} Mhs</span>
                                </div>
                            </div>

                            <!-- DAFTAR LOG PELAMAR (TOP 5 & ACCORDION EXPAND) -->
                            <div class="mt-auto pt-3 border-t border-gray-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[11px] font-bold text-gray-700 flex items-center gap-1.5">
                                        <i class="fas fa-users text-vokasi-primary"></i> Pendaftar ({{ $totalPelamar }})
                                    </span>
                                    @if($hasMore)
                                    <button type="button" @click="showAllApplicants = !showAllApplicants" class="text-[10px] font-bold text-vokasi-primary hover:underline flex items-center gap-1">
                                        <span x-text="showAllApplicants ? 'Tutup' : 'Lihat Semua ({{ $totalPelamar }})'"></span>
                                        <i class="fas" :class="showAllApplicants ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                    </button>
                                    @endif
                                </div>

                                @if($allPelamars->isEmpty())
                                    <p class="text-[11px] text-gray-400 italic py-1">Belum ada pelamar.</p>
                                @else
                                    <!-- 5 PELAMAR TERATAS (DEFAULT) -->
                                    <div class="space-y-1.5" x-show="!showAllApplicants">
                                        @foreach($topPelamars as $pelamar)
                                        <div class="flex items-center justify-between text-xs py-1 px-2 rounded-lg bg-gray-50/80 border border-gray-100">
                                            <div class="flex items-center gap-2 min-w-0 pr-2">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($pelamar->user?->name ?? 'M') }}&background=E6FFFA&color=0D9488&size=24" class="w-5 h-5 rounded-full shrink-0">
                                                <span class="font-medium text-gray-700 truncate text-[11px]">{{ $pelamar->user?->name ?? 'Mahasiswa' }}</span>
                                            </div>
                                            <div>
                                                @if($pelamar->status_seleksi === 'diterima')
                                                    <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 rounded text-[9px] font-bold shrink-0">Diterima</span>
                                                @elseif($pelamar->status_seleksi === 'ditolak')
                                                    <span class="px-1.5 py-0.5 bg-red-100 text-red-700 rounded text-[9px] font-bold shrink-0">Ditolak</span>
                                                @elseif($pelamar->status_seleksi === 'selesai')
                                                    <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded text-[9px] font-bold shrink-0">Selesai</span>
                                                @else
                                                    <span class="px-1.5 py-0.5 bg-amber-100 text-amber-700 rounded text-[9px] font-bold shrink-0">Proses</span>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    <!-- SELURUH PELAMAR KETIKA DROPDOWN DIBUKA -->
                                    <div class="space-y-1.5 max-h-48 overflow-y-auto custom-scrollbar pr-1" x-show="showAllApplicants" x-cloak>
                                        @foreach($allPelamars as $pelamar)
                                        <div class="flex items-center justify-between text-xs py-1 px-2 rounded-lg bg-gray-50 border border-gray-100">
                                            <div class="flex items-center gap-2 min-w-0 pr-2">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($pelamar->user?->name ?? 'M') }}&background=E6FFFA&color=0D9488&size=24" class="w-5 h-5 rounded-full shrink-0">
                                                <div>
                                                    <p class="font-medium text-gray-700 truncate text-[11px] leading-tight">{{ $pelamar->user?->name ?? 'Mahasiswa' }}</p>
                                                    <p class="text-[9px] text-gray-400 font-mono">{{ $pelamar->user?->mahasiswaProfile?->nim ?? '' }}</p>
                                                </div>
                                            </div>
                                            <div>
                                                @if($pelamar->status_seleksi === 'diterima')
                                                    <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 rounded text-[9px] font-bold shrink-0">Diterima</span>
                                                @elseif($pelamar->status_seleksi === 'ditolak')
                                                    <span class="px-1.5 py-0.5 bg-red-100 text-red-700 rounded text-[9px] font-bold shrink-0">Ditolak</span>
                                                @elseif($pelamar->status_seleksi === 'selesai')
                                                    <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded text-[9px] font-bold shrink-0">Selesai</span>
                                                @else
                                                    <span class="px-1.5 py-0.5 bg-amber-100 text-amber-700 rounded text-[9px] font-bold shrink-0">Proses</span>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                        </div>

                        <!-- Card Footer -->
                        <div class="p-4 bg-gray-50/80 border-t border-gray-100 mt-auto flex items-center justify-between">
                            <span class="text-[11px] text-gray-500 font-medium">
                                Kuota: <strong class="text-gray-800">{{ $job->kuota }} Kursi</strong>
                            </span>
                            <button @click="
                                selectedJob = {{ json_encode($job) }}; 
                                activeLamarUrl = '{{ route('dashboard-mahasiswa-daftar-lowongan-lamar', $job->id) }}'; 
                                isAlreadyActive = {{ $isActiveApplied ? 'true' : 'false' }};
                                openDetailModal = true;
                            " class="px-3.5 py-1.5 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-xl font-bold text-xs transition-colors shadow-sm flex items-center gap-1.5">
                                Detail & Lamar <i class="fas fa-chevron-right text-[9px]"></i>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full bg-white p-12 text-center rounded-2xl border border-gray-200 text-gray-400">
                        <i class="fas fa-briefcase text-4xl mb-3 block"></i>
                        <p class="font-bold text-gray-600 text-base">Belum Ada Lowongan Magang Tersedia</p>
                        <p class="text-xs mt-1">Coba gunakan kata kunci pencarian atau lokasi lain.</p>
                    </div>
                    @endforelse

                </div>

                <!-- Pagination -->
                <div class="flex justify-center mb-8">
                    {{ $lowongans->links() }}
                </div>

            </div>

            <!-- FOOTER -->
            <footer class="mt-auto py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
                Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
            </footer>

        </main>

        <!-- ========================================== -->
        <!-- MODAL POPUP: DETAIL LOWONGAN & LAMAR -->
        <!-- ========================================== -->
        <div x-show="openDetailModal" 
             x-cloak 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            
            <div @click.away="openDetailModal = false" class="bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-2xl overflow-hidden max-h-[90vh] flex flex-col" x-if="selectedJob">
                
                <!-- Modal Header -->
                <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center shrink-0">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-briefcase text-lg"></i>
                        <h3 class="font-bold text-lg" x-text="selectedJob?.judul_posisi"></h3>
                    </div>
                    <button @click="openDetailModal = false" class="text-white/80 hover:text-white hover:bg-black/10 p-1.5 rounded-lg transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                    
                    <!-- Header Info Perusahaan -->
                    <div class="flex items-center gap-4 pb-4 border-b border-gray-100">
                        <div class="w-14 h-14 rounded-xl bg-teal-50 border border-teal-100 flex items-center justify-center text-vokasi-primary font-bold text-xl shrink-0">
                            <span x-text="selectedJob?.perusahaan?.nama_perusahaan ? selectedJob?.perusahaan?.nama_perusahaan.substring(0, 2).toUpperCase() : 'PT'"></span>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg text-gray-800" x-text="selectedJob?.perusahaan?.nama_perusahaan"></h4>
                            <p class="text-xs text-vokasi-primary font-medium" x-text="selectedJob?.perusahaan?.sektor_industri"></p>
                            <p class="text-xs text-gray-500 mt-1"><i class="fas fa-map-marker-alt text-red-500 mr-1"></i> <span x-text="selectedJob?.perusahaan?.alamat"></span></p>
                        </div>
                    </div>

                    <!-- Attribute Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 bg-gray-50 p-4 rounded-xl border border-gray-200 text-xs">
                        <div>
                            <span class="text-gray-400 block font-semibold uppercase text-[10px]">Mode Kerja</span>
                            <span class="font-bold text-gray-800" x-text="selectedJob?.mode_kerja"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block font-semibold uppercase text-[10px]">Durasi Program</span>
                            <span class="font-bold text-gray-800" x-text="selectedJob?.durasi"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block font-semibold uppercase text-[10px]">Target Prodi</span>
                            <span class="font-bold text-gray-800" x-text="selectedJob?.prodi?.nama_prodi || 'Semua Prodi'"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block font-semibold uppercase text-[10px]">Target Penerimaan</span>
                            <span class="font-bold text-purple-700" x-text="selectedJob?.kuota + ' Mahasiswa'"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block font-semibold uppercase text-[10px]">Total Pendaftar</span>
                            <span class="font-bold text-emerald-600" x-text="(selectedJob?.total_pelamar || 0) + ' Orang Melamar'"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block font-semibold uppercase text-[10px]">Batas Akhir</span>
                            <span class="font-bold text-red-600" x-text="selectedJob?.batas_pendaftaran ? selectedJob?.batas_pendaftaran.split('T')[0] : '-'"></span>
                        </div>
                    </div>

                    <!-- Deskripsi Pekerjaan -->
                    <div>
                        <h5 class="font-bold text-gray-800 text-sm mb-2"><i class="fas fa-file-alt text-vokasi-primary mr-1.5"></i> Deskripsi & Kualifikasi</h5>
                        <p class="text-xs text-gray-600 leading-relaxed whitespace-pre-line" x-text="selectedJob?.deskripsi"></p>
                    </div>

                </div>

                <!-- Modal Footer / Action Lamar -->
                <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 shrink-0">
                    <button type="button" @click="openDetailModal = false" class="px-5 py-2 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-medium text-xs transition-colors">
                        Tutup
                    </button>
                    
                    <form :action="activeLamarUrl" method="POST">
                        @csrf
                        
                        <!-- JIKA SUDAH PERNAH DILAMAR DAN MASIH PROSES AKTIF -->
                        <template x-if="isAlreadyActive">
                            <button type="button" disabled class="px-6 py-2 bg-amber-100 text-amber-800 border border-amber-300 rounded-xl font-bold text-xs cursor-not-allowed flex items-center gap-1.5 shadow-sm">
                                <i class="fas fa-clock"></i> Lamaran Anda Sedang Diproses
                            </button>
                        </template>

                        <!-- JIKA BELUM ADA PROSES AKTIF (PENDAFTARAN BEBAS / UNLIMITED) -->
                        <template x-if="!isAlreadyActive">
                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin mengajukan lamaran magang pada posisi ini?')" class="px-6 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-xl font-bold text-xs transition-colors shadow-sm flex items-center gap-2">
                                <i class="fas fa-paper-plane"></i> Kirim Lamaran Magang
                            </button>
                        </template>
                    </form>
                </div>

            </div>
        </div>

    </div>
@endsection