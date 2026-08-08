@extends('layouts.dashboard')

@section('content')
     <!-- PUSAT VARIABEL ALPINE.JS -->
     <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative custom-scrollbar"
           x-data="{ 
                openModal: false, 
                activeUrl: '',
                activeLaporan: { name: '', judul: '', status: 'pending', catatan: '' } 
           }">
            
            <div class="max-w-7xl mx-auto w-full flex-1 space-y-6">
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Semua Laporan Akhir Magang</h2>
                        <p class="text-sm text-gray-500 mt-1">Pantau, unduh, dan verifikasi dokumen laporan akhir magang seluruh mahasiswa.</p>
                    </div>
                </div>

                @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between text-sm shadow-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900"><i class="fas fa-times"></i></button>
                </div>
                @endif

                @if(session('error'))
                <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl flex items-center justify-between text-sm shadow-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-circle text-red-600 text-lg"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900"><i class="fas fa-times"></i></button>
                </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Total Laporan Masuk</p>
                                <h3 class="text-3xl font-bold text-gray-800">{{ $totalLaporan }}</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center">
                                <i class="fas fa-folder-open text-lg"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Menunggu Verifikasi</p>
                                <h3 class="text-3xl font-bold text-yellow-600">{{ $totalMenunggu }}</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-yellow-50 text-yellow-500 flex items-center justify-center">
                                <i class="fas fa-clock text-lg"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Laporan Approved</p>
                                <h3 class="text-3xl font-bold text-emerald-600">{{ $totalApproved }}</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center">
                                <i class="fas fa-check-double text-lg"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Perlu Revisi</p>
                                <h3 class="text-3xl font-bold text-red-600">{{ $totalRevisi }}</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center">
                                <i class="fas fa-undo text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <form action="{{ route('dashboard-verifikasi-daftar-mahasiswa-semua-laporan') }}" method="GET" class="p-5 border-b border-gray-100 flex flex-col lg:flex-row justify-between lg:items-center gap-4 bg-gray-50/50">
                        <div class="relative w-full lg:w-96">
                            <i class="fas fa-search absolute left-3.5 top-3 text-gray-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama mahasiswa, NIM, atau judul laporan..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-primary">
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            @if(!$currentUser->hasRole('admin_prodi'))
                            <select name="prodi_id" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-xs rounded-xl focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option value="semua" {{ request('prodi_id') == 'semua' ? 'selected' : '' }}>Semua Program Studi</option>
                                @foreach($prodis as $p)
                                    <option value="{{ $p->id }}" {{ request('prodi_id') == $p->id ? 'selected' : '' }}>{{ $p->nama_prodi }}</option>
                                @endforeach
                            </select>
                            @endif

                            <select name="status" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-xs rounded-xl focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}>Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Dosen</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved Dosen</option>
                                <option value="revisi" {{ request('status') == 'revisi' ? 'selected' : '' }}>Revisi</option>
                            </select>
                        </div>
                    </form>

                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[1100px]">
                            <thead>
                                <tr class="bg-gray-100/60 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <th class="p-4 w-12 text-center">No</th>
                                    <th class="p-4 w-60">Mahasiswa & Prodi</th>
                                    <th class="p-4 w-56">Tempat Magang</th>
                                    <th class="p-4 min-w-[220px]">Dokumen Laporan</th>
                                    <th class="p-4 w-32">Tgl Submit</th>
                                    <th class="p-4 w-36 text-center">Status Verifikasi</th>
                                    <th class="p-4 w-32 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                @forelse($laporans as $index => $item)
                                <tr class="hover:bg-gray-50 transition-colors {{ $item->status_verifikasi == 'revisi' ? 'bg-red-50/20' : '' }}">
                                    <td class="p-4 text-center text-gray-500 font-medium">{{ $laporans->firstItem() + $index }}</td>
                                    <td class="p-4">
                                        <p class="font-bold text-gray-800">{{ $item->user->name ?? '-' }}</p>
                                        <p class="text-xs text-gray-500">NIM: {{ $item->user->mahasiswaProfile->nim ?? '-' }}</p>
                                        <p class="text-[10px] text-vokasi-primary mt-0.5 font-semibold">{{ $item->user->mahasiswaProfile->prodi->nama_prodi ?? '-' }}</p>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-bold text-gray-800 text-xs">{{ $item->pendaftaran->lowongan->perusahaan->nama_perusahaan ?? $item->pendaftaran->nama_instansi_mandiri ?? '-' }}</p>
                                        <p class="text-xs text-gray-500">{{ $item->pendaftaran->lowongan->judul_posisi ?? 'Magang Mandiri' }}</p>
                                    </td>
                                    <td class="p-4">
                                        @if($item->file_laporan)
                                            <a href="{{ asset('storage/' . $item->file_laporan) }}" target="_blank" class="inline-flex items-center gap-2 bg-blue-50 border border-blue-200 p-2 rounded-xl hover:bg-blue-100 transition-colors">
                                                <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                                                <div>
                                                    <p class="text-xs font-bold text-blue-900 leading-tight truncate max-w-[180px]">{{ $item->judul_laporan ?? 'File_Laporan.pdf' }}</p>
                                                    <p class="text-[10px] text-blue-600">Klik untuk unduh/lihat</p>
                                                </div>
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Belum ada file</span>
                                        @endif
                                    </td>
                                    <td class="p-4 whitespace-nowrap">
                                        <p class="font-medium text-gray-700 text-xs">{{ $item->created_at->format('d M Y') }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $item->created_at->format('H:i') }} WITA</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($item->status_verifikasi == 'approved')
                                            <span class="inline-flex items-center px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full border border-emerald-200">
                                                <i class="fas fa-check-double mr-1"></i> Approved
                                            </span>
                                        @elseif($item->status_verifikasi == 'revisi')
                                            <span class="inline-flex items-center px-2.5 py-1 bg-red-100 text-red-700 text-[10px] font-bold rounded-full border border-red-200">
                                                <i class="fas fa-undo mr-1"></i> Perlu Revisi
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 bg-yellow-100 text-yellow-700 text-[10px] font-bold rounded-full border border-yellow-200">
                                                <i class="fas fa-spinner fa-spin mr-1"></i> Menunggu Dosen
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        <!-- TOMBOL PEMICU (Menggunakan Dataset) -->
                                        <button type="button" 
                                                data-name="{{ $item->user->name ?? '-' }}"
                                                data-judul="{{ $item->judul_laporan ?? '-' }}"
                                                data-status="{{ $item->status_verifikasi }}"
                                                data-catatan="{{ $item->catatan ?? '' }}"
                                                data-url="{{ route('dashboard-verifikasi-daftar-mahasiswa-semua-laporan-update', $item->id) }}"
                                                @click="
                                                    activeLaporan.name = $event.currentTarget.dataset.name;
                                                    activeLaporan.judul = $event.currentTarget.dataset.judul;
                                                    activeLaporan.status = $event.currentTarget.dataset.status;
                                                    activeLaporan.catatan = $event.currentTarget.dataset.catatan;
                                                    activeUrl = $event.currentTarget.dataset.url;
                                                    openModal = true;
                                                " 
                                                class="bg-vokasi-primary hover:bg-vokasi-dark text-white text-xs font-bold py-1.5 px-3 rounded-xl shadow-sm transition-colors">
                                            Verifikasi
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-gray-400">
                                        <i class="fas fa-folder-open text-3xl mb-2 block"></i> Belum ada dokumen laporan akhir.
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
            </div>

            <footer class="mt-8 py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
                Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
            </footer>

            <!-- MODAL VERIFIKASI DOSEN (SEKARANG ADA DI DALAM TAG MAIN) -->
            <div x-show="openModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div @click.away="openModal = false" class="bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-lg overflow-hidden">
                    <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center">
                        <h3 class="font-bold text-base"><i class="fas fa-file-signature mr-2"></i> Verifikasi Laporan Akhir Dosen</h3>
                        <button type="button" @click="openModal = false" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
                    </div>

                    <form :action="activeUrl" method="POST" class="p-6 space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 text-xs space-y-1">
                            <p class="text-gray-500">Mahasiswa: <strong class="text-gray-800" x-text="activeLaporan.name"></strong></p>
                            <p class="text-gray-500">Judul Laporan: <strong class="text-vokasi-primary" x-text="activeLaporan.judul"></strong></p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Keputusan Verifikasi Dosen <span class="text-red-500">*</span></label>
                            <select name="status_verifikasi" x-model="activeLaporan.status" class="w-full px-3.5 py-2 text-xs bg-gray-50 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-vokasi-primary" required>
                                <option value="pending">Menunggu Verifikasi</option>
                                <option value="approved">Approved (Disetujui Dosen Pembimbing)</option>
                                <option value="revisi">Revisi (Kembalikan ke Mahasiswa)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Catatan Dosen</label>
                            <textarea name="catatan" x-model="activeLaporan.catatan" rows="3" placeholder="Tuliskan catatan revisi atau komentar Dosen Pembimbing..." class="w-full px-3.5 py-2 text-xs bg-gray-50 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-vokasi-primary resize-none"></textarea>
                        </div>

                        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                            <button type="button" @click="openModal = false" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-medium text-xs">Batal</button>
                            <button type="submit" class="px-5 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-xl font-bold text-xs shadow-sm">Simpan Keputusan</button>
                        </div>
                    </form>
                </div>
            </div>
            
        </main> <!-- PENUTUP TAG MAIN DI SINI -->
@endsection