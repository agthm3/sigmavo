@extends('layouts.dashboard')

@section('content')
    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50"
         x-data="{ 
            openModal: false, 
            openExportModal: false, 
            activeMahasiswa: '', 
            activeUrl: '',
            formScores: {} 
         }">
         
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative custom-scrollbar">
            <div class="max-w-7xl mx-auto w-full flex-1 space-y-6">
                
                <!-- Page Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Rekapitulasi Penilaian Akhir Magang</h2>
                        <p class="text-sm text-gray-500 mt-1">Nilai akhir magang ditentukan 100% berdasarkan evaluasi rubrik oleh Dosen Pembimbing.</p>
                    </div>
                    <div class="flex gap-2">
                        <button @click="openExportModal = true" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2 px-4 rounded-xl transition-colors shadow-sm flex items-center">
                            <i class="fas fa-file-pdf mr-2"></i> Export Berkas Lengkap (PDF)
                        </button>
                    </div>
                </div>

                @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex justify-between text-sm shadow-sm">
                    <div class="flex items-center gap-2"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900"><i class="fas fa-times"></i></button>
                </div>
                @endif
                
                @if(session('error'))
                <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl flex justify-between text-sm shadow-sm">
                    <div class="flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
                    <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900"><i class="fas fa-times"></i></button>
                </div>
                @endif

                <!-- SUMMARY CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center mr-4 shrink-0">
                            <i class="fas fa-user-graduate text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Total Mahasiswa</p>
                            <p class="text-xl font-bold text-gray-800 leading-none mt-1">{{ $pendaftarans->total() }}</p>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center mr-4 shrink-0">
                            <i class="fas fa-check-circle text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Input Selesai</p>
                            <p class="text-xl font-bold text-green-600 leading-none mt-1">
                                {{ $pendaftarans->filter(fn($p) => $p->penilaians->where('tipe_penilai', 'dosen')->isNotEmpty())->count() }}
                            </p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center mr-4 shrink-0">
                            <i class="fas fa-clock text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Belum Dinilai</p>
                            <p class="text-xl font-bold text-yellow-600 leading-none mt-1">
                                {{ $pendaftarans->filter(fn($p) => $p->penilaians->where('tipe_penilai', 'dosen')->isEmpty())->count() }}
                            </p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mr-4 shrink-0">
                            <i class="fas fa-chart-line text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Pusat Penilaian</p>
                            <p class="text-[11px] font-bold text-purple-600 mt-1">Oleh Dosen</p>
                        </div>
                    </div>
                </div>

                <!-- TABLE SECTION WITH FILTER TOOLBAR -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    
                    <!-- FORM FILTER MULTI-SELEKSI UNTUK TABEL AKSI LOKAL -->
                    <form action="{{ route('dashboard-penilaian-listing-mahasiswa') }}" method="GET" class="p-5 border-b border-gray-100 flex flex-col lg:flex-row justify-between lg:items-center gap-4 bg-gray-50/50">
                        <div class="relative w-full lg:w-80">
                            <i class="fas fa-search absolute left-3.5 top-3 text-gray-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIM, instansi..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-primary">
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Filter Prodi -->
                            <select name="prodi_id" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-xs rounded-xl focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option value="semua">Semua Program Studi</option>
                                @foreach($prodis as $prodi)
                                    <option value="{{ $prodi->id }}" {{ request('prodi_id') == $prodi->id ? 'selected' : '' }}>
                                        {{ $prodi->nama_prodi }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Filter Status Penilaian -->
                            <select name="status_nilai" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-xs rounded-xl focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option value="semua">Semua Status Penilaian</option>
                                <option value="lengkap" {{ request('status_nilai') == 'lengkap' ? 'selected' : '' }}>Sudah Dinilai (Lengkap)</option>
                                <option value="belum" {{ request('status_nilai') == 'belum' ? 'selected' : '' }}>Belum Dinilai</option>
                            </select>

                            <!-- Filter Jam Magang -->
                            <select name="status_jam" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-xs rounded-xl focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option value="semua">Semua Target Jam Magang</option>
                                <option value="selesai" {{ request('status_jam') == 'selesai' ? 'selected' : '' }}>Memenuhi Target Jam</option>
                                <option value="belum" {{ request('status_jam') == 'belum' ? 'selected' : '' }}>Belum Memenuhi Target</option>
                            </select>

                            @if(request()->hasAny(['search', 'prodi_id', 'status_nilai', 'status_jam']))
                                <a href="{{ route('dashboard-penilaian-listing-mahasiswa') }}" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold text-xs rounded-xl transition-colors" title="Reset Filter">
                                    <i class="fas fa-undo"></i>
                                </a>
                            @endif
                        </div>
                    </form>

                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[1000px]">
                            <thead>
                                <tr class="bg-gray-100/60 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <th class="p-4 w-12 text-center">No</th>
                                    <th class="p-4 w-60">Mahasiswa & Prodi</th>
                                    <th class="p-4 w-48">Instansi Penempatan</th>
                                    <th class="p-4 w-32 text-center">Nilai Akhir<br><span class="text-[10px] text-gray-400 font-normal">(Skala 100)</span></th>
                                    <th class="p-4 w-32 text-center">Huruf Mutu</th>
                                    <th class="p-4 w-32 text-center">Status</th>
                                    <th class="p-4 w-32 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                @forelse($pendaftarans as $index => $item)
                                    @php
                                        // Ambil Nilai dari Dosen
                                        $penilaianDosen = $item->penilaians->where('tipe_penilai', 'dosen')->first();
                                        $nilaiAkhir = $penilaianDosen ? $penilaianDosen->nilai_akhir : null;
                                        $isDinilai = $nilaiAkhir !== null;

                                        // Konversi Huruf Mutu
                                        $hurufMutu = '-';
                                        if($isDinilai) {
                                            if($nilaiAkhir >= 85) $hurufMutu = 'A';
                                            elseif($nilaiAkhir >= 80) $hurufMutu = 'A-';
                                            elseif($nilaiAkhir >= 75) $hurufMutu = 'B+';
                                            elseif($nilaiAkhir >= 70) $hurufMutu = 'B';
                                            elseif($nilaiAkhir >= 65) $hurufMutu = 'B-';
                                            elseif($nilaiAkhir >= 60) $hurufMutu = 'C+';
                                            elseif($nilaiAkhir >= 50) $hurufMutu = 'C';
                                            elseif($nilaiAkhir >= 40) $hurufMutu = 'D';
                                            else $hurufMutu = 'E';
                                        }

                                        // Persiapkan Data Form Rubrik
                                        $existingScores = $penilaianDosen ? $penilaianDosen->details->pluck('nilai_mentah', 'rubrik_id')->toArray() : [];
                                    @endphp
                                <tr class="hover:bg-gray-50 transition-colors {{ !$isDinilai ? 'bg-yellow-50/10' : '' }}">
                                    <td class="p-4 text-center text-gray-500 font-medium">{{ $pendaftarans->firstItem() + $index }}</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-vokasi-primary/10 text-vokasi-primary flex items-center justify-center font-bold text-xs shrink-0">
                                                {{ substr($item->user->name, 0, 2) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800 text-xs">{{ $item->user->name }}</p>
                                                <p class="text-[11px] text-gray-500">{{ $item->user->mahasiswaProfile->nim ?? '-' }}</p>
                                                <p class="text-[10px] font-semibold text-vokasi-primary mt-0.5">{{ $item->user->mahasiswaProfile->prodi->nama_prodi ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-semibold text-gray-800 text-xs">{{ $item->lowongan->perusahaan->nama_perusahaan ?? $item->nama_instansi_mandiri ?? '-' }}</p>
                                        <p class="text-[10px] text-gray-500 mt-0.5">{{ $item->lowongan->judul_posisi ?? 'Magang Mandiri' }}</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($isDinilai)
                                        <span class="font-extrabold text-gray-800 text-base">{{ number_format($nilaiAkhir, 2) }}</span>
                                        @else
                                        <span class="text-xs text-red-400 font-medium italic">Belum Dinilai</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($isDinilai)
                                            @php
                                                $color = 'bg-gray-100 text-gray-700';
                                                if(in_array($hurufMutu, ['A', 'A-'])) $color = 'bg-green-100 text-green-700 border-green-200';
                                                elseif(in_array($hurufMutu, ['B+', 'B', 'B-'])) $color = 'bg-blue-100 text-blue-700 border-blue-200';
                                                elseif(in_array($hurufMutu, ['C+', 'C'])) $color = 'bg-yellow-100 text-yellow-700 border-yellow-200';
                                                else $color = 'bg-red-100 text-red-700 border-red-200';
                                            @endphp
                                            <span class="inline-block px-3 py-1 font-bold text-sm rounded-lg border {{ $color }}">
                                                {{ $hurufMutu }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 font-mono">--</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($isDinilai)
                                            <span class="inline-flex items-center px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full border border-emerald-200">
                                                <i class="fas fa-check-circle mr-1"></i> Selesai
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 bg-yellow-100 text-yellow-700 text-[10px] font-bold rounded-full border border-yellow-200">
                                                <i class="fas fa-exclamation-triangle mr-1"></i> Menunggu
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($currentUser->hasAnyRole(['dosen', 'admin', 'superadmin', 'admin_prodi']))
                                        <button type="button" 
                                                data-id="{{ $item->id }}"
                                                data-nama="{{ addslashes($item->user->name) }}"
                                                data-scores="{{ json_encode($existingScores) }}"
                                                @click="
                                                    activeUrl = '{{ url('/penilaian/listing-mahasiswa') }}/' + $event.currentTarget.dataset.id + '/store';
                                                    activeMahasiswa = $event.currentTarget.dataset.nama;
                                                    formScores = JSON.parse($event.currentTarget.dataset.scores || '{}');
                                                    openModal = true;
                                                "
                                                class="bg-vokasi-primary hover:bg-vokasi-dark text-white text-xs font-bold py-1.5 px-3 rounded-xl transition-colors shadow-sm">
                                            <i class="fas fa-edit mr-1"></i> Input / Edit
                                        </button>
                                        @else
                                        <span class="text-[10px] text-gray-400 italic">Hak Akses Dosen</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-gray-400">
                                        <i class="fas fa-users-slash text-3xl mb-2 block"></i> Belum ada mahasiswa magang aktif yang sesuai kriteria filter.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="p-4 border-t border-gray-100 bg-white">
                        {{ $pendaftarans->links() }}
                    </div>
                </div>
            </div>

            <footer class="mt-8 py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
                Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
            </footer>

            <!-- MODAL INPUT RUBRIK PENILAIAN -->
            <div x-show="openModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div @click.away="openModal = false" class="bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh]">
                    <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center shrink-0">
                        <h3 class="font-bold text-base"><i class="fas fa-star-half-alt mr-2"></i> Input Nilai Rubrik Akademik</h3>
                        <button type="button" @click="openModal = false" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
                    </div>

                    <form :action="activeUrl" method="POST" class="flex flex-col flex-1 overflow-hidden">
                        @csrf
                        
                        <div class="p-5 bg-gray-50 border-b border-gray-200 shrink-0">
                            <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Mahasiswa Bimbingan</p>
                            <h4 class="text-lg font-bold text-gray-800 mt-1" x-text="activeMahasiswa"></h4>
                        </div>

                        <!-- Area Scrollable Rubrik -->
                        <div class="p-5 overflow-y-auto flex-1 space-y-4 custom-scrollbar bg-white">
                            @if($rubriks->isEmpty())
                                <div class="p-4 bg-yellow-50 text-yellow-800 rounded-xl border border-yellow-200 text-sm">
                                    <i class="fas fa-exclamation-triangle mr-2"></i> Rubrik Penilaian belum diatur oleh Superadmin. Silakan hubungi Admin Fakultas.
                                </div>
                            @else
                                <p class="text-xs text-gray-500 mb-4 bg-blue-50 p-3 rounded-lg border border-blue-100">
                                    <i class="fas fa-info-circle text-blue-500 mr-1"></i> Masukkan <strong>Nilai Mentah (skala 0 - 100)</strong> pada setiap komponen. Sistem akan mengonversinya secara otomatis berdasarkan persentase bobot.
                                </p>

                                @foreach($rubriks as $rubrik)
                                <div class="p-4 rounded-xl border border-gray-200 hover:border-vokasi-primary/40 hover:bg-gray-50/50 transition-colors flex flex-col md:flex-row gap-4 items-start md:items-center">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="bg-gray-800 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $rubrik->no_urut }}</span>
                                            <h5 class="text-sm font-bold text-gray-800">{{ $rubrik->komponen }}</h5>
                                            <span class="text-xs font-extrabold text-vokasi-primary bg-vokasi-primary/10 px-2 py-0.5 rounded border border-vokasi-primary/20">Bobot: {{ floatval($rubrik->bobot) }}%</span>
                                        </div>
                                        <p class="text-xs text-gray-500 leading-relaxed">{{ $rubrik->indikator }}</p>
                                    </div>
                                    <div class="w-full md:w-32 shrink-0 relative">
                                        <input type="number" step="0.01" min="0" max="100" name="nilai[{{ $rubrik->id }}]" x-model="formScores[{{ $rubrik->id }}]" placeholder="0-100" required class="w-full px-3 py-2.5 text-center text-sm font-bold text-gray-800 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-vokasi-primary focus:border-vokasi-primary">
                                        <span class="absolute right-3 top-2.5 text-xs text-gray-400 font-semibold pointer-events-none">/ 100</span>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Footer Form Modal -->
                        <div class="p-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3 shrink-0">
                            <button type="button" @click="openModal = false" class="px-5 py-2.5 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-bold text-xs transition-colors shadow-sm">Batal</button>
                            <button type="submit" class="px-6 py-2.5 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-xl font-bold text-xs shadow-sm transition-colors flex items-center gap-2">
                                <i class="fas fa-save"></i> Simpan Penilaian Akhir
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL FILTER EXPORT PDF -->
            <div x-show="openExportModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
                <div @click.away="openExportModal = false" class="bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-md overflow-hidden">
                    <div class="bg-emerald-600 px-6 py-4 text-white flex justify-between items-center">
                        <h3 class="font-bold text-base"><i class="fas fa-filter mr-2"></i> Filter Export Berkas Laporan</h3>
                        <button type="button" @click="openExportModal = false" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
                    </div>

                    <form action="{{ route('dashboard-penilaian-listing-mahasiswa-export') }}" method="GET" target="_blank" class="p-6 space-y-4">
                        
                        <!-- Meneruskan Pencarian Teks dari Halaman Utama -->
                        <input type="hidden" name="search" value="{{ request('search') }}">

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Pilih Program Studi</label>
                            <select name="prodi_id" class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:ring-vokasi-primary focus:outline-none">
                                <option value="semua">Semua Program Studi</option>
                                @foreach($prodis as $prodi)
                                    <option value="{{ $prodi->id }}" {{ request('prodi_id') == $prodi->id ? 'selected' : '' }}>
                                        {{ $prodi->nama_prodi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Status Penilaian Dosen</label>
                            <select name="status_nilai" class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:ring-vokasi-primary focus:outline-none">
                                <option value="semua" {{ request('status_nilai') == 'semua' ? 'selected' : '' }}>Semua Status (Lengkap maupun Belum)</option>
                                <option value="lengkap" {{ request('status_nilai') == 'lengkap' ? 'selected' : '' }}>Hanya Mahasiswa yang Sudah Dinilai Selesai</option>
                                <option value="belum" {{ request('status_nilai') == 'belum' ? 'selected' : '' }}>Hanya Mahasiswa yang Belum Dinilai</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Syarat Jam Magang (Logbook)</label>
                            <select name="status_jam" class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:ring-vokasi-primary focus:outline-none">
                                <option value="semua" {{ request('status_jam') == 'semua' ? 'selected' : '' }}>Abaikan Syarat Jam Magang</option>
                                <option value="selesai" {{ request('status_jam') == 'selesai' ? 'selected' : '' }}>Telah Memenuhi Target Minimal Jam Magang ({{ $minJamMagang }} Jam)</option>
                                <option value="belum" {{ request('status_jam') == 'belum' ? 'selected' : '' }}>Belum Memenuhi Target (Magang Belum Selesai)</option>
                            </select>
                        </div>

                        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                            <button type="button" @click="openExportModal = false" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 bg-white font-bold text-xs">Batal</button>
                            <button type="submit" @click="openExportModal = false" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs shadow-sm flex items-center gap-2">
                                <i class="fas fa-print"></i> Generate PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
@endsection