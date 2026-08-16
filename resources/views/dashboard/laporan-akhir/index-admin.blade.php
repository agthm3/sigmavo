@extends('layouts.dashboard')

@section('content')
<div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50"
     x-data="{ 
         openVerifyModal: false, 
         selectedLaporan: null, 
         verifyUrl: '',
         openModal(item, url) {
             this.selectedLaporan = item;
             this.verifyUrl = url;
             this.openVerifyModal = true;
         }
     }">

    <main class="flex-1 overflow-y-auto bg-gray-50 p-4 lg:p-8 custom-scrollbar">
        
        <!-- HEADER HALAMAN -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Rekapitulasi & Verifikasi Laporan Akhir</h1>
                <p class="text-sm text-gray-500 mt-1">Pantau dan verifikasi dokumen laporan akhir magang 900 jam mahasiswa bimbingan Anda.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-teal-50 text-vokasi-primary border border-teal-200">
                    <i class="fas fa-file-contract mr-1.5"></i> Berkas Laporan Akhir Vokasi
                </span>
            </div>
        </div>

        <!-- NOTIFIKASI SUKSES / ERROR -->
        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between text-sm shadow-sm">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900"><i class="fas fa-times"></i></button>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl flex items-center justify-between text-sm shadow-sm">
            <div class="flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-red-600 text-lg"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900"><i class="fas fa-times"></i></button>
        </div>
        @endif

        <!-- CARDS RINGKASAN STATISTIK -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl font-bold shrink-0">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Total Laporan Masuk</p>
                    <h3 class="text-lg font-bold text-gray-800">{{ $totalLaporan }} Berkas</h3>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl font-bold shrink-0">
                    <i class="fas fa-hourglass-half {{ $totalPending > 0 ? 'animate-pulse' : '' }}"></i>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Perlu Verifikasi</p>
                    <h3 class="text-lg font-bold text-amber-600">{{ $totalPending }} Mahasiswa</h3>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl font-bold shrink-0">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Telah Disetujui</p>
                    <h3 class="text-lg font-bold text-emerald-600">{{ $totalApproved }} Laporan</h3>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-red-50 text-red-600 rounded-xl flex items-center justify-center text-xl font-bold shrink-0">
                    <i class="fas fa-undo"></i>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Status Revisi</p>
                    <h3 class="text-lg font-bold text-red-600">{{ $totalRevisi }} Berkas</h3>
                </div>
            </div>
        </div>

        <!-- TABEL LISTING LAPORAN AKHIR -->
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden mb-8">
            
            <!-- Toolbar Filter -->
            <form action="{{ route('dashboard-mahasiswa-laporan-akhir') }}" method="GET" class="p-5 border-b border-gray-100 flex flex-col md:flex-row justify-between md:items-center gap-4 bg-gray-50/50">
                <div class="relative w-full md:w-80">
                    <i class="fas fa-search absolute left-3.5 top-3 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama mahasiswa, NIM, atau judul..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-primary">
                </div>
                
                <div class="flex flex-wrap items-center gap-2">
                    @if($user->hasAnyRole(['admin', 'superadmin']))
                        <select name="prodi_id" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-xs rounded-xl px-3 py-2 outline-none shadow-sm focus:ring-2 focus:ring-vokasi-primary font-medium">
                            <option value="semua" {{ request('prodi_id') == 'semua' ? 'selected' : '' }}>Semua Program Studi</option>
                            @foreach($prodis as $pr)
                                <option value="{{ $pr->id }}" {{ request('prodi_id') == $pr->id ? 'selected' : '' }}>{{ $pr->nama_prodi }}</option>
                            @endforeach
                        </select>
                    @endif

                    <select name="status" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-xs rounded-xl px-3 py-2 outline-none shadow-sm focus:ring-2 focus:ring-vokasi-primary font-medium">
                        <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}>Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Telah Disetujui (Approved)</option>
                        <option value="revisi" {{ request('status') == 'revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                    </select>
                </div>
            </form>

            <!-- Table -->
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse min-w-[1050px]">
                    <thead>
                        <tr class="bg-gray-100/60 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="p-4 w-12 text-center">No</th>
                            <th class="p-4 w-64">Mahasiswa & Program Studi</th>
                            <th class="p-4 w-72">Judul Laporan Akhir</th>
                            <th class="p-4 w-52">Instansi & Pembimbing</th>
                            <th class="p-4 w-32 text-center">Status</th>
                            <th class="p-4 w-36 text-center">Aksi & Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        @forelse($laporans as $index => $item)
                            @php
                                $mhs = $item->user;
                                $profile = $mhs?->mahasiswaProfile;
                                $pendaftaran = $item->pendaftaran;
                                $isMandiri = $pendaftaran?->jalur_magang === 'mandiri';
                                $namaMitra = $isMandiri ? $pendaftaran?->nama_instansi_mandiri : ($pendaftaran?->lowongan?->perusahaan?->nama_perusahaan ?? '-');
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4 text-center text-gray-500 font-medium">{{ $laporans->firstItem() + $index }}</td>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($mhs?->name ?? 'Mhs') }}&background=E6FFFA&color=0D9488&size=40" alt="Avatar" class="w-10 h-10 rounded-full border border-teal-200 shrink-0">
                                        <div>
                                            <p class="font-bold text-gray-800 text-xs leading-tight">{{ $mhs?->name ?? 'Mahasiswa' }}</p>
                                            <p class="text-[11px] text-gray-500 font-mono mt-0.5">{{ $profile?->nim ?? '-' }}</p>
                                            <p class="text-[10px] text-vokasi-primary font-semibold">{{ $profile?->prodi?->nama_prodi ?? 'Vokasi' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <p class="font-bold text-gray-800 text-xs leading-relaxed">{{ $item->judul_laporan }}</p>
                                    <p class="text-[10px] text-gray-400 mt-1"><i class="fas fa-clock mr-1"></i> {{ $item->updated_at->format('d M Y, H:i') }} WITA</p>
                                    
                                    @if($item->catatan)
                                        <div class="mt-1.5 p-2 bg-red-50 border border-red-100 rounded-lg text-[10px] text-red-700 font-medium">
                                            <strong>Catatan:</strong> {{ $item->catatan }}
                                        </div>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <p class="font-semibold text-gray-800 text-xs leading-tight">{{ $namaMitra }}</p>
                                    <p class="text-[10px] text-gray-500 mt-0.5">DPL: {{ $pendaftaran?->dosen?->name ?? 'Belum Ditugaskan' }}</p>
                                </td>
                                <td class="p-4 text-center">
                                    @if($item->status_verifikasi === 'approved')
                                        <span class="inline-flex items-center px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full border border-emerald-200">
                                            <i class="fas fa-check-double mr-1"></i> Disetujui
                                        </span>
                                    @elseif($item->status_verifikasi === 'revisi')
                                        <span class="inline-flex items-center px-2.5 py-1 bg-red-100 text-red-700 text-[10px] font-bold rounded-full border border-red-200">
                                            <i class="fas fa-undo mr-1"></i> Perlu Revisi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full border border-amber-200 animate-pulse">
                                            <i class="fas fa-clock mr-1"></i> Menunggu
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Tombol Unduh PDF -->
                                        @if($item->file_laporan)
                                            <a href="{{ asset('storage/' . $item->file_laporan) }}" target="_blank" class="p-2 text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 rounded-xl transition text-xs font-bold" title="Buka & Unduh PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        @endif

                                        <!-- Tombol Verifikasi (Modal) -->
                                        <button type="button" @click="openModal({{ json_encode($item) }}, '{{ route('dashboard-laporan-akhir-verifikasi', $item->id) }}')" class="py-1.5 px-3 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-xl transition text-xs font-bold flex items-center gap-1 shadow-sm" title="Verifikasi Laporan">
                                            <i class="fas fa-check-square text-[11px]"></i> Verifikasi
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-12 text-center text-gray-400">
                                    <i class="fas fa-file-signature text-4xl mb-3 block"></i>
                                    <p class="font-bold text-gray-600 text-sm">Belum Ada Mahasiswa Mengunggah Laporan Akhir</p>
                                    <p class="text-xs mt-1">Laporan akhir yang dikirimkan oleh mahasiswa magang akan muncul di sini untuk diperiksa.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100 bg-white">
                {{ $laporans->links() }}
            </div>
        </div>

    </main>

    <!-- ========================================================================= -->
    <!-- MODAL POPUP: FORM VERIFIKASI LAPORAN AKHIR -->
    <!-- ========================================================================= -->
    <div x-show="openVerifyModal" 
         x-cloak 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        
        <div @click.away="openVerifyModal = false" class="bg-white rounded-3xl shadow-2xl border border-gray-200 w-full max-w-lg overflow-hidden flex flex-col" x-if="selectedLaporan">
            
            <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center shrink-0">
                <div class="flex items-center gap-2.5">
                    <i class="fas fa-check-circle text-xl"></i>
                    <div>
                        <h3 class="font-bold text-base leading-none">Verifikasi Laporan Akhir</h3>
                        <p class="text-[11px] text-white/80 mt-0.5">Tentukan persetujuan atau catatan perbaikan laporan</p>
                    </div>
                </div>
                <button type="button" @click="openVerifyModal = false" class="text-white/80 hover:text-white p-1.5 rounded-xl">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form :action="verifyUrl" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                @method('PUT')

                <!-- Ringkasan Mahasiswa -->
                <div class="p-3.5 bg-gray-50 rounded-2xl border border-gray-200 space-y-1">
                    <p class="text-gray-500">Mahasiswa: <strong class="text-gray-800 text-sm" x-text="selectedLaporan?.user?.name"></strong></p>
                    <p class="text-gray-500">NIM: <span class="text-gray-700 font-mono" x-text="selectedLaporan?.user?.mahasiswa_profile?.nim || '-'"></span></p>
                    <p class="text-gray-500">Judul: <span class="font-semibold text-gray-800 block mt-0.5" x-text="selectedLaporan?.judul_laporan"></span></p>
                </div>

                <!-- Opsi Verifikasi -->
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-2">Keputusan Verifikasi <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 p-3 bg-emerald-50 border border-emerald-200 rounded-xl cursor-pointer hover:bg-emerald-100 transition-colors">
                            <input type="radio" name="status_verifikasi" value="approved" :checked="selectedLaporan?.status_verifikasi === 'approved'" class="text-emerald-600 focus:ring-emerald-500" required>
                            <span class="font-bold text-emerald-800">Setujui (Approved)</span>
                        </label>

                        <label class="flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-xl cursor-pointer hover:bg-red-100 transition-colors">
                            <input type="radio" name="status_verifikasi" value="revisi" :checked="selectedLaporan?.status_verifikasi === 'revisi'" class="text-red-600 focus:ring-red-500" required>
                            <span class="font-bold text-red-800">Minta Revisi</span>
                        </label>
                    </div>
                </div>

                <!-- Catatan Verifikator -->
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Catatan / Masukan Perbaikan (Opsional)</label>
                    <textarea name="catatan" x-text="selectedLaporan?.catatan" rows="3" placeholder="Tuliskan catatan perbaikan jika status laporan adalah revisi..." class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none resize-none"></textarea>
                </div>

                <!-- Footer Action -->
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" @click="openVerifyModal = false" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-medium text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-xl font-bold text-xs shadow-sm flex items-center gap-1.5">
                        <i class="fas fa-save"></i> Simpan Keputusan
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection