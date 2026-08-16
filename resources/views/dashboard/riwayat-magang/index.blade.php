@extends('layouts.dashboard')

@section('content')
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col custom-scrollbar" 
          x-data="{ openDetailModal: false, activeItem: null }">
        
        <div class="flex-1 max-w-7xl mx-auto w-full">
            
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Riwayat Program Magang</h3>
                    <p class="text-sm text-gray-500 mt-1">Pantau status pengajuan dan histori seluruh stase/magang yang pernah Anda ikuti.</p>
                </div>
                
                <!-- Filter Status -->
                <form action="{{ route('dashboard-mahasiswa-riwayat-magang') }}" method="GET" class="flex items-center space-x-2">
                    <select name="status" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-xs rounded-xl focus:ring-vokasi-primary focus:border-vokasi-primary block p-2.5 outline-none shadow-sm font-semibold">
                        <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}>Semua Status</option>
                        <option value="berjalan" {{ request('status') == 'berjalan' ? 'selected' : '' }}>Sedang Berjalan (Aktif)</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai Magang / Stase</option>
                        <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu Diterima</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </form>
            </div>

            <!-- CARD GRID -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                @forelse($riwayats as $item)
                    @php
                        $isMandiri = $item->jalur_magang === 'mandiri';
                        $namaPerusahaan = $isMandiri ? $item->nama_instansi_mandiri : ($item->lowongan->perusahaan->nama_perusahaan ?? '-');
                        $judulPosisi = $isMandiri ? ($item->divisi_mandiri ?? 'Pengajuan Mandiri') : ($item->lowongan->judul_posisi ?? '-');
                        $alamat = $isMandiri ? '-' : ($item->lowongan->perusahaan->alamat ?? '-');
                        
                        // Menentukan status magang akurat
                        $isSelesai = ($item->status_seleksi === 'selesai') || ($item->status_seleksi === 'diterima' && $item->tgl_selesai_magang && \Carbon\Carbon::parse($item->tgl_selesai_magang)->isPast());
                        $isBerjalan = ($item->status_seleksi === 'diterima') && (!$item->tgl_selesai_magang || \Carbon\Carbon::parse($item->tgl_selesai_magang)->isFuture() || \Carbon\Carbon::parse($item->tgl_selesai_magang)->isToday());
                        $isMenunggu = in_array($item->status_seleksi, ['menunggu', 'pending']);
                    @endphp

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md hover:border-vokasi-primary transition-all flex flex-col">
                        <div class="p-5 flex-1 border-b border-gray-100">
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-12 h-12 rounded-lg bg-teal-50 border border-teal-100 flex items-center justify-center text-vokasi-primary shrink-0 font-bold text-lg">
                                    {{ Str::upper(Str::substr($namaPerusahaan, 0, 2)) }}
                                </div>

                                @if($isBerjalan)
                                    <span class="bg-emerald-100 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full border border-emerald-200 flex items-center gap-1.5 shadow-sm">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Sedang Berjalan
                                    </span>
                                @elseif($isSelesai)
                                    <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full border border-blue-200 flex items-center gap-1 shadow-sm">
                                        <i class="fas fa-flag-checkered text-[10px]"></i> Selesai
                                    </span>
                                @elseif($isMenunggu)
                                    <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full border border-yellow-200">
                                        Menunggu Diterima
                                    </span>
                                @else
                                    <span class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full border border-red-200">
                                        Ditolak
                                    </span>
                                @endif
                            </div>

                            <h4 class="font-bold text-lg text-gray-800 mb-1 leading-tight line-clamp-1">{{ $judulPosisi }}</h4>
                            <p class="text-vokasi-primary font-semibold text-sm mb-3 line-clamp-1">{{ $namaPerusahaan }}</p>
                            
                            <div class="space-y-2 mt-4 text-xs text-gray-600">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt w-5 text-gray-400"></i>
                                    <span>
                                        @if($item->tgl_mulai_magang && $item->tgl_selesai_magang)
                                            {{ \Carbon\Carbon::parse($item->tgl_mulai_magang)->format('d M Y') }} - {{ \Carbon\Carbon::parse($item->tgl_selesai_magang)->format('d M Y') }}
                                        @else
                                            Tgl Belum Ditetapkan
                                        @endif
                                    </span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt w-5 text-gray-400"></i>
                                    <span class="line-clamp-1">{{ Str::limit($alamat, 30) }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-user-tie w-5 text-gray-400"></i>
                                    <span>Dosen: <strong>{{ $item->dosen->name ?? 'Belum Ditugaskan' }}</strong></span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="p-4 bg-gray-50 flex items-center justify-between gap-2 mt-auto">
                            @if($isBerjalan)
                                <a href="{{ route('dashboard-mahasiswa-logbook') }}" class="text-vokasi-primary hover:text-vokasi-dark text-xs font-bold transition-colors flex items-center">
                                    Lihat Logbook <i class="fas fa-arrow-right ml-1 text-[10px]"></i>
                                </a>
                            @elseif($isSelesai)
                                <span class="text-[11px] font-bold text-blue-600 flex items-center gap-1">
                                    <i class="fas fa-check-double"></i> Stase Selesai
                                </span>
                            @else
                                <span></span>
                            @endif

                            <button @click="activeItem = {{ json_encode($item) }}; openDetailModal = true" 
                                    class="bg-white border border-gray-200 hover:bg-gray-100 text-gray-700 text-xs font-bold py-1.5 px-3 rounded-lg transition-colors ml-auto shadow-sm">
                                <i class="fas fa-file-alt mr-1"></i> Detail
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white p-12 text-center rounded-2xl border border-gray-200 text-gray-400">
                        <i class="fas fa-history text-4xl mb-3 block"></i>
                        <p class="font-bold text-gray-600 text-base">Belum Ada Riwayat Magang</p>
                        <p class="text-xs mt-1">Riwayat magang yang pernah Anda daftarkan akan muncul di halaman ini.</p>
                    </div>
                @endforelse

            </div>
        </div>

        <!-- FOOTER -->
        <footer class="mt-8 py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
            Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
        </footer>

        <!-- ========================================== -->
        <!-- MODAL POPUP: DETAIL RIWAYAT MAGANG -->
        <!-- ========================================== -->
        <div x-show="openDetailModal" 
             x-cloak 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            
            <div @click.away="openDetailModal = false" class="bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-xl overflow-hidden" x-if="activeItem">
                
                <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-file-alt text-lg"></i>
                        <h3 class="font-bold text-lg">Detail Riwayat Magang</h3>
                    </div>
                    <button @click="openDetailModal = false" class="text-white/80 hover:text-white p-1 rounded-lg">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div class="p-6 space-y-4 text-xs text-gray-700">
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-2">
                        <p class="text-sm font-bold text-gray-800" x-text="activeItem?.jalur_magang === 'mandiri' ? activeItem?.nama_instansi_mandiri : activeItem?.lowongan?.perusahaan?.nama_perusahaan"></p>
                        <p class="text-vokasi-primary font-semibold" x-text="activeItem?.jalur_magang === 'mandiri' ? (activeItem?.divisi_mandiri || 'Pengajuan Mandiri') : activeItem?.lowongan?.judul_posisi"></p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <span class="text-gray-400 block uppercase font-bold text-[10px]">Jalur Magang</span>
                            <span class="font-bold uppercase text-gray-800" x-text="activeItem?.jalur_magang || 'REGULER'"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block uppercase font-bold text-[10px]">Status Seleksi</span>
                            <span class="font-bold uppercase" 
                                  :class="activeItem?.status_seleksi === 'selesai' ? 'text-blue-600' : (activeItem?.status_seleksi === 'diterima' ? 'text-emerald-600' : 'text-gray-800')" 
                                  x-text="activeItem?.status_seleksi === 'selesai' ? 'SELESAI MAGANG / STASE' : activeItem?.status_seleksi"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block uppercase font-bold text-[10px]">Nomor Surat Pengantar</span>
                            <span class="font-mono text-gray-800" x-text="activeItem?.nomor_surat || 'Belum Diterbitkan'"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block uppercase font-bold text-[10px]">Dosen Pendamping</span>
                            <span class="font-bold text-gray-800" x-text="activeItem?.dosen?.name || 'Belum Ditetapkan'"></span>
                        </div>
                    </div>

                    <template x-if="activeItem?.catatan_seleksi">
                        <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-blue-900">
                            <strong>Catatan Pengelola / Admin:</strong>
                            <p class="mt-1" x-text="activeItem?.catatan_seleksi"></p>
                        </div>
                    </template>
                </div>

                <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                    <button type="button" @click="openDetailModal = false" class="px-5 py-2 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-bold transition-colors">
                        Tutup
                    </button>
                </div>

            </div>
        </div>

    </main>
@endsection