@extends('layouts.dashboard')

@section('content')
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative custom-scrollbar"
      x-data="{ openDetailModal: false, activeDetail: null }">
    
    <div class="max-w-7xl mx-auto w-full flex-1 space-y-6">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Riwayat Terverifikasi</h2>
                <p class="text-sm text-gray-500 mt-1">Daftar laporan logbook, absensi, dan izin mahasiswa yang telah selesai Anda tindak lanjuti.</p>
            </div>
        </div>

        <!-- TABEL RIWAYAT VERIFIKASI -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden">
            
            <!-- Table Controls & Search Form -->
            <form action="{{ route('dashboard-verifikasi-daftar-mahasiswa-terverifikasi') }}" method="GET" class="p-5 border-b border-gray-100 flex flex-col md:flex-row justify-between md:items-center gap-4 bg-gray-50/50">
                <div class="relative w-full md:w-80">
                    <i class="fas fa-search absolute left-3.5 top-3 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama mahasiswa / NIM..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20">
                </div>
                
                <div class="flex flex-wrap items-center gap-2">
                    <select name="jenis" onchange="this.form.submit()" class="bg-white border border-gray-200 text-gray-700 text-xs rounded-xl focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm font-semibold">
                        <option value="semua" {{ request('jenis') == 'semua' ? 'selected' : '' }}>Semua Jenis Laporan</option>
                        <option value="logbook" {{ request('jenis') == 'logbook' ? 'selected' : '' }}>Logbook Harian</option>
                        <option value="izin" {{ request('jenis') == 'izin' ? 'selected' : '' }}>Izin / Sakit</option>
                        <option value="presensi" {{ request('jenis') == 'presensi' ? 'selected' : '' }}>Presensi Hadir</option>
                    </select>
                </div>
            </form>

            <!-- Responsive Table -->
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100/60 border-b border-gray-200 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                            <th class="p-4 pl-6 w-12 text-center">No</th>
                            <th class="p-4 w-52">Mahasiswa</th>
                            <th class="p-4 w-40">Jenis Laporan</th>
                            <th class="p-4 w-32">Tgl Kegiatan</th>
                            <th class="p-4 min-w-[280px]">Uraian / Ringkasan</th>
                            <th class="p-4 w-36">Status Keputusan</th>
                            <th class="p-4 w-28 text-center pr-6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y divide-gray-100 text-gray-700">
                        
                        @forelse($riwayats as $index => $row)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="p-4 pl-6 text-center text-gray-500 font-medium">{{ $riwayats->firstItem() + $index }}</td>
                            <td class="p-4">
                                <p class="font-bold text-gray-800">{{ $row->user->name ?? 'Mahasiswa' }}</p>
                                <p class="text-[11px] text-gray-400 font-mono">NIM: {{ $row->user->mahasiswaProfile?->nim ?? '-' }}</p>
                            </td>
                            <td class="p-4">
                                @if($row->tipe_data === 'logbook')
                                    <span class="inline-flex items-center text-[10px] font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-100">
                                        <i class="fas fa-book mr-1.5 text-blue-500"></i> Logbook Harian
                                    </span>
                                @elseif(str_contains($row->jenis_laporan, 'Izin'))
                                    <span class="inline-flex items-center text-[10px] font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-100">
                                        <i class="fas fa-notes-medical mr-1.5 text-amber-500"></i> {{ $row->jenis_laporan }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-[10px] font-bold text-purple-700 bg-purple-50 px-2.5 py-1 rounded-lg border border-purple-100">
                                        <i class="fas fa-user-check mr-1.5 text-purple-500"></i> Presensi Hadir
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-gray-600 font-medium whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}
                            </td>
                            <td class="p-4 text-gray-700 leading-relaxed max-w-xs truncate">
                                {{ $row->uraian }}
                            </td>
                            <td class="p-4">
                                @if(in_array($row->status_verifikasi, ['approved', 'disetujui']))
                                    <span class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-full border border-emerald-200">
                                        <i class="fas fa-check-circle mr-1"></i> Disetujui / Approved
                                    </span>
                                @elseif($row->status_verifikasi === 'revisi')
                                    <span class="inline-flex items-center px-2.5 py-1 bg-amber-50 text-amber-700 text-[10px] font-bold rounded-full border border-amber-200">
                                        <i class="fas fa-undo mr-1"></i> Minta Revisi
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 bg-red-50 text-red-700 text-[10px] font-bold rounded-full border border-red-200">
                                        <i class="fas fa-times-circle mr-1"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-center pr-6">
                                <button type="button" @click="activeDetail = @js($row); openDetailModal = true" class="text-gray-600 hover:text-vokasi-primary bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg transition-colors text-xs font-bold" title="Lihat Detail">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-400">
                                <i class="fas fa-clipboard-check text-3xl mb-2 block"></i> Belum ada riwayat verifikasi laporan.
                            </td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-gray-100 bg-white">
                {{ $riwayats->links() }}
            </div>

        </div>

    </div>

    <!-- MODAL POPUP: DETAIL LAPORAN TERVERIFIKASI -->
    <div x-show="openDetailModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.away="openDetailModal = false" class="bg-white rounded-3xl shadow-2xl border border-gray-200 w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]" x-if="activeDetail">
            
            <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center shrink-0">
                <h3 class="font-bold text-sm flex items-center gap-2">
                    <i class="fas fa-info-circle text-base"></i> Detail Laporan Terverifikasi
                </h3>
                <button type="button" @click="openDetailModal = false" class="text-white/80 hover:text-white text-lg"><i class="fas fa-times"></i></button>
            </div>

            <div class="p-6 space-y-4 text-xs overflow-y-auto custom-scrollbar flex-1">
                <!-- 1. IDENTITAS MAHASISWA & INSTANSI -->
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-2xl space-y-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-bold text-gray-800 text-sm" x-text="activeDetail?.user?.name"></p>
                            <p class="text-gray-500 font-mono">NIM: <span x-text="activeDetail?.user?.mahasiswa_profile?.nim || '-'"></span></p>
                            <p class="text-vokasi-primary font-semibold text-[11px]" x-text="activeDetail?.user?.mahasiswa_profile?.prodi?.nama_prodi || '-'"></p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase" 
                              :class="activeDetail?.tipe_data === 'logbook' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700'"
                              x-text="activeDetail?.jenis_laporan">
                        </span>
                    </div>
                    
                    <div class="pt-2 border-t border-gray-200/60 flex items-center gap-2 text-gray-700">
                        <i class="fas fa-building text-vokasi-primary"></i>
                        <span class="font-bold" x-text="activeDetail?.nama_perusahaan"></span>
                    </div>
                </div>

                <!-- 2. DETAIL KHUSUS PRESENSI HADIR (WAKTU & KOORDINAT GPS) -->
                <template x-if="activeDetail?.tipe_data === 'absensi' && activeDetail?.jenis_laporan === 'Presensi Hadir'">
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <!-- Absen Masuk -->
                            <div class="p-3.5 bg-teal-50/60 border border-teal-200 rounded-xl space-y-1">
                                <span class="text-[10px] uppercase font-bold text-teal-800 block"><i class="fas fa-sign-in-alt mr-1"></i> Absen Masuk</span>
                                <p class="font-mono font-bold text-gray-800 text-xs" x-text="activeDetail?.waktu_masuk ? activeDetail?.waktu_masuk + ' WITA' : '-'"></p>
                                <div class="text-[10px] text-gray-500 font-mono pt-1">
                                    <i class="fas fa-map-marker-alt text-teal-600 mr-1"></i>
                                    <span x-text="(activeDetail?.latitude_masuk || '-') + ', ' + (activeDetail?.longitude_masuk || '-')"></span>
                                </div>
                            </div>

                            <!-- Absen Pulang -->
                            <div class="p-3.5 bg-orange-50/60 border border-orange-200 rounded-xl space-y-1">
                                <span class="text-[10px] uppercase font-bold text-orange-800 block"><i class="fas fa-sign-out-alt mr-1"></i> Absen Pulang</span>
                                <p class="font-mono font-bold text-gray-800 text-xs" x-text="activeDetail?.waktu_pulang ? activeDetail?.waktu_pulang + ' WITA' : 'Belum Absen Pulang'"></p>
                                <div class="text-[10px] text-gray-500 font-mono pt-1">
                                    <i class="fas fa-map-marker-alt text-orange-600 mr-1"></i>
                                    <span x-text="(activeDetail?.latitude_pulang || '-') + ', ' + (activeDetail?.longitude_pulang || '-')"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Link Google Maps Lokasi Masuk -->
                        <template x-if="activeDetail?.latitude_masuk && activeDetail?.longitude_masuk">
                            <div class="text-right">
                                <a :href="'https://www.google.com/maps?q=' + activeDetail?.latitude_masuk + ',' + activeDetail?.longitude_masuk" 
                                   target="_blank" 
                                   class="text-[11px] font-bold text-vokasi-primary hover:underline inline-flex items-center gap-1">
                                    <i class="fas fa-external-link-alt"></i> Buka Lokasi Masuk di Google Maps
                                </a>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- 3. RINCIAN LAPORAN / ALASAN -->
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Rincian Laporan / Uraian:</label>
                    <div class="p-3.5 bg-gray-50 border border-gray-200 rounded-xl leading-relaxed text-gray-800 whitespace-pre-line" x-text="activeDetail?.uraian"></div>
                </div>

                <!-- 4. CATATAN DOSEN -->
                <template x-if="activeDetail?.catatan">
                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Catatan Verifikator:</label>
                        <div class="p-3 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl leading-relaxed font-semibold" x-text="activeDetail?.catatan"></div>
                    </div>
                </template>

                <!-- 5. FOTO DOKUMENTASI / SELFIE / SURAT -->
                <template x-if="activeDetail?.foto || activeDetail?.foto_pulang">
                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Lampiran Foto Dokumentasi / Bukti:</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <template x-if="activeDetail?.foto">
                                <div class="space-y-1">
                                    <span class="text-[10px] text-gray-500 font-bold block" x-text="activeDetail?.tipe_data === 'absensi' ? 'Foto Masuk / Surat' : 'Foto Kegiatan'"></span>
                                    <div class="h-36 rounded-xl overflow-hidden border border-gray-200 bg-black/90 flex items-center justify-center">
                                        <img :src="'{{ asset('storage') }}/' + activeDetail?.foto" class="max-h-full max-w-full object-contain">
                                    </div>
                                </div>
                            </template>

                            <template x-if="activeDetail?.foto_pulang">
                                <div class="space-y-1">
                                    <span class="text-[10px] text-gray-500 font-bold block">Foto Pulang</span>
                                    <div class="h-36 rounded-xl overflow-hidden border border-gray-200 bg-black/90 flex items-center justify-center">
                                        <img :src="'{{ asset('storage') }}/' + activeDetail?.foto_pulang" class="max-h-full max-w-full object-contain">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <div class="flex justify-end pt-2 border-t border-gray-100 shrink-0">
                    <button type="button" @click="openDetailModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold text-xs transition-colors">
                        Tutup
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- FOOTER -->
    <footer class="mt-auto py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
        Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
    </footer>

</main>
@endsection