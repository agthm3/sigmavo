@extends('layouts.dashboard')

@section('content')
    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50" x-data="{ openAddModal: false, openMateriModal: false }">
        
        <!-- CONTENT -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative custom-scrollbar">
            
            <div class="max-w-6xl mx-auto w-full flex-1 space-y-6">
                
                <!-- HEADER & TOMBOL AKSI -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Pembekalan Magang</h2>
                        <p class="text-sm text-gray-500 mt-1">Informasi agenda pembekalan, materi pedoman, dan presensi kehadiran.</p>
                    </div>

                    @if(isset($user) && $user->hasAnyRole(['admin', 'superadmin', 'admin_prodi']))
                    <div class="flex gap-2">
                        @if($pembekalan)
                        <button type="button" @click="openMateriModal = true" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold text-xs py-2.5 px-4 rounded-xl shadow-sm transition-colors flex items-center gap-2">
                            <i class="fas fa-file-upload text-vokasi-primary"></i> Tambah Materi
                        </button>
                        @endif
                        <button type="button" @click="openAddModal = true" class="bg-vokasi-primary hover:bg-vokasi-dark text-white font-bold text-xs py-2.5 px-4 rounded-xl shadow-sm transition-colors flex items-center gap-2">
                            <i class="fas fa-plus-circle"></i> Buat Agenda Baru
                        </button>
                    </div>
                    @endif
                </div>

                <!-- NOTIFIKASI SUKSES / ERROR -->
                @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900"><i class="fas fa-times"></i></button>
                </div>
                @endif

                @if($pembekalan)
                <!-- BANNER UTAMA AGENDA -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- LEFT COLUMN (Jadwal & Status) -->
                    <div class="lg:col-span-2 space-y-6">
                        
                        <!-- Status Kehadiran Banner (Khusus Mahasiswa / Presensi Diri Sendiri) -->
                        @if($presensi && $presensi->is_hadir)
                            <div class="bg-gradient-to-r from-emerald-600 to-vokasi-primary rounded-xl shadow-sm p-6 text-white flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center shrink-0">
                                        <i class="fas fa-check-circle text-2xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-lg">Status: Telah Mengikuti Pembekalan</h3>
                                        <p class="text-xs text-emerald-100 opacity-90 mt-0.5">Dikonfirmasi pada {{ \Carbon\Carbon::parse($presensi->waktu_presensi)->format('d M Y, H:i') }} WITA</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-amber-500 rounded-xl shadow-sm p-6 text-white flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center shrink-0">
                                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-lg">Status: Belum Konfirmasi Kehadiran</h3>
                                        <p class="text-xs text-amber-100 opacity-90 mt-0.5">Silakan isi konfirmasi kehadiran Anda untuk memenuhi syarat magang.</p>
                                    </div>
                                </div>
                                <form action="{{ route('dashboard-mahasiswa-pembekalan-magang-presensi', $pembekalan->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-white text-amber-800 font-bold text-xs py-2.5 px-4 rounded-xl shadow-sm hover:bg-amber-50 transition-colors w-full sm:w-auto text-center shrink-0">
                                        <i class="fas fa-user-check mr-1.5"></i> Konfirmasi Kehadiran
                                    </button>
                                </form>
                            </div>
                        @endif

                        <!-- Detail Agenda Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="border-b border-gray-100 p-5 bg-gray-50/50 flex justify-between items-center">
                                <h3 class="font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-calendar-alt text-vokasi-primary mr-2"></i> {{ $pembekalan->judul }}
                                </h3>
                                <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-full border
                                    {{ $pembekalan->status == 'selesai' ? 'bg-gray-100 text-gray-600 border-gray-200' : 'bg-green-100 text-green-700 border-green-200' }}">
                                    {{ $pembekalan->status }}
                                </span>
                            </div>
                            <div class="p-6">
                                <div class="flex flex-col md:flex-row gap-6">
                                    <div class="flex-1 space-y-4">
                                        <div class="flex items-start">
                                            <div class="w-8 flex justify-center text-gray-400 mt-0.5"><i class="fas fa-clock"></i></div>
                                            <div>
                                                <p class="text-xs font-semibold text-gray-500 uppercase">Waktu Pelaksanaan</p>
                                                <p class="font-medium text-gray-800">{{ $pembekalan->waktu_mulai->format('d F Y') }}</p>
                                                <p class="text-xs text-gray-600">{{ $pembekalan->waktu_mulai->format('H:i') }} - {{ $pembekalan->waktu_selesai->format('H:i') }} WITA</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="w-8 flex justify-center text-gray-400 mt-0.5"><i class="fas fa-map-marker-alt"></i></div>
                                            <div>
                                                <p class="text-xs font-semibold text-gray-500 uppercase">Lokasi / Platform</p>
                                                <p class="font-medium text-gray-800">{{ $pembekalan->lokasi }}</p>
                                                @if($pembekalan->link_zoom)
                                                    <a href="{{ $pembekalan->link_zoom }}" target="_blank" class="text-xs text-vokasi-primary hover:underline mt-1 inline-block font-semibold">
                                                        Buka Tautan Zoom <i class="fas fa-external-link-alt text-[10px] ml-1"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex-1 space-y-4 md:border-l md:border-gray-100 md:pl-6">
                                        <div class="flex items-start">
                                            <div class="w-8 flex justify-center text-gray-400 mt-0.5"><i class="fas fa-user-tie"></i></div>
                                            <div>
                                                <p class="text-xs font-semibold text-gray-500 uppercase">Pemateri</p>
                                                <p class="font-medium text-gray-800">{{ $pembekalan->pemateri }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="w-8 flex justify-center text-gray-400 mt-0.5"><i class="fas fa-clipboard-list"></i></div>
                                            <div>
                                                <p class="text-xs font-semibold text-gray-500 uppercase">Topik Utama</p>
                                                <ul class="list-disc list-inside text-xs text-gray-600 mt-1 space-y-1">
                                                    @foreach(explode(',', $pembekalan->topik_utama) as $topik)
                                                        <li>{{ trim($topik) }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN (Materi Unduhan) -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="border-b border-gray-100 p-5 bg-gray-50/50">
                                <h3 class="font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-folder-open text-vokasi-primary mr-2"></i> Materi Pembekalan
                                </h3>
                                <p class="text-xs text-gray-500 mt-1">Unduh pedoman dan dokumen pendukung magang.</p>
                            </div>
                            
                            <div class="divide-y divide-gray-100">
                                @forelse($pembekalan->materis as $materi)
                                <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                                            <i class="{{ $materi->tipe_file === 'DOCX' ? 'fas fa-file-word text-blue-500' : 'fas fa-file-pdf text-red-500' }} text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-xs text-gray-800 group-hover:text-vokasi-primary transition-colors">{{ $materi->judul_materi }}</p>
                                            <p class="text-[10px] text-gray-400">{{ $materi->tipe_file }} • {{ $materi->ukuran_file }}</p>
                                        </div>
                                    </div>
                                    <a href="#" class="text-gray-400 hover:text-vokasi-primary transition-colors p-2" title="Unduh File">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                                @empty
                                <div class="p-6 text-center text-gray-400 text-xs">
                                    Belum ada berkas materi diunggah.
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>

                <!-- PANEL KHUSUS ADMIN / ADMIN PRODI: REKAP & MANUAL OVERRIDE KEHADIRAN MAHASISWA -->
                @if(isset($user) && $user->hasAnyRole(['admin', 'superadmin', 'admin_prodi']))
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mt-8">
                    
                    <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                                <i class="fas fa-users-check text-vokasi-primary"></i> Rekapitulasi Presensi Kehadiran Mahasiswa
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">Kelola dan tandai kehadiran mahasiswa secara manual jika terjadi kendala.</p>
                        </div>

                        <!-- Ringkasan Statistik -->
                        <div class="flex items-center gap-3 text-xs font-bold">
                            <span class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg border border-blue-100">
                                Total: {{ $totalMhs }}
                            </span>
                            <span class="px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg border border-emerald-100">
                                Hadir: {{ $totalHadir }}
                            </span>
                            <span class="px-3 py-1.5 bg-amber-50 text-amber-700 rounded-lg border border-amber-100">
                                Belum: {{ $totalBelum }}
                            </span>
                        </div>
                    </div>

                    <!-- Search Toolbar -->
                    <div class="p-4 border-b border-gray-100 bg-white">
                        <form action="{{ route('dashboard-mahasiswa-pembekalan-magang') }}" method="GET" class="flex gap-2">
                            <div class="relative flex-1 max-w-md">
                                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama mahasiswa atau NIM..." class="w-full pl-8 pr-4 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:outline-none">
                            </div>
                            <button type="submit" class="px-4 py-1.5 bg-vokasi-primary text-white text-xs font-bold rounded-lg hover:bg-vokasi-dark transition-colors">
                                Cari
                            </button>
                        </form>
                    </div>

                    <!-- Tabel Presensi Mahasiswa -->
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-gray-100/60 border-b border-gray-200 font-semibold text-gray-600 uppercase tracking-wider">
                                    <th class="p-3 w-12 text-center">No</th>
                                    <th class="p-3">Nama Mahasiswa & NIM</th>
                                    <th class="p-3">Program Studi</th>
                                    <th class="p-3 w-40 text-center">Waktu Presensi</th>
                                    <th class="p-3 w-32 text-center">Status</th>
                                    <th class="p-3 w-36 text-center">Aksi Override</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($mahasiswas as $idx => $mhs)
                                @php $isHadir = $mhs->presensi_pembekalan?->is_hadir; @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-3 text-center text-gray-500 font-medium">{{ $idx + 1 }}</td>
                                    <td class="p-3 font-bold text-gray-800">
                                        {{ $mhs->name }}
                                        <span class="block text-[10px] text-gray-400 font-normal">NIM: {{ $mhs->mahasiswaProfile->nim ?? '-' }}</span>
                                    </td>
                                    <td class="p-3 text-gray-600">
                                        {{ $mhs->mahasiswaProfile->prodi->nama_prodi ?? '-' }}
                                    </td>
                                    <td class="p-3 text-center text-gray-500">
                                        {{ $mhs->presensi_pembekalan ? \Carbon\Carbon::parse($mhs->presensi_pembekalan->waktu_presensi)->format('d/m/Y H:i') : '-' }}
                                    </td>
                                    <td class="p-3 text-center">
                                        @if($isHadir)
                                            <span class="inline-flex items-center px-2 py-0.5 bg-emerald-100 text-emerald-700 font-bold rounded-full text-[10px]">
                                                <i class="fas fa-check-circle mr-1"></i> Hadir
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 bg-amber-100 text-amber-700 font-bold rounded-full text-[10px]">
                                                <i class="fas fa-clock mr-1"></i> Belum
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center">
                                        <form action="{{ route('dashboard-mahasiswa-pembekalan-magang-manual', ['pembekalanId' => $pembekalan->id, 'userId' => $mhs->id]) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            @if($isHadir)
                                                <button type="submit" class="px-2.5 py-1 bg-red-50 text-red-600 hover:bg-red-100 font-bold rounded border border-red-200 transition-colors" title="Batalkan Kehadiran">
                                                    Batal Hadir
                                                </button>
                                            @else
                                                <button type="submit" class="px-2.5 py-1 bg-vokasi-primary text-white hover:bg-vokasi-dark font-bold rounded shadow-sm transition-colors" title="Tandai Sebagai Hadir">
                                                    <i class="fas fa-user-check mr-1"></i> Tandai Hadir
                                                </button>
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-gray-400">Tidak ada data mahasiswa ditemukan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
                @endif

                @else
                <div class="bg-white p-12 text-center rounded-2xl border border-gray-200 text-gray-400">
                    <i class="fas fa-calendar-times text-4xl mb-3 block"></i>
                    <p class="font-bold text-gray-600 text-base">Belum Ada Agenda Pembekalan</p>
                    <p class="text-xs mt-1">Jadwal kegiatan pembekalan magang akan diinformasikan oleh panitia fakultas.</p>
                </div>
                @endif

            </div>

            <!-- FOOTER -->
            <footer class="mt-auto py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
                Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
            </footer>

        </main>

        <!-- MODAL POPUP 1: BUAT AGENDA PEMBEKALAN BARU (ADMIN) -->
        <div x-show="openAddModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div @click.away="openAddModal = false" class="bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-2xl overflow-hidden">
                <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center">
                    <h3 class="font-bold text-lg"><i class="fas fa-calendar-plus mr-2"></i> Buat Agenda Pembekalan Magang</h3>
                    <button type="button" @click="openAddModal = false" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
                </div>

                <form action="{{ route('dashboard-mahasiswa-pembekalan-magang-store') }}" method="POST" class="p-6 space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Judul Kegiatan <span class="text-red-500">*</span></label>
                        <input type="text" name="judul" required placeholder="Contoh: Pembekalan Magang Vokasi 2026" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Waktu Mulai <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="waktu_mulai" required class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Waktu Selesai <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="waktu_selesai" required class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Pemateri <span class="text-red-500">*</span></label>
                            <input type="text" name="pemateri" required placeholder="Nama Pemateri" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Lokasi / Platform <span class="text-red-500">*</span></label>
                            <input type="text" name="lokasi" required placeholder="Aula Fakultas / Online Zoom" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Link Zoom / Virtual Meeting (Opsional)</label>
                        <input type="url" name="link_zoom" placeholder="https://zoom.us/j/..." class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Topik Utama (Pisahkan dengan Koma)</label>
                        <textarea name="topik_utama" rows="2" placeholder="Etika Profesi, Format Logbook, K3 Industri" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl resize-none focus:bg-white focus:outline-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                        <button type="button" @click="openAddModal = false" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-medium">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-xl font-bold shadow-sm">Terbitkan Agenda</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL POPUP 2: UNGGAH MATERI PEMBEKALAN BARU -->
        @if($pembekalan)
        <div x-show="openMateriModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div @click.away="openMateriModal = false" class="bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-lg overflow-hidden">
                <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center">
                    <h3 class="font-bold text-lg"><i class="fas fa-file-upload mr-2"></i> Tambah Materi Pembekalan</h3>
                    <button type="button" @click="openMateriModal = false" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
                </div>

                <form action="{{ route('dashboard-mahasiswa-pembekalan-magang-materi-store', $pembekalan->id) }}" method="POST" class="p-6 space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Judul / Nama Dokumen <span class="text-red-500">*</span></label>
                        <input type="text" name="judul_materi" required placeholder="Contoh: Buku Saku Magang 2026" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Tipe File <span class="text-red-500">*</span></label>
                            <select name="tipe_file" required class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none">
                                <option value="PDF">PDF Document</option>
                                <option value="DOCX">Word (.docx)</option>
                                <option value="PPTX">PowerPoint (.pptx)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Ukuran File</label>
                            <input type="text" name="ukuran_file" placeholder="1.5 MB" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                        <button type="button" @click="openMateriModal = false" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-medium">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-xl font-bold shadow-sm">Simpan Materi</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

    </div>
@endsection