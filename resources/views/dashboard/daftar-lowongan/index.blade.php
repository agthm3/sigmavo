@extends('layouts.dashboard')

@section('content')
<div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50"
     x-data="lowonganMagang()">

    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 flex flex-col relative custom-scrollbar">
        
        <!-- Hero Search Section -->
        <div class="bg-vokasi-dark px-4 py-8 lg:px-8 shadow-inner relative overflow-hidden shrink-0">
            <div class="absolute inset-0 opacity-10 pointer-events-none">
                <i class="fas fa-briefcase absolute text-9xl -right-10 -bottom-10 text-white"></i>
            </div>
            
            <div class="relative z-10 max-w-4xl mx-auto">
                <h3 class="text-2xl font-bold text-white mb-2 text-center md:text-left">Temukan Tempat Magang & Stase Praktik</h3>
                <p class="text-vokasi-light mb-6 text-sm text-center md:text-left">Jelajahi berbagai posisi magang industri dan stase poli resmi dari mitra Vokasi UNHAS.</p>
                
                <!-- Live Search Form -->
                <div class="flex flex-col md:flex-row gap-2 bg-white p-2 rounded-xl shadow-lg">
                    <div class="flex-1 flex items-center bg-gray-50 rounded-lg border border-gray-200 px-3 py-2">
                        <i class="fas fa-search text-gray-400 mr-2"></i>
                        <input type="text" x-model="searchQuery" @input.debounce.500ms="fetchData()" placeholder="Cari posisi, poli, instansi, atau kata kunci..." class="bg-transparent border-none outline-none w-full text-sm text-gray-700 focus:outline-none">
                    </div>
                    <div class="flex-1 flex items-center bg-gray-50 rounded-lg border border-gray-200 px-3 py-2">
                        <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                        <select x-model="searchLocation" @change="fetchData()" class="bg-transparent border-none outline-none w-full text-sm text-gray-700 cursor-pointer focus:outline-none">
                            <option value="">Semua Lokasi</option>
                            <option value="Makassar">Makassar</option>
                            <option value="Gowa">Gowa</option>
                            <option value="Maros">Maros</option>
                        </select>
                    </div>
                    <button type="button" @click="fetchData()" class="bg-vokasi-primary hover:bg-vokasi-dark text-white font-bold py-3 md:py-2 px-6 rounded-lg transition-colors w-full md:w-auto">
                        Cari
                    </button>
                </div>
            </div>
        </div>

        <div class="flex-1 p-4 lg:p-6 max-w-7xl mx-auto w-full">
            
            @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center justify-between text-sm shadow-sm">
                <div class="flex items-center gap-2"><i class="fas fa-check-circle text-emerald-600 text-lg"></i><span>{{ session('success') }}</span></div>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900"><i class="fas fa-times"></i></button>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center justify-between text-sm shadow-sm">
                <div class="flex items-center gap-2"><i class="fas fa-exclamation-circle text-red-600 text-lg"></i><span>{{ session('error') }}</span></div>
                <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900"><i class="fas fa-times"></i></button>
            </div>
            @endif

            <!-- Loading Indicator -->
            <div x-show="isLoading" x-cloak class="flex justify-center items-center py-10">
                <i class="fas fa-spinner fa-spin text-3xl text-vokasi-primary"></i>
                <span class="ml-3 font-semibold text-gray-500">Mencari Lowongan...</span>
            </div>

            <!-- JOB CARDS GRID (Live Data via Alpine) -->
            <div x-show="!isLoading" x-transition.opacity duration.500 class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                
                <template x-for="job in jobs" :key="job.id">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 flex flex-col h-full hover:border-vokasi-primary hover:shadow-md group">
                        
                        <div class="p-5 flex-1 flex flex-col">
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-12 h-12 rounded-xl bg-teal-50 border border-teal-100 flex items-center justify-center text-vokasi-primary font-bold text-base shadow-sm" x-text="job.inisial"></div>

                                <template x-if="job.is_active">
                                    <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2.5 py-1 rounded-full border border-amber-200 shadow-sm"><i class="fas fa-clock text-[9px] mr-1"></i> Sedang Proses</span>
                                </template>
                                <template x-if="job.is_completed">
                                    <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2.5 py-1 rounded-full border border-blue-200 shadow-sm"><i class="fas fa-check-double text-[9px] mr-1"></i> Pernah Selesai</span>
                                </template>
                            </div>
                            
                            <h4 class="font-bold text-lg text-gray-800 leading-tight mb-1 line-clamp-1 group-hover:text-vokasi-primary transition-colors" x-text="job.judul_posisi"></h4>
                            <p class="text-vokasi-primary font-semibold text-sm mb-3 line-clamp-1" x-text="job.perusahaan_nama"></p>
                            
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-2.5 py-1 rounded-lg border border-blue-100" x-text="job.mode_kerja"></span>
                                <span class="bg-purple-50 text-purple-600 text-[10px] font-bold px-2.5 py-1 rounded-lg border border-purple-100 line-clamp-1" x-text="job.prodi_nama"></span>
                            </div>

                            <ul class="text-[11px] text-gray-500 space-y-2 mb-4">
                                <li class="flex items-center"><i class="fas fa-map-marker-alt w-5 text-gray-400"></i> <span x-text="job.alamat"></span></li>
                                <li class="flex items-center"><i class="fas fa-calendar-alt w-5 text-gray-400"></i> Batas: <span x-text="job.batas_pendaftaran"></span></li>
                                <li class="flex items-center"><i class="fas fa-user-shield w-5 text-gray-400"></i> SPV: <span class="font-bold text-gray-700 ml-1" x-text="job.spv_nama"></span></li>
                            </ul>

                            <div class="grid grid-cols-3 gap-2 p-2.5 bg-gray-50 rounded-xl border border-gray-200 text-center mb-4 mt-auto">
                                <div class="p-1">
                                    <span class="block text-[10px] font-semibold text-emerald-700 uppercase">Diterima</span>
                                    <span class="text-xs font-bold text-emerald-600" x-text="job.total_diterima + ' / ' + job.kuota"></span>
                                </div>
                                <div class="p-1 border-x border-gray-200">
                                    <span class="block text-[10px] font-semibold text-amber-700 uppercase">Proses</span>
                                    <span class="text-xs font-bold text-amber-600" x-text="job.total_menunggu + ' Mhs'"></span>
                                </div>
                                <div class="p-1">
                                    <span class="block text-[10px] font-semibold text-red-700 uppercase">Ditolak</span>
                                    <span class="text-xs font-bold text-red-500" x-text="job.total_ditolak + ' Mhs'"></span>
                                </div>
                            </div>

                            <!-- LOG PELAMAR (Accordion) -->
                            <div class="border-t border-gray-100 pt-3" x-data="{ showAll: false }">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[11px] font-bold text-gray-700 flex items-center gap-1.5">
                                        <i class="fas fa-users text-vokasi-primary"></i> Log Pelamar (<span x-text="job.total_pelamar"></span>)
                                    </span>
                                    <template x-if="job.pelamars.length > 3">
                                        <button type="button" @click="showAll = !showAll" class="text-[10px] font-bold text-vokasi-primary hover:underline flex items-center gap-1">
                                            <span x-text="showAll ? 'Tutup' : 'Lihat Semua'"></span>
                                            <i class="fas" :class="showAll ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                        </button>
                                    </template>
                                </div>

                                <template x-if="job.pelamars.length === 0">
                                    <p class="text-[11px] text-gray-400 italic">Belum ada pelamar.</p>
                                </template>

                                <template x-if="job.pelamars.length > 0">
                                    <div class="space-y-1.5" :class="showAll ? 'max-h-48 overflow-y-auto custom-scrollbar pr-1' : ''">
                                        <template x-for="(pelamar, index) in job.pelamars" :key="pelamar.id">
                                            <div x-show="showAll || index < 3" class="flex flex-col text-xs py-1.5 px-2 rounded-lg bg-gray-50/80 border border-gray-100">
                                                <div class="flex items-center justify-between mb-1">
                                                    <div class="flex items-center gap-2 min-w-0 pr-2">
                                                        <div class="w-5 h-5 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-[9px] shrink-0" x-text="pelamar.nama.charAt(0)"></div>
                                                        <div>
                                                            <span class="font-medium text-gray-700 truncate text-[11px] block leading-tight" x-text="pelamar.nama"></span>
                                                            <span class="text-[9px] text-gray-400 font-mono" x-text="pelamar.nim"></span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <template x-if="pelamar.status === 'diterima'"><span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 rounded text-[9px] font-bold shrink-0">Diterima</span></template>
                                                        <template x-if="pelamar.status === 'ditolak'"><span class="px-1.5 py-0.5 bg-red-100 text-red-700 rounded text-[9px] font-bold shrink-0">Ditolak</span></template>
                                                        <template x-if="pelamar.status === 'selesai'"><span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded text-[9px] font-bold shrink-0">Selesai</span></template>
                                                        <template x-if="['menunggu', 'pending', 'wawancara'].includes(pelamar.status)"><span class="px-1.5 py-0.5 bg-amber-100 text-amber-700 rounded text-[9px] font-bold shrink-0">Proses</span></template>
                                                    </div>
                                                </div>
                                                <!-- Dosen Info -->
                                                <div class="text-[9px] text-gray-500 pl-7">
                                                    <i class="fas fa-user-tie mr-1 text-blue-400"></i> Dosen: <strong class="text-gray-600" x-text="pelamar.dosen"></strong>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="p-4 bg-gray-50/80 border-t border-gray-100 flex items-center justify-between mt-auto shrink-0">
                            <span class="text-[11px] text-gray-500 font-medium">Kuota: <strong class="text-gray-800" x-text="job.kuota + ' Kursi'"></strong></span>
                            <button @click="openModal(job)" class="px-3.5 py-1.5 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-xl font-bold text-xs transition-colors shadow-sm flex items-center gap-1.5">
                                Detail & Lamar <i class="fas fa-chevron-right text-[9px]"></i>
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Empty State -->
                <template x-if="jobs.length === 0 && !isLoading">
                    <div class="col-span-full bg-white p-12 text-center rounded-2xl border border-gray-200 text-gray-400">
                        <i class="fas fa-briefcase text-4xl mb-3 block"></i>
                        <p class="font-bold text-gray-600 text-base">Tidak Ada Lowongan Ditemukan</p>
                        <p class="text-xs mt-1">Coba gunakan kata kunci pencarian atau lokasi lain.</p>
                    </div>
                </template>

            </div>

            <!-- HTML Pagination Placeholder -->
            <div x-show="!isLoading && paginationHtml !== ''" class="flex justify-center mb-8" x-html="paginationHtml"></div>
        </div>

        <footer class="mt-auto py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
            Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
        </footer>
    </main>

    <!-- ========================================== -->
    <!-- MODAL POPUP: DETAIL LOWONGAN & LAMAR -->
    <!-- ========================================== -->
    <div x-show="openDetailModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.away="openDetailModal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden max-h-[90vh] flex flex-col" x-if="selectedJob">
            
            <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center shrink-0">
                <h3 class="font-bold text-lg flex items-center gap-2"><i class="fas fa-briefcase"></i> <span x-text="selectedJob?.judul_posisi"></span></h3>
                <button @click="openDetailModal = false" class="text-white/80 hover:text-white"><i class="fas fa-times text-lg"></i></button>
            </div>

            <div class="p-6 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                <div class="flex items-center gap-4 pb-4 border-b border-gray-100">
                    <div class="w-14 h-14 rounded-xl bg-teal-50 border border-teal-100 flex items-center justify-center text-vokasi-primary font-bold text-xl shrink-0" x-text="selectedJob?.inisial"></div>
                    <div>
                        <h4 class="font-bold text-lg text-gray-800" x-text="selectedJob?.perusahaan_nama"></h4>
                        <p class="text-xs text-vokasi-primary font-medium" x-text="selectedJob?.perusahaan_sektor"></p>
                        <p class="text-xs text-gray-500 mt-1"><i class="fas fa-map-marker-alt text-red-500 mr-1"></i> <span x-text="selectedJob?.alamat"></span></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 bg-gray-50 p-4 rounded-xl border border-gray-200 text-xs">
                    <div><span class="text-gray-400 block font-semibold uppercase text-[10px]">Mode Kerja</span><span class="font-bold text-gray-800" x-text="selectedJob?.mode_kerja"></span></div>
                    <div><span class="text-gray-400 block font-semibold uppercase text-[10px]">Durasi Program</span><span class="font-bold text-gray-800" x-text="selectedJob?.durasi"></span></div>
                    <div><span class="text-gray-400 block font-semibold uppercase text-[10px]">Target Prodi</span><span class="font-bold text-gray-800" x-text="selectedJob?.prodi_nama"></span></div>
                    <div><span class="text-gray-400 block font-semibold uppercase text-[10px]">Target Penerimaan</span><span class="font-bold text-purple-700" x-text="selectedJob?.kuota + ' Mahasiswa'"></span></div>
                    <div><span class="text-gray-400 block font-semibold uppercase text-[10px]">Total Pendaftar</span><span class="font-bold text-emerald-600" x-text="selectedJob?.total_pelamar + ' Orang'"></span></div>
                    <div><span class="text-gray-400 block font-semibold uppercase text-[10px]">Batas Akhir</span><span class="font-bold text-red-600" x-text="selectedJob?.batas_pendaftaran"></span></div>
                </div>

                <div>
                    <h5 class="font-bold text-gray-800 text-sm mb-2"><i class="fas fa-file-alt text-vokasi-primary mr-1.5"></i> Deskripsi & Kualifikasi</h5>
                    <p class="text-xs text-gray-600 leading-relaxed whitespace-pre-line" x-text="selectedJob?.deskripsi"></p>
                </div>
            </div>

            <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 shrink-0">
                <button type="button" @click="openDetailModal = false" class="px-5 py-2 border border-gray-300 rounded-xl text-gray-700 bg-white font-medium text-xs">Tutup</button>
                <form :action="activeLamarUrl" method="POST">
                    @csrf
                    <template x-if="selectedJob?.is_active">
                        <button type="button" disabled class="px-6 py-2 bg-amber-100 text-amber-800 border border-amber-300 rounded-xl font-bold text-xs cursor-not-allowed flex items-center gap-1.5"><i class="fas fa-clock"></i> Sedang Diproses</button>
                    </template>
                    <template x-if="!selectedJob?.is_active">
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin mengajukan lamaran magang pada posisi ini?')" class="px-6 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-xl font-bold text-xs shadow-sm flex items-center gap-2"><i class="fas fa-paper-plane"></i> Kirim Lamaran Magang</button>
                    </template>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('lowonganMagang', () => ({
        jobs: [],
        searchQuery: new URLSearchParams(window.location.search).get('search') || '',
        searchLocation: new URLSearchParams(window.location.search).get('lokasi') || '',
        isLoading: true,
        openDetailModal: false,
        selectedJob: null,
        activeLamarUrl: '',
        paginationHtml: '',

        init() {
            this.fetchData();
        },

        fetchData() {
            this.isLoading = true;
            let url = new URL("{{ route('dashboard-mahasiswa-daftar-lowongan') }}");
            url.searchParams.set('search', this.searchQuery);
            url.searchParams.set('lokasi', this.searchLocation);
            
            window.history.pushState({}, '', url);

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.jobs = data.data;
                this.paginationHtml = data.links;
                this.isLoading = false;
            })
            .catch(err => {
                console.error(err);
                this.isLoading = false;
            });
        },

        openModal(job) {
            this.selectedJob = job;
            this.activeLamarUrl = "{{ route('dashboard-mahasiswa-daftar-lowongan-lamar', ':id') }}".replace(':id', job.id);
            this.openDetailModal = true;
        }
    }));
});
</script>
@endsection