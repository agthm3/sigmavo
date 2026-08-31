@extends('layouts.dashboard')

@section('content')
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative custom-scrollbar"
      x-data="{ activeTab: 'semua' }">
    
    <div class="max-w-5xl mx-auto w-full flex-1">
        
        <!-- BANNER MODE MONITORING IMPERSONATE -->
        <div class="mb-6 bg-purple-600 border border-purple-700 p-4 rounded-2xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-white shadow-lg relative overflow-hidden">
            <div class="absolute -right-4 -top-4 opacity-10">
                <i class="fas fa-user-secret text-8xl"></i>
            </div>
            <div class="flex items-center gap-3 relative z-10">
                <div class="bg-white/20 p-2.5 rounded-xl shrink-0">
                    <i class="fas fa-binoculars text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-base">Mode Pantau (Read-Only) Aktif</h3>
                    <p class="text-xs text-purple-100 mt-0.5">
                        Menampilkan layar antrean Perlu Verifikasi milik: 
                        <strong class="bg-white/20 px-2 py-0.5 rounded text-white">{{ $targetUser->name }}</strong>
                    </p>
                </div>
            </div>
            <a href="{{ route('dashboard-manajemen-monitoring') }}" class="px-5 py-2.5 bg-white text-purple-700 hover:bg-gray-100 font-bold text-xs rounded-xl transition shadow-sm relative z-10 shrink-0">
                <i class="fas fa-sign-out-alt mr-1"></i> Tutup & Kembali
            </a>
        </div>

        <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Antrean Verifikasi (Pantauan)</h2>
                <p class="text-sm text-gray-500 mt-1">Daftar logbook dan pengajuan izin/sakit yang sedang ditunggu konfirmasinya oleh pengguna ini.</p>
            </div>
            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                <i class="fas fa-tasks mr-1.5"></i> {{ count($pendingLogbooks) + count($pendingAbsensis) }} Antrean Menunggu
            </span>
        </div>

        <!-- QUICK FILTER TABS -->
        <div class="flex overflow-x-auto gap-2 mb-6 custom-scrollbar pb-2">
            <button @click="activeTab = 'semua'" :class="activeTab === 'semua' ? 'bg-vokasi-primary text-white font-bold' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'" class="px-4 py-2 rounded-xl text-xs whitespace-nowrap shadow-sm transition-colors">
                Semua ({{ count($pendingLogbooks) + count($pendingAbsensis) }})
            </button>
            <button @click="activeTab = 'logbook'" :class="activeTab === 'logbook' ? 'bg-vokasi-primary text-white font-bold' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'" class="px-4 py-2 rounded-xl text-xs whitespace-nowrap shadow-sm transition-colors">
                Logbook ({{ count($pendingLogbooks) }})
            </button>
            <button @click="activeTab = 'absensi'" :class="activeTab === 'absensi' ? 'bg-vokasi-primary text-white font-bold' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'" class="px-4 py-2 rounded-xl text-xs whitespace-nowrap shadow-sm transition-colors">
                Izin / Sakit / Flag ({{ count($pendingAbsensis) }})
            </button>
        </div>

        <div class="space-y-6">
            
            <!-- LIST ITEM 1: LOGBOOK HARIAN -->
            @forelse($pendingLogbooks as $logbook)
            <div x-show="activeTab === 'semua' || activeTab === 'logbook'" class="bg-white rounded-2xl shadow-sm border overflow-hidden {{ $logbook->is_susulan ? 'border-red-300' : 'border-yellow-200' }}">
                <div class="{{ $logbook->is_susulan ? 'bg-red-50/60 border-red-100' : 'bg-yellow-50/60 border-yellow-100' }} border-b p-4 flex justify-between items-center text-xs">
                    <div class="flex items-center gap-2">
                        <span class="{{ $logbook->is_susulan ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }} p-1.5 rounded-lg"><i class="fas fa-book"></i></span>
                        <h3 class="font-bold text-gray-800 text-sm">
                            Pengajuan Logbook
                            @if($logbook->is_susulan)
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-red-100 text-red-700 border border-red-200 uppercase tracking-wider">
                                    <i class="fas fa-history mr-1"></i> Susulan Terlewat
                                </span>
                            @else
                                Harian
                            @endif
                        </h3>
                    </div>
                    <span class="font-semibold text-gray-500"><i class="far fa-clock mr-1"></i> Tanggal Logbook: {{ $logbook->tanggal->format('d M Y') }}</span>
                </div>
                
                <div class="p-5 flex flex-col md:flex-row gap-6">
                    <div class="flex-1 space-y-3 text-xs">
                        <div class="flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($logbook->user->name) }}&background=37A7AC&color=fff" class="w-10 h-10 rounded-full border shadow-sm">
                            <div>
                                <p class="font-bold text-gray-800 text-sm">{{ $logbook->user->name }} <span class="text-gray-400 font-normal ml-1">NIM: {{ $logbook->user->mahasiswaProfile->nim ?? '-' }}</span></p>
                                <p class="text-[11px] text-gray-500">{{ $logbook->pendaftaran->perusahaan->nama_perusahaan ?? 'Perusahaan Mitra' }} • {{ $logbook->user->mahasiswaProfile->prodi->nama_prodi ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-gray-700 leading-relaxed text-justify space-y-2">
                            <p>{{ $logbook->uraian_kegiatan }}</p>

                            @if(!empty($logbook->mata_kuliah) && is_array($logbook->mata_kuliah))
                                <div class="flex flex-wrap gap-1 pt-1 border-t border-gray-200/60">
                                    @foreach($logbook->mata_kuliah as $mk)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-50 text-blue-700 text-[10px] font-semibold border border-blue-200">
                                            <i class="fas fa-book-open text-[9px] mr-1"></i> {{ $mk }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            @if($logbook->foto_dokumentasi)
                                <div class="pt-2">
                                    <a href="{{ asset('storage/' . $logbook->foto_dokumentasi) }}" target="_blank" class="inline-flex items-center gap-1.5 text-vokasi-primary font-bold hover:underline">
                                        <i class="fas fa-image"></i> Lihat Foto Dokumentasi Kegiatan
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- DISABLED ACTION AREA (KARENA MODE PANTAU) -->
                    <div class="w-full md:w-64 shrink-0 flex flex-col justify-end space-y-2 border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-6 text-xs">
                        <div class="p-3 bg-purple-50 border border-purple-200 rounded-xl text-purple-700 text-center text-[10px] font-bold">
                            <i class="fas fa-eye text-lg block mb-1 text-purple-400"></i> Read-Only Mode
                        </div>
                        <button disabled class="w-full bg-gray-200 text-gray-400 text-xs font-bold py-2.5 rounded-xl cursor-not-allowed flex items-center justify-center">
                            <i class="fas fa-check mr-1.5"></i> Approve Logbook
                        </button>
                        <button disabled class="w-full bg-gray-100 border border-gray-200 text-gray-400 text-xs font-bold py-2.5 rounded-xl cursor-not-allowed flex items-center justify-center">
                            <i class="fas fa-undo mr-1.5"></i> Minta Revisi
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div x-show="activeTab === 'semua' || activeTab === 'logbook'" class="p-8 text-center bg-white rounded-2xl border border-gray-200 text-gray-400">
                <i class="fas fa-check-circle text-3xl mb-2 text-emerald-500 block"></i> Tidak ada antrean pengajuan logbook.
            </div>
            @endforelse

            <!-- LIST ITEM 2: IZIN / SAKIT / ABSENSI -->
            @forelse($pendingAbsensis as $absensi)
            <div x-show="activeTab === 'semua' || activeTab === 'absensi'" class="bg-white rounded-2xl shadow-sm border border-orange-200 overflow-hidden">
                <div class="bg-orange-50/50 border-b border-orange-100 p-4 flex justify-between items-center text-xs">
                    <div class="flex items-center gap-2">
                        <span class="bg-orange-100 text-orange-700 p-1.5 rounded-lg"><i class="fas fa-notes-medical"></i></span>
                        <h3 class="font-bold text-gray-800 text-sm capitalize">Pengajuan {{ $absensi->tipe_kehadiran === 'hadir' ? 'Flag Absensi' : $absensi->tipe_kehadiran }}</h3>
                    </div>
                    <span class="font-semibold text-gray-500"><i class="far fa-clock mr-1"></i> Tanggal: {{ $absensi->tanggal->format('d M Y') }}</span>
                </div>

                <div class="p-5 flex flex-col md:flex-row gap-6">
                    <div class="flex-1 space-y-3 text-xs">
                        <div class="flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($absensi->user->name) }}&background=f3f4f6&color=37A7AC" class="w-10 h-10 rounded-full border shadow-sm">
                            <div>
                                <p class="font-bold text-gray-800 text-sm">{{ $absensi->user->name }} <span class="text-gray-400 font-normal ml-1">NIM: {{ $absensi->user->mahasiswaProfile->nim ?? '-' }}</span></p>
                                <p class="text-[11px] text-gray-500">{{ $absensi->pendaftaran->perusahaan->nama_perusahaan ?? 'Perusahaan Mitra' }}</p>
                            </div>
                        </div>

                        <div class="bg-orange-50/60 border border-orange-100 rounded-xl p-4 text-gray-700 space-y-2">
                            <p><strong>Alasan / Keterangan:</strong> {{ $absensi->alasan_izin ?? 'Tidak ada keterangan.' }}</p>
                            
                            @if($absensi->surat_izin)
                                <a href="{{ asset('storage/' . $absensi->surat_izin) }}" target="_blank" class="inline-flex items-center text-xs font-bold bg-white text-orange-600 border border-orange-200 px-3 py-1.5 rounded-lg hover:bg-orange-50 transition-colors">
                                    <i class="fas fa-file-medical-alt mr-2"></i> Lihat Surat Keterangan / Bukti
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- DISABLED ACTION AREA (KARENA MODE PANTAU) -->
                    <div class="w-full md:w-64 shrink-0 flex flex-col justify-end space-y-2 border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-6 text-xs">
                        <div class="p-3 bg-purple-50 border border-purple-200 rounded-xl text-purple-700 text-center text-[10px] font-bold">
                            <i class="fas fa-eye text-lg block mb-1 text-purple-400"></i> Read-Only Mode
                        </div>
                        <button disabled class="w-full bg-gray-200 text-gray-400 text-xs font-bold py-2.5 rounded-xl cursor-not-allowed flex items-center justify-center">
                            <i class="fas fa-check-circle mr-1.5"></i> Setujui Pengajuan
                        </button>
                        <button disabled class="w-full bg-gray-100 border border-gray-200 text-gray-400 text-xs font-bold py-2.5 rounded-xl cursor-not-allowed flex items-center justify-center">
                            Tolak Pengajuan
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div x-show="activeTab === 'semua' || activeTab === 'absensi'" class="p-8 text-center bg-white rounded-2xl border border-gray-200 text-gray-400">
                <i class="fas fa-check-circle text-3xl mb-2 text-emerald-500 block"></i> Tidak ada antrean pengajuan izin / sakit.
            </div>
            @endforelse

        </div>
    </div>

    <!-- FOOTER -->
    <footer class="mt-8 py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
        Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
    </footer>

</main>
@endsection