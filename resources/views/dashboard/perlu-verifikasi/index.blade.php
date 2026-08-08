@extends('layouts.dashboard')

@section('content')
<!-- NOTIFIKASI SUKSES -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between text-sm shadow-sm">
        <div class="flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900"><i class="fas fa-times"></i></button>
    </div>
    @endif

    <!-- NOTIFIKASI ERROR / ATURAN PRESENSI BELUM TERPENUHI -->
    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl flex items-center justify-between text-sm shadow-sm">
        <div class="flex items-center gap-2">
            <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900"><i class="fas fa-times"></i></button>
    </div>
    @endif
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative custom-scrollbar"
      x-data="{ activeTab: 'semua' }">
    
    <div class="max-w-5xl mx-auto w-full flex-1">
        
        <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Antrean Verifikasi</h2>
                <p class="text-sm text-gray-500 mt-1">Tinjau logbook harian, pengajuan izin/sakit, dan flag absensi dari mahasiswa bimbingan Anda.</p>
            </div>
            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                <i class="fas fa-tasks mr-1.5"></i> {{ count($pendingLogbooks) + count($pendingAbsensis) }} Antrean Menunggu
            </span>
        </div>

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

        <!-- QUICK FILTER TABS -->
        <div class="flex overflow-x-auto gap-2 mb-6 custom-scrollbar pb-2">
            <button @click="activeTab = 'semua'" :class="activeTab === 'semua' ? 'bg-vokasi-primary text-white font-bold' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'" class="px-4 py-2 rounded-xl text-xs whitespace-nowrap shadow-sm transition-colors">
                Semua ({{ count($pendingLogbooks) + count($pendingAbsensis) }})
            </button>
            <button @click="activeTab = 'logbook'" :class="activeTab === 'logbook' ? 'bg-vokasi-primary text-white font-bold' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'" class="px-4 py-2 rounded-xl text-xs whitespace-nowrap shadow-sm transition-colors">
                Logbook Harian ({{ count($pendingLogbooks) }})
            </button>
            <button @click="activeTab = 'absensi'" :class="activeTab === 'absensi' ? 'bg-vokasi-primary text-white font-bold' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'" class="px-4 py-2 rounded-xl text-xs whitespace-nowrap shadow-sm transition-colors">
                Izin / Sakit / Flag ({{ count($pendingAbsensis) }})
            </button>
        </div>

        <div class="space-y-6">
            
            <!-- LIST ITEM 1: LOGBOOK HARIAN -->
            @forelse($pendingLogbooks as $logbook)
            <div x-show="activeTab === 'semua' || activeTab === 'logbook'" class="bg-white rounded-2xl shadow-sm border border-yellow-200 overflow-hidden">
                <div class="bg-yellow-50/60 border-b border-yellow-100 p-4 flex justify-between items-center text-xs">
                    <div class="flex items-center gap-2">
                        <span class="bg-blue-100 text-blue-700 p-1.5 rounded-lg"><i class="fas fa-book"></i></span>
                        <h3 class="font-bold text-gray-800 text-sm">Pengajuan Logbook Harian</h3>
                    </div>
                    <span class="font-semibold text-gray-500"><i class="far fa-clock mr-1"></i> Tanggal Logbook: {{ $logbook->tanggal->format('d M Y') }}</span>
                </div>
                
                <div class="p-5 flex flex-col md:flex-row gap-6">
                    <!-- Info Mahasiswa & Uraian Logbook -->
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

                            <!-- Badge Mata Kuliah -->
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
                    
                    <!-- Action Form Area -->
                    <form action="{{ route('dashboard-verifikasi-logbook-action', $logbook->id) }}" method="POST" class="w-full md:w-64 shrink-0 flex flex-col justify-end space-y-2 border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-6 text-xs">
                        @csrf
                        <div>
                            <textarea name="catatan_dosen" rows="2" placeholder="Catatan/Masukan untuk mahasiswa (opsional)..." class="w-full text-xs p-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none focus:border-vokasi-primary resize-none"></textarea>
                        </div>
                        <button type="submit" name="action" value="approve" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2.5 rounded-xl transition-colors shadow-sm flex items-center justify-center">
                            <i class="fas fa-check mr-1.5"></i> Approve (+8 Jam)
                        </button>
                        <button type="submit" name="action" value="revisi" class="w-full bg-white hover:bg-red-50 text-red-600 border border-red-200 text-xs font-bold py-2 rounded-xl transition-colors shadow-sm flex items-center justify-center">
                            <i class="fas fa-undo mr-1.5"></i> Minta Revisi
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div x-show="activeTab === 'semua' || activeTab === 'logbook'" class="p-8 text-center bg-white rounded-2xl border border-gray-200 text-gray-400">
                <i class="fas fa-check-circle text-3xl mb-2 text-emerald-500 block"></i> Tidak ada antrean pengajuan logbook harian.
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
                                    <i class="fas fa-file-medical-alt mr-2"></i> Lihat Surat Keterangan Dokter / Bukti (PDF/Foto)
                                </a>
                            @endif
                        </div>
                    </div>

                    <form action="{{ route('dashboard-verifikasi-absensi-action', $absensi->id) }}" method="POST" class="w-full md:w-64 shrink-0 flex flex-col justify-end space-y-2 border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-6 text-xs">
                        @csrf
                        <button type="submit" name="action" value="approve" class="w-full bg-vokasi-primary hover:bg-vokasi-dark text-white font-bold py-2.5 rounded-xl transition-colors shadow-sm flex items-center justify-center">
                            <i class="fas fa-check-circle mr-1.5"></i> Setujui Pengajuan
                        </button>
                        <button type="submit" name="action" value="reject" class="w-full bg-white hover:bg-gray-50 text-gray-600 border border-gray-300 font-bold py-2 rounded-xl transition-colors shadow-sm flex items-center justify-center">
                            Tolak Pengajuan
                        </button>
                    </form>
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