@extends('layouts.dashboard')

@section('content')
    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50" 
         x-data="{ openModal: false, isEdit: false, editData: {}, activeUrl: '' }">

        <!-- CONTENT -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative custom-scrollbar">
            
            <div class="max-w-7xl mx-auto w-full flex-1 space-y-6">
                
                <!-- Page Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Kelola Program & Lowongan Magang</h2>
                        <p class="text-sm text-gray-500 mt-1">Buat, publikasikan, dan atur kuota posisi magang dari mitra industri.</p>
                    </div>
                    <div class="flex gap-2">
                        <!-- TRIGGER MODAL TAMBAH -->
                        <button @click="isEdit = false; editData = {}; openModal = true" class="bg-vokasi-primary hover:bg-vokasi-dark text-white text-sm font-semibold py-2 px-4 rounded-lg transition-colors shadow-sm flex items-center">
                            <i class="fas fa-plus-circle mr-2"></i> Buat Lowongan Magang Baru
                        </button>
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

                @if($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm">
                    <p class="font-bold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal menyimpan data:</p>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- SUMMARY CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-briefcase text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Total Lowongan</p>
                            <p class="text-xl font-bold text-gray-800 leading-none mt-1">{{ $totalLowongan }} Posisi</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-globe text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Status Dipublikasi</p>
                            <p class="text-xl font-bold text-green-600 leading-none mt-1">{{ $totalPublished }} Active</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-users-viewfinder text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Total Kuota Dibuka</p>
                            <p class="text-xl font-bold text-purple-600 leading-none mt-1">{{ $totalKuota }} Kursi</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-calendar-xmark text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Draft / Ditutup</p>
                            <p class="text-xl font-bold text-red-600 leading-none mt-1">{{ $totalDraftClosed }} Posisi</p>
                        </div>
                    </div>
                </div>

                <!-- TABLE SECTION -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    
                    <!-- Table Toolbar / Filter -->
                    <form action="{{ route('dashboard-daftar-lowongan-listing-program') }}" method="GET" class="p-5 border-b border-gray-100 flex flex-col lg:flex-row justify-between lg:items-center gap-4 bg-gray-50/50">
                        <div class="relative w-full lg:w-96">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari posisi, perusahaan, atau prodi..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary">
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            <select name="status" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}>Semua Status</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Publik (Aktif)</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Pendaftaran Ditutup</option>
                            </select>

                            <select name="mode_kerja" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option value="semua" {{ request('mode_kerja') == 'semua' ? 'selected' : '' }}>Semua Mode Kerja</option>
                                <option value="WFO" {{ request('mode_kerja') == 'WFO' ? 'selected' : '' }}>On-site (WFO)</option>
                                <option value="Hybrid" {{ request('mode_kerja') == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                                <option value="WFH" {{ request('mode_kerja') == 'WFH' ? 'selected' : '' }}>Remote (WFH)</option>
                            </select>
                        </div>
                    </form>

                    <!-- Responsive Table -->
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[1100px]">
                            <thead>
                                <tr class="bg-gray-100/60 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <th class="p-4 w-12 text-center">No</th>
                                    <th class="p-4 w-60">Posisi & Perusahaan</th>
                                    <th class="p-4 w-44">Target Prodi</th>
                                    <th class="p-4 w-36 text-center">Sisa / Total Kuota</th>
                                    <th class="p-4 w-36">Batas Pendaftaran</th>
                                    <th class="p-4 w-32 text-center">Status</th>
                                    <th class="p-4 w-36 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                
                                @forelse($lowongans as $index => $item)
                                <tr class="hover:bg-gray-50 transition-colors {{ $item->status == 'draft' ? 'bg-gray-50/50' : '' }}">
                                    <td class="p-4 text-center text-gray-500 font-medium">{{ $lowongans->firstItem() + $index }}</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-teal-50 border border-teal-100 flex items-center justify-center text-vokasi-primary shrink-0 font-bold">
                                                {{ $item->perusahaan->inisial ?? 'PT' }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800">{{ $item->judul_posisi }}</p>
                                                <p class="text-xs text-vokasi-primary font-medium">{{ $item->perusahaan->nama_perusahaan ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-blue-50 text-blue-700 text-[11px] font-semibold rounded">
                                            {{ $item->prodi?->nama_prodi ?? 'Semua Prodi Vokasi' }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="font-bold text-gray-800">{{ $item->kuota_terisi }} / {{ $item->kuota }} Kursi</div>
                                        @if($item->kuota_terisi >= $item->kuota)
                                            <span class="text-[10px] text-red-500 font-semibold">(Kuota Penuh)</span>
                                        @else
                                            <span class="text-[10px] text-orange-600 font-semibold">(Tersisa {{ $item->kuota - $item->kuota_terisi }})</span>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        <p class="font-medium text-red-600">{{ $item->batas_pendaftaran ? $item->batas_pendaftaran->format('d M Y') : '-' }}</p>
                                        <p class="text-xs text-gray-400">Durasi: {{ $item->durasi }}</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($item->status == 'published')
                                            <span class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full border border-green-200">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span> Dipublikasi
                                            </span>
                                        @elseif($item->status == 'closed')
                                            <span class="inline-flex items-center px-2.5 py-1 bg-yellow-100 text-yellow-700 text-[10px] font-bold rounded-full border border-yellow-200">
                                                Ditutup
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 bg-gray-200 text-gray-600 text-[10px] font-bold rounded-full border border-gray-300">
                                                Draft
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- TOGGLE PUBLISH -->
                                            <form action="{{ route('dashboard-daftar-lowongan-listing-program-toggle', $item->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                @if($item->status == 'published')
                                                    <button type="submit" class="bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-bold py-1.5 px-2 rounded transition-colors border border-amber-200" title="Ubah ke Draft">
                                                        Unpublish
                                                    </button>
                                                @else
                                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-1.5 px-2.5 rounded transition-colors shadow-sm" title="Publikasikan">
                                                        Publish
                                                    </button>
                                                @endif
                                            </form>

                                            <!-- EDIT BUTTON -->
                                            <button @click="isEdit = true; editData = {{ json_encode($item) }}; activeUrl = '{{ route('dashboard-daftar-lowongan-listing-program-update', $item->id) }}'; openModal = true" 
                                                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold py-1.5 px-2.5 rounded transition-colors" title="Edit Lowongan">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <!-- DELETE BUTTON -->
                                            <form action="{{ route('dashboard-daftar-lowongan-listing-program-destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus lowongan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold py-1.5 px-2.5 rounded transition-colors border border-red-200" title="Hapus Lowongan">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-gray-400">
                                        <i class="fas fa-briefcase text-3xl mb-2 block"></i> Belum ada lowongan magang dibuat.
                                    </td>
                                </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Controls -->
                    <div class="p-4 border-t border-gray-100 bg-white">
                        {{ $lowongans->links() }}
                    </div>

                </div>

            </div>

            <!-- FOOTER -->
            <footer class="mt-8 py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
                Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
            </footer>

        </main>

        <!-- ========================================== -->
        <!-- MODAL POPUP: BUAT / EDIT LOWONGAN MAGANG -->
        <!-- ========================================== -->
        <div x-show="openModal" 
             x-cloak 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            
            <div @click.away="openModal = false" class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-3xl overflow-hidden max-h-[90vh] flex flex-col">
                
                <!-- Modal Header -->
                <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center shrink-0">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-plus-circle text-lg"></i>
                        <h3 class="font-bold text-lg" x-text="isEdit ? 'Edit Lowongan Magang' : 'Buat Lowongan Magang Baru'"></h3>
                    </div>
                    <button @click="openModal = false" class="text-white/80 hover:text-white hover:bg-black/10 p-1.5 rounded-lg transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Modal Body / Form -->
                <form :action="isEdit ? activeUrl : '{{ route('dashboard-daftar-lowongan-listing-program-store') }}'" method="POST" class="p-6 space-y-4 overflow-y-auto custom-scrollbar flex-1">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <!-- Pilih Perusahaan Mitra -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Perusahaan / Instansi Mitra <span class="text-red-500">*</span></label>
                            <select name="perusahaan_id" :value="isEdit ? editData.perusahaan_id : ''" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                                <option value="" disabled selected>Pilih Perusahaan Mitra</option>
                                @foreach($perusahaans as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Judul Posisi / Lowongan -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Judul Posisi Magang <span class="text-red-500">*</span></label>
                            <input type="text" name="judul_posisi" :value="isEdit ? editData.judul_posisi : ''" placeholder="Contoh: STEM Product Designer Intern" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                        </div>

                        <!-- Target Prodi -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Khusus Program Studi <span class="text-red-500">*</span></label>
                            <select name="prodi_id" :value="isEdit ? (editData.prodi_id ?? 'all') : 'all'" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                                <option value="all">Semua Prodi Vokasi</option>
                                @foreach($prodis as $pr)
                                    <option value="{{ $pr->id }}">{{ $pr->nama_prodi }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Mode Kerja -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mode Kerja <span class="text-red-500">*</span></label>
                            <select name="mode_kerja" :value="isEdit ? editData.mode_kerja : 'WFO'" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                                <option value="WFO">On-site (WFO)</option>
                                <option value="Hybrid">Hybrid (WFO & WFH)</option>
                                <option value="WFH">Remote (WFH)</option>
                            </select>
                        </div>

                        <!-- Kuota Mahasiswa -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kuota Mahasiswa Diterima <span class="text-red-500">*</span></label>
                            <input type="number" name="kuota" min="1" :value="isEdit ? editData.kuota : 2" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                        </div>

                        <!-- Batas Pendaftaran -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Batas Akhir Pendaftaran <span class="text-red-500">*</span></label>
                            <input type="date" name="batas_pendaftaran" :value="isEdit ? (editData.batas_pendaftaran ? editData.batas_pendaftaran.split('T')[0] : '') : ''" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                        </div>

                        <!-- Durasi -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Durasi Program</label>
                            <input type="text" name="durasi" :value="isEdit ? editData.durasi : '6 Bulan'" placeholder="Contoh: 6 Bulan / 900 Jam" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors">
                        </div>

                        <!-- Deskripsi & Jobdesc -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Pekerjaan & Tanggung Jawab <span class="text-red-500">*</span></label>
                            <textarea name="deskripsi" x-text="isEdit ? editData.deskripsi : ''" rows="4" placeholder="Tuliskan tugas utama dan kualifikasi yang dibutuhkan..." class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors resize-none" required></textarea>
                        </div>

                        <!-- Status Publikasi -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Publikasi Lowongan</label>
                            <div class="flex gap-4 mt-1">
                                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="status" value="published" :checked="!isEdit || editData.status === 'published'" class="text-vokasi-primary focus:ring-vokasi-primary">
                                    Langsung Dipublikasikan (Aktif)
                                </label>
                                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="status" value="draft" :checked="isEdit && editData.status === 'draft'" class="text-vokasi-primary focus:ring-vokasi-primary">
                                    Simpan Sebagai Draft
                                </label>
                                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" name="status" value="closed" :checked="isEdit && editData.status === 'closed'" class="text-vokasi-primary focus:ring-vokasi-primary">
                                    Pendaftaran Ditutup
                                </label>
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-6 shrink-0">
                        <button type="button" @click="openModal = false" class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 font-medium text-sm transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-lg font-bold text-sm transition-colors shadow-sm flex items-center">
                            <i class="fas fa-paper-plane mr-2"></i> <span x-text="isEdit ? 'Perbarui Lowongan' : 'Simpan Lowongan'"></span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
@endsection 