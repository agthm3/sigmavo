@extends('layouts.dashboard')

@section('content')

    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50"
         x-data="{ 
             openDetailModal: false, 
             activeMhs: null,
             showModal(data) {
                 this.activeMhs = data;
                 this.openDetailModal = true;
             }
         }">

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative custom-scrollbar">
            
            <div class="max-w-7xl mx-auto w-full flex-1 space-y-6">
                
                <!-- HEADER PAGE -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Mahasiswa Bimbingan</h2>
                        <p class="text-sm text-gray-500 mt-1">Pantau progres akumulasi jam kerja, status validasi SPV mitra, dan asistensi logbook mahasiswa magang Anda.</p>
                    </div>
                    
                    <!-- Quick Stats Badges -->
                    <div class="flex gap-3">
                        <div class="bg-white border border-gray-200 px-4 py-2 rounded-xl shadow-sm flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center font-bold">
                                <i class="fas fa-user-graduate text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[11px] text-gray-500 font-semibold uppercase">Total Bimbingan</p>
                                <p class="text-lg font-bold text-gray-800 leading-none mt-0.5">{{ $totalBimbingan }} Mahasiswa</p>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-200 px-4 py-2 rounded-xl shadow-sm flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-orange-50 text-orange-500 flex items-center justify-center font-bold">
                                <i class="fas fa-file-signature text-sm {{ $siapAsistensi > 0 ? 'animate-pulse' : '' }}"></i>
                            </div>
                            <div>
                                <p class="text-[11px] text-gray-500 font-semibold uppercase">Siap Diasistensi</p>
                                <p class="text-lg font-bold text-orange-600 leading-none mt-0.5">{{ $siapAsistensi }} Mahasiswa</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TOOLBAR (Search & Filter) -->
                <form action="{{ route('dashboard-dosen-mahasiswa-bimbingan') }}" method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex flex-col md:flex-row gap-4 justify-between items-center">
                    <div class="relative w-full md:w-96">
                        <i class="fas fa-search absolute left-3.5 top-3 text-gray-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama mahasiswa, NIM, atau instansi..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vokasi-primary text-xs">
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                        <select name="status_laporan" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-xs rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm font-medium">
                            <option value="semua" {{ request('status_laporan') == 'semua' ? 'selected' : '' }}>Semua Status Logbook</option>
                            <option value="ready" {{ request('status_laporan') == 'ready' ? 'selected' : '' }}>Siap Diasistensi Dosen (Sudah di-Approve SPV)</option>
                            <option value="waiting_spv" {{ request('status_laporan') == 'waiting_spv' ? 'selected' : '' }}>Menunggu Approval SPV Mitra</option>
                            <option value="uptodate" {{ request('status_laporan') == 'uptodate' ? 'selected' : '' }}>Logbook Up to Date (Selesai)</option>
                        </select>
                    </div>
                </form>

                <!-- LISTING MAHASISWA (Rich Cards) -->
                <div class="space-y-4">
                    
                    @forelse($bimbingans as $item)
                        @php
                            $mhs = $item->user;
                            $profile = $mhs?->mahasiswaProfile;
                            $isMandiri = $item->jalur_magang === 'mandiri';
                            $namaMitra = $isMandiri ? $item->nama_instansi_mandiri : ($item->lowongan->perusahaan->nama_perusahaan ?? 'Mitra Magang');
                            $judulPosisi = $isMandiri ? ($item->divisi_mandiri ?? 'Pengajuan Mandiri') : ($item->lowongan->judul_posisi ?? 'Stase Magang');
                            
                            $canAssist = $item->logbook_ready_dosen > 0;
                            $isWaitingSpv = ($item->logbook_ready_dosen == 0) && ($item->logbook_waiting_spv > 0);
                        @endphp

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 hover:border-vokasi-primary transition-all duration-300 flex flex-col lg:flex-row gap-6 items-start lg:items-center">
                            
                            <!-- Profile & Info Utama -->
                            <div class="flex items-start gap-4 flex-1">
                                <div class="relative shrink-0 cursor-pointer" @click="showModal(@js($item))">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($mhs?->name ?? 'Mhs') }}&background=E6FFFA&color=0D9488&size=56" alt="Avatar" class="w-14 h-14 rounded-full border border-teal-200 shadow-sm hover:opacity-90">
                                    
                                    @if($item->status_seleksi === 'diterima')
                                        <span class="absolute bottom-0 right-0 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full" title="Sedang Aktif Magang"></span>
                                    @elseif($item->status_seleksi === 'selesai')
                                        <span class="absolute bottom-0 right-0 w-4 h-4 bg-blue-500 border-2 border-white rounded-full" title="Magang Selesai"></span>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="font-bold text-base text-gray-800 leading-tight">
                                        <button type="button" @click="showModal(@js($item))" class="text-left font-bold text-gray-800 hover:text-vokasi-primary transition-colors">
                                            {{ $mhs?->name ?? 'Mahasiswa' }}
                                        </button>
                                    </h3>
                                    <p class="text-xs text-gray-500 font-mono mt-0.5">NIM: {{ $profile?->nim ?? '-' }} | {{ $profile?->prodi?->nama_prodi ?? 'Vokasi' }}</p>
                                    
                                    <div class="flex items-center text-xs text-gray-600 mt-2 gap-2">
                                        <span class="inline-flex items-center gap-1 text-vokasi-primary font-semibold">
                                            <i class="fas fa-building"></i> {{ $namaMitra }}
                                        </span>
                                        <span class="text-gray-300">•</span>
                                        <span class="text-gray-500 font-medium">{{ $judulPosisi }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Section Akumulasi Jam -->
                            <div class="w-full lg:w-64 shrink-0">
                                <div class="flex justify-between items-end mb-1.5">
                                    <span class="text-xs font-semibold text-gray-600">Progres {{ $item->target_jam }} Jam</span>
                                    <span class="text-xs font-bold text-vokasi-primary">{{ $item->total_jam }} Jam <span class="text-gray-400 font-normal">({{ $item->persentase_jam }}%)</span></span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden border border-gray-200">
                                    <div class="bg-vokasi-primary h-2.5 rounded-full transition-all duration-500" style="width: {{ $item->persentase_jam }}%"></div>
                                </div>
                                <p class="text-[10px] text-gray-500 mt-1 text-right">Sisa Target: <strong>{{ $item->sisa_jam }} Jam</strong></p>
                            </div>

                            <!-- Status Logbook & Action Buttons -->
                            <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto border-t lg:border-t-0 lg:border-l border-gray-100 pt-4 lg:pt-0 lg:pl-6 shrink-0">
                                
                                <!-- Status Indicator Berdasarkan Tahap SPV vs Dosen -->
                                @if($canAssist)
                                    <div class="flex items-center justify-center bg-orange-50 border border-orange-200 rounded-xl px-3.5 py-2 w-full sm:w-auto">
                                        <i class="fas fa-file-signature text-orange-500 mr-2 text-sm animate-pulse"></i>
                                        <div>
                                            <p class="text-[9px] text-orange-800 font-bold uppercase tracking-wider">Status Logbook</p>
                                            <p class="text-xs text-orange-600 font-bold">{{ $item->logbook_ready_dosen }} Siap Diasistensi</p>
                                        </div>
                                    </div>
                                @elseif($isWaitingSpv)
                                    <div class="flex items-center justify-center bg-amber-50 border border-amber-200 rounded-xl px-3.5 py-2 w-full sm:w-auto" title="Menunggu persetujuan pembimbing lapangan (SPV)">
                                        <i class="fas fa-user-clock text-amber-500 mr-2 text-sm"></i>
                                        <div>
                                            <p class="text-[9px] text-amber-800 font-bold uppercase tracking-wider">Status Logbook</p>
                                            <p class="text-xs text-amber-700 font-bold">{{ $item->logbook_waiting_spv }} Menunggu SPV</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center justify-center bg-emerald-50 border border-emerald-200 rounded-xl px-3.5 py-2 w-full sm:w-auto">
                                        <i class="fas fa-check-circle text-emerald-500 mr-2 text-sm"></i>
                                        <div>
                                            <p class="text-[9px] text-emerald-800 font-bold uppercase tracking-wider">Status Logbook</p>
                                            <p class="text-xs text-emerald-600 font-bold">Up to Date (Aman)</p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="flex gap-2 w-full sm:w-auto items-center">
                                    <!-- TOMBOL IKON PROFIL DENGAN HANDLER SHOWMODAL -->
                                    <button type="button" 
                                            @click="showModal(@js($item))" 
                                            class="flex-1 sm:flex-none bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:text-vokasi-primary font-bold text-xs py-2.5 px-3 rounded-xl transition-colors shadow-sm flex items-center justify-center gap-1" 
                                            title="Buka Profil & Rincian Progres Mahasiswa">
                                        <i class="fas fa-user text-sm"></i>
                                    </button>

                                    <!-- Tombol Asistensi Logbook -->
                                    @if($canAssist)
                                        <a href="{{ route('dashboard-verifikasi-daftar-mahasiswa-perlu-verifikasi') }}" 
                                           class="flex-1 sm:flex-none bg-vokasi-primary hover:bg-vokasi-dark text-white font-bold text-xs py-2.5 px-4 rounded-xl transition-colors shadow-sm flex items-center justify-center gap-1.5"
                                           title="Buka antrean logbook untuk asistensi">
                                            <i class="fas fa-file-signature"></i> Asistensi Logbook
                                        </a>
                                    @elseif($isWaitingSpv)
                                        <button type="button" 
                                                disabled 
                                                class="flex-1 sm:flex-none bg-gray-100 border border-gray-300 text-gray-400 font-bold text-xs py-2.5 px-4 rounded-xl cursor-not-allowed flex items-center justify-center gap-1.5 shadow-none" 
                                                title="Menunggu validasi dari Supervisor (SPV) Mitra Lapangan terlebih dahulu">
                                            <i class="fas fa-lock text-[10px]"></i> Menunggu SPV
                                        </button>
                                    @else
                                        <a href="{{ route('dashboard-verifikasi-daftar-mahasiswa-semua-laporan', ['search' => $mhs?->name]) }}" 
                                           class="flex-1 sm:flex-none bg-white border border-gray-300 text-gray-600 hover:bg-gray-50 font-bold text-xs py-2.5 px-4 rounded-xl transition-colors shadow-sm flex items-center justify-center gap-1.5"
                                           title="Lihat riwayat logbook mahasiswa">
                                            <i class="fas fa-history"></i> Riwayat Logbook
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white p-12 text-center rounded-2xl border border-gray-200 text-gray-400">
                            <i class="fas fa-user-graduate text-4xl mb-3 block"></i>
                            <p class="font-bold text-gray-600 text-base">Belum Ada Mahasiswa Bimbingan</p>
                            <p class="text-xs mt-1">Daftar mahasiswa magang yang ditugaskan kepada Anda oleh Admin Prodi akan tampil di sini.</p>
                        </div>
                    @endforelse

                </div>

            </div>

            <!-- FOOTER -->
            <footer class="mt-auto py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
                Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
            </footer>

        </main>

        <!-- ========================================================================= -->
        <!-- MODAL POPUP: DETAIL LENGKAP PROFIL & PROGRESS MAHASISWA BIMBINGAN -->
        <!-- ========================================================================= -->
        <div x-show="openDetailModal" 
             x-cloak 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            
            <div @click.away="openDetailModal = false" class="bg-white rounded-3xl shadow-2xl border border-gray-200 w-full max-w-2xl overflow-hidden max-h-[90vh] flex flex-col" x-if="activeMhs">
                
                <!-- Modal Header -->
                <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center shrink-0">
                    <div class="flex items-center gap-2.5">
                        <i class="fas fa-id-card text-xl"></i>
                        <div>
                            <h3 class="font-bold text-base leading-none">Detail Profil & Progres Bimbingan</h3>
                            <p class="text-[11px] text-white/80 mt-0.5">Informasi akademik dan pencapaian magang mahasiswa</p>
                        </div>
                    </div>
                    <button @click="openDetailModal = false" class="text-white/80 hover:text-white hover:bg-white/10 p-1.5 rounded-xl transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="p-6 space-y-5 overflow-y-auto custom-scrollbar flex-1 text-xs">
                    
                    <!-- 1. KARTU HEADER PROFIL MAHASISWA -->
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-200">
                        <img :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent(activeMhs?.user?.name || 'M') + '&background=E6FFFA&color=0D9488&size=72'" class="w-16 h-16 rounded-2xl border-2 border-teal-200 shadow-md shrink-0">
                        <div class="text-center sm:text-left flex-1">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                <h4 class="text-base font-bold text-gray-800" x-text="activeMhs?.user?.name"></h4>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border inline-block" 
                                      :class="activeMhs?.status_seleksi === 'selesai' ? 'bg-blue-100 text-blue-700 border-blue-200' : 'bg-emerald-100 text-emerald-700 border-emerald-200'" 
                                      x-text="activeMhs?.status_seleksi === 'selesai' ? 'Selesai Magang / Stase' : 'Aktif Magang'">
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 font-mono mt-0.5" x-text="'NIM: ' + (activeMhs?.user?.mahasiswa_profile?.nim || '-')"></p>
                            <p class="text-xs font-semibold text-vokasi-primary mt-1" x-text="activeMhs?.user?.mahasiswa_profile?.prodi?.nama_prodi || 'Program Pendidikan Vokasi'"></p>
                        </div>
                    </div>

                    <!-- 2. DETAIL TEMPAT MAGANG & JALUR -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200">
                            <span class="text-gray-400 block uppercase font-bold text-[10px]"><i class="fas fa-building mr-1"></i> Perusahaan / Instansi</span>
                            <span class="font-bold text-gray-800 text-sm mt-0.5 block" x-text="activeMhs?.jalur_magang === 'mandiri' ? activeMhs?.nama_instansi_mandiri : activeMhs?.lowongan?.perusahaan?.nama_perusahaan"></span>
                            <span class="text-[10px] text-gray-500" x-text="activeMhs?.lowongan?.perusahaan?.alamat || 'Lokasi Terdaftar'"></span>
                        </div>

                        <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200">
                            <span class="text-gray-400 block uppercase font-bold text-[10px]"><i class="fas fa-briefcase mr-1"></i> Posisi / Stase Unit</span>
                            <span class="font-bold text-vokasi-primary text-sm mt-0.5 block" x-text="activeMhs?.jalur_magang === 'mandiri' ? (activeMhs?.divisi_mandiri || 'Magang Mandiri') : activeMhs?.lowongan?.judul_posisi"></span>
                            <span class="text-[10px] font-semibold uppercase" :class="activeMhs?.jalur_magang === 'mandiri' ? 'text-purple-600' : 'text-blue-600'" x-text="'Jalur: ' + (activeMhs?.jalur_magang || 'Reguler')"></span>
                        </div>
                    </div>

                    <!-- 3. DETAIL PROGRES AKUMULASI JAM MAGANG -->
                    <div class="p-4 bg-emerald-50/60 rounded-2xl border border-emerald-200 space-y-3">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-xs font-bold text-emerald-950 block">Akumulasi Jam Praktik Magang</span>
                                <span class="text-[11px] text-emerald-700">Target Kurikulum: <strong x-text="activeMhs?.target_jam + ' Jam'"></strong></span>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-extrabold text-emerald-700" x-text="activeMhs?.total_jam + ' Jam'"></span>
                                <span class="text-[11px] font-bold text-emerald-600 block" x-text="'(' + activeMhs?.persentase_jam + '% Terpenuhi)'"></span>
                            </div>
                        </div>

                        <!-- Progress Bar Besar -->
                        <div class="w-full bg-emerald-100 rounded-full h-3 overflow-hidden border border-emerald-200">
                            <div class="bg-emerald-600 h-3 rounded-full transition-all duration-500" :style="'width: ' + activeMhs?.persentase_jam + '%'"></div>
                        </div>

                        <div class="flex justify-between text-[11px] text-emerald-800 pt-1 border-t border-emerald-200/60 font-medium">
                            <span>Sisa Target yang Harus Dipenuhi:</span>
                            <span class="font-bold" x-text="activeMhs?.sisa_jam + ' Jam'"></span>
                        </div>
                    </div>

                    <!-- 4. STATUS ASISTENSI & LOGBOOK -->
                    <div>
                        <h5 class="font-bold text-gray-800 text-xs uppercase mb-2 flex items-center gap-1.5">
                            <i class="fas fa-book-open text-vokasi-primary"></i> Rincian Status Logbook
                        </h5>
                        <div class="grid grid-cols-3 gap-2.5">
                            <div class="p-3 bg-orange-50 border border-orange-200 rounded-xl text-center">
                                <span class="block text-[10px] font-bold text-orange-800 uppercase">Siap Diasistensi</span>
                                <span class="text-base font-extrabold text-orange-600 mt-1 block" x-text="(activeMhs?.logbook_ready_dosen || 0) + ' Berkas'"></span>
                                <span class="text-[9px] text-orange-700">Sudah di-Approve SPV</span>
                            </div>

                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-center">
                                <span class="block text-[10px] font-bold text-amber-800 uppercase">Menunggu SPV</span>
                                <span class="text-base font-extrabold text-amber-600 mt-1 block" x-text="(activeMhs?.logbook_waiting_spv || 0) + ' Berkas'"></span>
                                <span class="text-[9px] text-amber-700">Di Pembimbing Lapangan</span>
                            </div>

                            <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-center">
                                <span class="block text-[10px] font-bold text-blue-800 uppercase">Status Revisi</span>
                                <span class="text-base font-extrabold text-blue-600 mt-1 block" x-text="(activeMhs?.logbook_revisi || 0) + ' Berkas'"></span>
                                <span class="text-[9px] text-blue-700">Perbaikan Mahasiswa</span>
                            </div>
                        </div>
                    </div>

                    <!-- 5. INFORMASI KONTAK MAHASISWA -->
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-2">
                        <h5 class="font-bold text-slate-800 text-xs uppercase flex items-center gap-1.5">
                            <i class="fas fa-address-book text-slate-600"></i> Kontak Langsung
                        </h5>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-slate-700">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-envelope text-slate-400 w-4"></i>
                                <span class="truncate">Email: <strong x-text="activeMhs?.user?.email || '-'"></strong></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fab fa-whatsapp text-emerald-500 w-4"></i>
                                <span>WhatsApp: <strong x-text="activeMhs?.user?.mahasiswa_profile?.no_hp || '-'"></strong></span>
                            </div>
                        </div>
                    </div>

                </div>
<!-- Modal Footer -->
                <div class="p-4 bg-gray-50 border-t border-gray-100 flex flex-wrap justify-between items-center shrink-0 gap-3">
                    <button type="button" @click="openDetailModal = false" class="px-5 py-2.5 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-100 font-bold text-xs transition-colors shadow-sm">
                        Tutup
                    </button>

                    <div class="flex items-center gap-2">
                        <!-- TOMBOL SAKLAR LOGBOOK SUSULAN -->
                        <form :action="'{{ url('/dashboard-dosen/mahasiswa-bimbingan') }}/' + activeMhs?.id + '/toggle-susulan'" method="POST" class="inline-block">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="px-4 py-2.5 border text-xs font-bold rounded-xl shadow-sm transition-colors flex items-center gap-1.5"
                                    :class="activeMhs?.allow_logbook_susulan ? 'bg-red-50 text-red-600 border-red-200 hover:bg-red-100' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
                                    :title="activeMhs?.allow_logbook_susulan ? 'Tutup Akses Logbook Terlewat' : 'Buka Akses Logbook Terlewat (Susulan) untuk mahasiswa ini'">
                                <i class="fas" :class="activeMhs?.allow_logbook_susulan ? 'fa-lock' : 'fa-unlock-alt'"></i>
                                <span x-text="activeMhs?.allow_logbook_susulan ? 'Tutup Akses Susulan' : 'Buka Izin Logbook Terlewat'"></span>
                            </button>
                        </form>

                        <template x-if="activeMhs?.logbook_ready_dosen > 0">
                            <a href="{{ route('dashboard-verifikasi-daftar-mahasiswa-perlu-verifikasi') }}" 
                               class="px-5 py-2.5 bg-vokasi-primary hover:bg-vokasi-dark text-white font-bold text-xs rounded-xl shadow-sm transition-colors flex items-center gap-2">
                                <i class="fas fa-file-signature"></i> Asistensi Logbook
                            </a>
                        </template>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection