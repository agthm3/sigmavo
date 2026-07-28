@extends('layouts.dashboard')

@section('content')
    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50" 
         x-data="{ openModal: false, activeData: null, activeUrl: '' }">

        <!-- CONTENT -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative custom-scrollbar">
            
            <div class="max-w-7xl mx-auto w-full flex-1 space-y-6">
                
                <!-- Page Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Seleksi & Penempatan Pelamar</h2>
                        <p class="text-sm text-gray-500 mt-1">Tinjau berkas pendaftaran mahasiswa, tetapkan status seleksi, dan alokasikan Dosen Pendamping.</p>
                    </div>
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

                <!-- SUMMARY CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-user-group text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Total Pelamar</p>
                            <p class="text-xl font-bold text-gray-800 leading-none mt-1">{{ $totalPelamar }} Orang</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-yellow-50 text-yellow-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-hourglass-start text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Belum Diseleksi</p>
                            <p class="text-xl font-bold text-yellow-600 leading-none mt-1">{{ $totalMenunggu }} Pelamar</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-user-check text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Diterima Magang</p>
                            <p class="text-xl font-bold text-green-600 leading-none mt-1">{{ $totalDiterima }} Orang</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-user-xmark text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Ditolak / Gugur</p>
                            <p class="text-xl font-bold text-red-600 leading-none mt-1">{{ $totalDitolak }} Orang</p>
                        </div>
                    </div>
                </div>

                <!-- TABLE SECTION -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    
                    <!-- Table Toolbar / Filter -->
                    <form action="{{ route('dashboard-daftar-lowongan-seleksi') }}" method="GET" class="p-5 border-b border-gray-100 flex flex-col lg:flex-row justify-between lg:items-center gap-4 bg-gray-50/50">
                        <div class="relative w-full lg:w-96">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama mahasiswa, NIM, atau posisi..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary">
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            <select name="lowongan_id" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option value="semua" {{ request('lowongan_id') == 'semua' ? 'selected' : '' }}>Semua Posisi Lowongan</option>
                                @foreach($lowongans as $l)
                                    <option value="{{ $l->id }}" {{ request('lowongan_id') == $l->id ? 'selected' : '' }}>{{ $l->judul_posisi }} ({{ $l->perusahaan->nama_perusahaan ?? '' }})</option>
                                @endforeach
                            </select>

                            <select name="status" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}>Semua Status</option>
                                <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu Seleksi</option>
                                <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                    </form>

                    <!-- Responsive Table -->
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[1100px]">
                            <thead>
                                <tr class="bg-gray-100/60 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <th class="p-4 w-12 text-center">No</th>
                                    <th class="p-4 w-60">Mahasiswa Pelamar</th>
                                    <th class="p-4 w-56">Posisi & Perusahaan</th>
                                    <th class="p-4 w-48">Dosen Pendamping</th>
                                    <th class="p-4 w-32">Tgl Melamar</th>
                                    <th class="p-4 w-32 text-center">Status Seleksi</th>
                                    <th class="p-4 w-36 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                
                                @forelse($pendaftarans as $index => $item)
                                <tr class="hover:bg-gray-50 transition-colors {{ $item->status_seleksi == 'menunggu' ? 'bg-yellow-50/20' : '' }}">
                                    <td class="p-4 text-center text-gray-500 font-medium">{{ $pendaftarans->firstItem() + $index }}</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($item->mahasiswa->name ?? 'Mhs') }}&background=f3f4f6&color=37A7AC" alt="Avatar" class="w-9 h-9 rounded-full border border-gray-200">
                                            <div>
                                                <p class="font-bold text-gray-800">{{ $item->mahasiswa->name ?? '-' }}</p>
                                                <p class="text-xs text-gray-500">NIM: {{ $item->mahasiswa->mahasiswaProfile->nim ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-bold text-gray-800">{{ $item->lowongan->judul_posisi ?? '-' }}</p>
                                        <p class="text-xs text-vokasi-primary font-medium">{{ $item->lowongan->perusahaan->nama_perusahaan ?? '-' }}</p>
                                    </td>
                                    <td class="p-4">
                                        @if($item->dosen)
                                            <p class="font-bold text-gray-800 text-xs">{{ $item->dosen->name }}</p>
                                            <p class="text-[10px] text-green-600 font-semibold"><i class="fas fa-check-circle"></i> Assigned</p>
                                        @else
                                            <span class="text-xs text-gray-400 italic"><i class="fas fa-user-plus mr-1"></i> Belum Ditetapkan</span>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        <p class="font-medium text-gray-700">{{ $item->created_at->format('d M Y') }}</p>
                                        <p class="text-xs text-gray-400">{{ $item->created_at->format('H:i') }} WITA</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($item->status_seleksi == 'diterima')
                                            <span class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full border border-green-200">
                                                <i class="fas fa-check-circle mr-1"></i> Diterima
                                            </span>
                                        @elseif($item->status_seleksi == 'ditolak')
                                            <span class="inline-flex items-center px-2.5 py-1 bg-red-100 text-red-700 text-[10px] font-bold rounded-full border border-red-200">
                                                <i class="fas fa-times-circle mr-1"></i> Ditolak
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 bg-yellow-100 text-yellow-700 text-[10px] font-bold rounded-full border border-yellow-200">
                                                <i class="fas fa-spinner fa-spin mr-1"></i> Menunggu
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        <button @click="activeData = {{ json_encode($item) }}; activeUrl = '{{ route('dashboard-daftar-lowongan-seleksi-update', $item->id) }}'; openModal = true" 
                                                class="bg-vokasi-primary hover:bg-vokasi-dark text-white text-xs font-bold py-1.5 px-3 rounded shadow-sm transition-colors flex items-center justify-center mx-auto">
                                            <i class="fas fa-file-signature mr-1.5"></i> Tinjau Berkas
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-gray-400">
                                        <i class="fas fa-user-xmark text-3xl mb-2 block"></i> Belum ada data pelamar magang.
                                    </td>
                                </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Controls -->
                    <div class="p-4 border-t border-gray-100 bg-white">
                        {{ $pendaftarans->links() }}
                    </div>

                </div>

            </div>

            <!-- FOOTER -->
            <footer class="mt-8 py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
                Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
            </footer>

        </main>

        <!-- ========================================== -->
        <!-- MODAL POPUP: TINJAU BERKAS & SELEKSI PELAMAR -->
        <!-- ========================================== -->
        <div x-show="openModal" 
             x-cloak 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            
            <div @click.away="openModal = false" class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-3xl overflow-hidden max-h-[90vh] flex flex-col" x-if="activeData">
                
                <!-- Modal Header -->
                <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center shrink-0">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-user-check text-lg"></i>
                        <h3 class="font-bold text-lg">Tinjau Berkas & Keputusan Seleksi</h3>
                    </div>
                    <button @click="openModal = false" class="text-white/80 hover:text-white hover:bg-black/10 p-1.5 rounded-lg transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="p-6 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                    
                    <!-- Profile Pelamar Summary -->
                    <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <img :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent(activeData?.mahasiswa?.name || 'Mhs') + '&background=37A7AC&color=fff'" alt="Pelamar" class="w-16 h-16 rounded-full border-2 border-white shadow-sm shrink-0">
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-bold text-lg text-gray-800" x-text="activeData?.mahasiswa?.name"></h4>
                                    <p class="text-xs text-gray-500">
                                        NIM: <span x-text="activeData?.mahasiswa?.mahasiswa_profile?.nim || '-'"></span> — 
                                        <span x-text="activeData?.mahasiswa?.mahasiswa_profile?.prodi?.nama_prodi || '-'"></span>
                                    </p>
                                </div>
                            </div>
                            <div class="mt-2 text-xs text-gray-600 space-y-1">
                                <p><i class="fas fa-briefcase text-gray-400 mr-1.5"></i> Melamar Posisi: <strong x-text="activeData?.lowongan?.judul_posisi"></strong></p>
                                <p><i class="fas fa-building text-gray-400 mr-1.5"></i> Instansi: <strong x-text="activeData?.lowongan?.perusahaan?.nama_perusahaan"></strong></p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Keputusan Seleksi -->
                    <form :action="activeUrl" method="POST" class="space-y-4 pt-2">
                        @csrf
                        @method('PUT')

                        <h5 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-2 flex items-center">
                            <i class="fas fa-user-gear text-vokasi-primary mr-2"></i> Form Keputusan & Penugasan
                        </h5>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Keputusan Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Keputusan Seleksi <span class="text-red-500">*</span></label>
                                <select name="status_seleksi" :value="activeData?.status_seleksi" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                                    <option value="menunggu">Menunggu Seleksi</option>
                                    <option value="diterima">Diterima Magang</option>
                                    <option value="ditolak">Ditolak / Tidak Lolos</option>
                                    <option value="wawancara">Panggil Wawancara</option>
                                </select>
                            </div>

                            <!-- Assign Dosen Pendamping -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Assign Dosen Pendamping</label>
                                <select name="dosen_id" :value="activeData?.dosen_id" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors">
                                    <option value="">-- Pilih Dosen Pendamping --</option>
                                    @foreach($dosens as $d)
                                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Catatan Seleksi -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan / Alasan Penolakan</label>
                                <textarea name="catatan_seleksi" x-text="activeData?.catatan_seleksi" rows="2" placeholder="Tuliskan catatan khusus untuk mahasiswa..." class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors resize-none"></textarea>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-6 shrink-0">
                            <button type="button" @click="openModal = false" class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 font-medium text-sm transition-colors">
                                Batal
                            </button>
                            <button type="submit" class="px-6 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-lg font-bold text-sm transition-colors shadow-sm flex items-center">
                                <i class="fas fa-check mr-2"></i> Simpan Keputusan
                            </button>
                        </div>
                    </form>

                </div>

            </div>
        </div>

    </div>
@endsection