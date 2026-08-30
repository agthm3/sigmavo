@extends('layouts.dashboard')

@section('content')
    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50"
         x-data="{ 
             openDetailModal: false, 
             activeMhs: null,
             showProfile(data) {
                 this.activeMhs = data;
                 this.openDetailModal = true;
             }
         }">

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative custom-scrollbar">
            
            <div class="max-w-7xl mx-auto w-full flex-1 space-y-6">
                
                <!-- Page Header & Stats -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Direktori Mahasiswa</h2>
                        <p class="text-sm text-gray-500 mt-1">Kelola dan pantau seluruh daftar mahasiswa magang di bawah bimbingan Anda.</p>
                    </div>
                    
                    <div class="flex items-center gap-3 bg-white px-4 py-2.5 border border-gray-200 rounded-xl shadow-sm text-xs">
                        <div class="flex items-center gap-2 border-r border-gray-200 pr-4">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-gray-600">Aktif: <strong class="text-gray-800">{{ $totalAktif }}</strong></span>
                        </div>
                        <div class="flex items-center gap-2 border-r border-gray-200 pr-4">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span class="text-gray-600">Selesai: <strong class="text-gray-800">{{ $totalSelesai }}</strong></span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-gray-500">Total Terdata:</span>
                            <span class="font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded-md">{{ $totalSemua }}</span>
                        </div>
                    </div>
                </div>

                <!-- FLASH MESSAGES -->
                @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center justify-between text-sm shadow-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900"><i class="fas fa-times"></i></button>
                </div>
                @endif

                @if(session('error'))
                <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center justify-between text-sm shadow-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-circle text-red-600 text-lg"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900"><i class="fas fa-times"></i></button>
                </div>
                @endif

                <!-- TABEL MAHASISWA -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    
                    <!-- Table Toolbar -->
                    <form action="{{ route('dashboard-dosen-daftar-mahasiswa') }}" method="GET" class="p-5 border-b border-gray-100 flex flex-col md:flex-row justify-between md:items-center gap-4 bg-gray-50/50">
                        <div class="relative w-full md:w-80">
                            <i class="fas fa-search absolute left-3.5 top-3 text-gray-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIM, atau instansi..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-primary">
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            <select name="status" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-xs rounded-xl focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm font-medium">
                                <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}>Semua Status</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif Magang</option>
                                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai Magang / Stase</option>
                                <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu Penempatan</option>
                            </select>

                            <!-- EXPORT BUTTON -->
                            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2 px-3.5 rounded-xl transition-colors shadow-sm flex items-center gap-1.5">
                                <i class="fas fa-file-excel"></i> Export CSV
                            </a>
                        </div>
                    </form>

                    <!-- Responsive Table -->
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[1000px]">
                            <thead>
                                <tr class="bg-gray-100/60 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <th class="p-4 w-12 text-center">No</th>
                                    <th class="p-4 w-64">Identitas Mahasiswa</th>
                                    <th class="p-4 w-60">Penempatan & Stase</th>
                                    <th class="p-4 w-52">Akumulasi Jam</th>
                                    <th class="p-4 w-32 text-center">Status</th>
                                    <th class="p-4 w-28 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                
                                @forelse($mahasiswas as $index => $item)
                                    @php
                                        $mhs = $item->user;
                                        $profile = $mhs?->mahasiswaProfile;
                                        $isMandiri = $item->jalur_magang === 'mandiri';
                                        $namaMitra = $isMandiri ? $item->nama_instansi_mandiri : ($item->lowongan->perusahaan->nama_perusahaan ?? '-');
                                        $judulPosisi = $isMandiri ? ($item->divisi_mandiri ?? 'Pengajuan Mandiri') : ($item->lowongan->judul_posisi ?? '-');
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="p-4 text-center text-gray-500 font-medium">{{ $mahasiswas->firstItem() + $index }}</td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-3">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($mhs?->name ?? 'Mhs') }}&background=E6FFFA&color=0D9488&size=40" alt="Avatar" class="w-10 h-10 rounded-full border border-teal-200 shrink-0">
                                                <div>
                                                    <p class="font-bold text-gray-800 text-xs hover:text-vokasi-primary cursor-pointer" @click="showProfile(@js($item))">{{ $mhs?->name ?? 'User Terhapus' }}</p>
                                                    <p class="text-[11px] text-gray-500 font-mono">{{ $profile?->nim ?? '-' }}</p>
                                                    <p class="text-[10px] text-vokasi-primary font-semibold">{{ $profile?->prodi?->nama_prodi ?? 'Vokasi' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <p class="font-bold text-gray-800 text-xs">{{ $namaMitra }}</p>
                                            <p class="text-[11px] text-gray-500">{{ $judulPosisi }}</p>
                                            <span class="inline-block mt-0.5 text-[9px] font-bold uppercase px-1.5 py-0.5 rounded border {{ $isMandiri ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-blue-50 text-blue-700 border-blue-200' }}">
                                                {{ $item->jalur_magang ?? 'REGULER' }}
                                            </span>
                                        </td>
                                        <td class="p-4">
                                            <div class="flex justify-between text-xs mb-1">
                                                <span class="font-bold text-emerald-600">{{ $item->total_jam }} Jam</span>
                                                <span class="text-gray-500 text-[11px]">{{ $item->persentase_jam }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden border border-gray-200">
                                                <div class="bg-vokasi-primary h-2 rounded-full transition-all duration-500" style="width: {{ $item->persentase_jam }}%"></div>
                                            </div>
                                            <p class="text-[10px] text-gray-400 mt-1">Target: {{ $item->target_jam }} Jam</p>
                                        </td>
                                        <td class="p-4 text-center">
                                            @if($item->status_seleksi === 'diterima')
                                                <span class="inline-flex items-center px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5 animate-pulse"></span> Aktif
                                                </span>
                                            @elseif($item->status_seleksi === 'selesai')
                                                <span class="inline-flex items-center px-2.5 py-1 bg-blue-100 text-blue-700 text-[10px] font-bold rounded-full border border-blue-200">
                                                    <i class="fas fa-check-double mr-1"></i> Selesai
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full border border-amber-200">
                                                    Proses
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-4 text-center">
                                            <button type="button" 
                                                    @click="showProfile(@js($item))" 
                                                    class="text-gray-600 hover:text-vokasi-primary bg-gray-100 hover:bg-teal-50 border border-gray-200 hover:border-teal-200 py-1.5 px-3 rounded-xl transition-colors text-xs font-bold flex items-center justify-center gap-1 shadow-sm">
                                                <i class="fas fa-id-card text-[11px]"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-gray-400">
                                            <i class="fas fa-users-slash text-3xl mb-2 block"></i> Belum ada data mahasiswa bimbingan ditemukan.
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Controls -->
                    <div class="p-4 border-t border-gray-100 bg-white">
                        {{ $mahasiswas->links() }}
                    </div>

                </div>

            </div>

            <!-- FOOTER -->
            <footer class="mt-8 py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
                Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
            </footer>

        </main>

        <!-- ========================================================================= -->
        <!-- MODAL POPUP: DETAIL PROFIL LENGKAP MAHASISWA & PROGRES -->
        <!-- ========================================================================= -->
        <div x-show="openDetailModal" 
             x-cloak 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            
            <div @click.away="openDetailModal = false" class="bg-white rounded-3xl shadow-2xl border border-gray-200 w-full max-w-2xl overflow-hidden max-h-[90vh] flex flex-col" x-if="activeMhs">
                
                <!-- Modal Header -->
                <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center shrink-0">
                    <div class="flex items-center gap-2.5">
                        <i class="fas fa-user-graduate text-xl"></i>
                        <div>
                            <h3 class="font-bold text-base leading-none">Profil & Riwayat Mahasiswa</h3>
                            <p class="text-[11px] text-white/80 mt-0.5">Informasi akademik, stase magang, dan akumulasi jam kerja</p>
                        </div>
                    </div>
                    <button @click="openDetailModal = false" class="text-white/80 hover:text-white hover:bg-white/10 p-1.5 rounded-xl transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="p-6 space-y-5 overflow-y-auto custom-scrollbar flex-1 text-xs">
                    
                    <!-- 1. KARTU HEADER PROFIL -->
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-200">
                        <img :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent(activeMhs?.user?.name || 'M') + '&background=E6FFFA&color=0D9488&size=72'" class="w-16 h-16 rounded-2xl border-2 border-teal-200 shadow-md shrink-0">
                        <div class="text-center sm:text-left flex-1">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                <h4 class="text-base font-bold text-gray-800" x-text="activeMhs?.user?.name"></h4>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border inline-block" 
                                      :class="activeMhs?.status_seleksi === 'selesai' ? 'bg-blue-100 text-blue-700 border-blue-200' : 'bg-emerald-100 text-emerald-700 border-emerald-200'" 
                                      x-text="activeMhs?.status_seleksi === 'selesai' ? 'Selesai Magang' : 'Aktif Magang'">
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 font-mono mt-0.5" x-text="'NIM: ' + (activeMhs?.user?.mahasiswa_profile?.nim || '-')"></p>
                            <p class="text-xs font-semibold text-vokasi-primary mt-1" x-text="activeMhs?.user?.mahasiswa_profile?.prodi?.nama_prodi || 'Program Pendidikan Vokasi'"></p>
                        </div>
                    </div>

                    <!-- 2. DETAIL PENEMPATAN & PEMBIMBING -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200 space-y-1">
                            <span class="text-gray-400 block uppercase font-bold text-[10px]"><i class="fas fa-building mr-1"></i> Tempat & Stase</span>
                            <p class="font-bold text-gray-800 text-xs" x-text="activeMhs?.jalur_magang === 'mandiri' ? activeMhs?.nama_instansi_mandiri : activeMhs?.lowongan?.perusahaan?.nama_perusahaan"></p>
                            <p class="text-vokasi-primary font-semibold text-[11px]" x-text="activeMhs?.jalur_magang === 'mandiri' ? (activeMhs?.divisi_mandiri || 'Mandiri') : activeMhs?.lowongan?.judul_posisi"></p>
                        </div>

                        <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200 space-y-1">
                            <span class="text-gray-400 block uppercase font-bold text-[10px]"><i class="fas fa-user-tie mr-1"></i> Dosen Pembimbing (DPL)</span>
                            <p class="font-bold text-gray-800 text-xs" x-text="activeMhs?.dosen?.name || 'Belum Ditugaskan'"></p>
                            <p class="text-gray-500 text-[11px]" x-text="'Jalur: ' + (activeMhs?.jalur_magang || 'Reguler').toUpperCase()"></p>
                        </div>
                    </div>

                    <!-- 3. DETAIL PROGRES JAM MAGANG -->
                    <div class="p-4 bg-emerald-50/60 rounded-2xl border border-emerald-200 space-y-3">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-xs font-bold text-emerald-950 block">Progres Akumulasi Jam Magang</span>
                                <span class="text-[11px] text-emerald-700">Target Kurikulum: <strong x-text="activeMhs?.target_jam + ' Jam'"></strong></span>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-extrabold text-emerald-700" x-text="activeMhs?.total_jam + ' Jam'"></span>
                                <span class="text-[11px] font-bold text-emerald-600 block" x-text="'(' + activeMhs?.persentase_jam + '% Terpenuhi)'"></span>
                            </div>
                        </div>

                        <div class="w-full bg-emerald-100 rounded-full h-3 overflow-hidden border border-emerald-200">
                            <div class="bg-emerald-600 h-3 rounded-full transition-all duration-500" :style="'width: ' + activeMhs?.persentase_jam + '%'"></div>
                        </div>

                        <div class="flex justify-between text-[11px] text-emerald-800 pt-1 border-t border-emerald-200/60 font-medium">
                            <span>Sisa Target yang Harus Dipenuhi:</span>
                            <span class="font-bold" x-text="activeMhs?.sisa_jam + ' Jam'"></span>
                        </div>
                    </div>

                    <!-- 4. STATUS ASISTENSI LOGBOOK -->
                    <div>
                        <h5 class="font-bold text-gray-800 text-xs uppercase mb-2 flex items-center gap-1.5">
                            <i class="fas fa-book-open text-vokasi-primary"></i> Ringkasan Logbook Harian
                        </h5>
                        <div class="grid grid-cols-3 gap-2.5">
                            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-center">
                                <span class="block text-[10px] font-bold text-emerald-800 uppercase">Telah Disetujui</span>
                                <span class="text-base font-extrabold text-emerald-600 mt-1 block" x-text="(activeMhs?.logbook_approved || 0) + ' Log'"></span>
                                <span class="text-[9px] text-emerald-700">Validasi Lengkap</span>
                            </div>

                            <div class="p-3 bg-orange-50 border border-orange-200 rounded-xl text-center">
                                <span class="block text-[10px] font-bold text-orange-800 uppercase">Siap Asistensi</span>
                                <span class="text-base font-extrabold text-orange-600 mt-1 block" x-text="(activeMhs?.logbook_waiting_dosen || 0) + ' Log'"></span>
                                <span class="text-[9px] text-orange-700">Sudah di-Approve SPV</span>
                            </div>

                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-center">
                                <span class="block text-[10px] font-bold text-amber-800 uppercase">Menunggu SPV</span>
                                <span class="text-base font-extrabold text-amber-600 mt-1 block" x-text="(activeMhs?.logbook_waiting_spv || 0) + ' Log'"></span>
                                <span class="text-[9px] text-amber-700">Tahap Mitra</span>
                            </div>
                        </div>
                    </div>

                    <!-- 5. INFORMASI KONTAK -->
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-2">
                        <h5 class="font-bold text-slate-800 text-xs uppercase flex items-center gap-1.5">
                            <i class="fas fa-address-book text-slate-600"></i> Kontak Mahasiswa
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
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection