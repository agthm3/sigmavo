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
                        <p class="text-sm text-gray-500 mt-1">Atur kuota penerimaan mahasiswa, masa pendaftaran, dan seleksi kandidat magang industri.</p>
                    </div>
                    <div class="flex gap-2">
                        <!-- TRIGGER MODAL TAMBAH -->
                        <button @click="isEdit = false; editData = {}; openModal = true" class="bg-vokasi-primary hover:bg-vokasi-dark text-white text-sm font-semibold py-2 px-4 rounded-xl transition-colors shadow-sm flex items-center gap-2">
                            <i class="fas fa-plus-circle"></i> Buat Lowongan Baru
                        </button>
                    </div>
                </div>

                <!-- FITUR BARU: NOTIFIKASI SPV BARU DIBUAT (DENGAN TOMBOL SALIN) -->
                @if(session('success_spv'))
                <div class="bg-emerald-50 border border-emerald-300 rounded-xl p-5 shadow-sm relative overflow-hidden" x-data="{ copied: false }">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 text-xl shrink-0">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-emerald-900 text-sm mb-1">{{ session('success_spv')['message'] }}</h4>
                            <div class="bg-white p-3 rounded-lg border border-emerald-100 mt-2 font-mono text-xs text-gray-700 select-all">
                                <p>Email Login : <strong>{{ session('success_spv')['email'] }}</strong></p>
                                <p>Password : <strong class="text-red-600">{{ session('success_spv')['password'] }}</strong></p>
                            </div>
                            <button @click="navigator.clipboard.writeText('Email: {{ session('success_spv')['email'] }}\nPassword: {{ session('success_spv')['password'] }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                                    class="mt-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-1.5 px-3 rounded-lg transition-colors flex items-center gap-2">
                                <i class="fas" :class="copied ? 'fa-check' : 'fa-copy'"></i> 
                                <span x-text="copied ? 'Berhasil Disalin!' : 'Salin Kredensial SPV'"></span>
                            </button>
                        </div>
                    </div>
                </div>
                @endif

                <!-- NOTIFIKASI SUKSES / ERROR NORMAL -->
                @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center justify-between text-sm shadow-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900"><i class="fas fa-times"></i></button>
                </div>
                @endif

                @if($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm shadow-sm">
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
                            <i class="fas fa-user-check text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Target Penerimaan</p>
                            <p class="text-xl font-bold text-purple-600 leading-none mt-1">{{ $totalKuota }} Mahasiswa</p>
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
                        <table class="w-full text-left border-collapse min-w-[1200px]">
                            <thead>
                                <tr class="bg-gray-100/60 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <th class="p-4 w-12 text-center">No</th>
                                    <th class="p-4 w-60">Posisi & Perusahaan</th>
                                    <th class="p-4 w-44">SPV Program (Baru)</th>
                                    <th class="p-4 w-32 text-center">Pendaftar</th>
                                    <th class="p-4 w-36 text-center">Kuota Diterima</th>
                                    <th class="p-4 w-36">Batas Pendaftaran</th>
                                    <th class="p-4 w-28 text-center">Status</th>
                                    <th class="p-4 w-36 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                
                                @forelse($lowongans as $index => $item)
                                @php
                                    $diterima = $item->total_diterima ?? 0;
                                    $pelamar = $item->total_pelamar ?? 0;
                                @endphp
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
                                                <p class="text-[10px] text-gray-400 mt-0.5"><i class="fas fa-bullseye text-gray-300"></i> {{ $item->prodi?->nama_prodi ?? 'Semua Prodi' }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- KOLOM SPV BARU -->
                                    <td class="p-4">
                                        <p class="font-bold text-gray-800 text-xs">
                                            <i class="fas fa-user-tie text-gray-400 mr-1"></i> {{ $item->spv->name ?? 'Belum Diatur' }}
                                        </p>
                                        <p class="text-[10px] text-gray-500">{{ $item->spv->email ?? '-' }}</p>
                                    </td>

                                    <!-- JUMLAH PENDAFTAR -->
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center gap-1 font-bold text-gray-800 bg-gray-100 px-2.5 py-1 rounded-lg text-xs">
                                            <i class="fas fa-users text-gray-500 text-[11px]"></i> {{ $pelamar }} Pelamar
                                        </span>
                                    </td>

                                    <!-- KUOTA PENERIMAAN -->
                                    <td class="p-4 text-center">
                                        <div class="font-bold text-gray-800">{{ $diterima }} / {{ $item->kuota }} Diterima</div>
                                        @if($diterima >= $item->kuota)
                                            <span class="text-[10px] text-red-500 font-semibold">(Kuota Penuh)</span>
                                        @else
                                            <span class="text-[10px] text-emerald-600 font-semibold">(Sisa Kuota: {{ $item->kuota - $diterima }})</span>
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
                                    <td colspan="8" class="p-8 text-center text-gray-400">
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

        <!-- MODAL POPUP: BUAT / EDIT LOWONGAN MAGANG (2 KOLOM: DATA LOWONGAN & DATA SPV) -->
        <div x-show="openModal" 
             x-cloak 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            
            <div @click.away="openModal = false" class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-4xl overflow-hidden max-h-[95vh] flex flex-col">
                
                <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center shrink-0">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-plus-circle text-lg"></i>
                        <h3 class="font-bold text-lg" x-text="isEdit ? 'Edit Lowongan Magang' : 'Buat Lowongan Magang Baru'"></h3>
                    </div>
                    <button @click="openModal = false" class="text-white/80 hover:text-white hover:bg-black/10 p-1.5 rounded-lg transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <form :action="isEdit ? activeUrl : '{{ route('dashboard-daftar-lowongan-listing-program-store') }}'" method="POST" class="flex-1 overflow-y-auto custom-scrollbar flex flex-col md:flex-row">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <!-- BAGIAN KIRI: DATA LOWONGAN -->
                    <div class="w-full md:w-3/5 p-6 border-b md:border-b-0 md:border-r border-gray-200 space-y-4">
                        <h4 class="font-bold text-sm text-gray-800 border-b border-gray-100 pb-2 mb-3">
                            <i class="fas fa-briefcase text-vokasi-primary mr-1"></i> Data Program Magang
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-700 mb-1">Perusahaan / Instansi Mitra <span class="text-red-500">*</span></label>
                                <select name="perusahaan_id" :value="isEdit ? editData.perusahaan_id : ''" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                                    <option value="" disabled selected>Pilih Perusahaan Mitra</option>
                                    @foreach($perusahaans as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-700 mb-1">Judul Posisi Magang / Stase Poli <span class="text-red-500">*</span></label>
                                <input type="text" name="judul_posisi" :value="isEdit ? editData.judul_posisi : ''" placeholder="Contoh: Poli Bedah Mulut / IT Support Intern" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Khusus Program Studi <span class="text-red-500">*</span></label>
                                <select name="prodi_id" :value="isEdit ? (editData.prodi_id ?? 'all') : 'all'" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                                    <option value="all">Semua Prodi Vokasi</option>
                                    @foreach($prodis as $pr)
                                        <option value="{{ $pr->id }}">{{ $pr->nama_prodi }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Mode Kerja <span class="text-red-500">*</span></label>
                                <select name="mode_kerja" :value="isEdit ? editData.mode_kerja : 'WFO'" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                                    <option value="WFO">On-site (WFO)</option>
                                    <option value="Hybrid">Hybrid (WFO & WFH)</option>
                                    <option value="WFH">Remote (WFH)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Batas Akhir Pendaftaran <span class="text-red-500">*</span></label>
                                <input type="date" name="batas_pendaftaran" :value="isEdit ? (editData.batas_pendaftaran ? editData.batas_pendaftaran.split('T')[0] : '') : ''" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Kuota Mahasiswa Diterima <span class="text-red-500">*</span></label>
                                <input type="number" name="kuota" min="1" :value="isEdit ? editData.kuota : 2" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-700 mb-1">Deskripsi Pekerjaan & Tanggung Jawab <span class="text-red-500">*</span></label>
                                <textarea name="deskripsi" x-text="isEdit ? editData.deskripsi : ''" rows="3" placeholder="Tuliskan tugas utama dan kualifikasi yang dibutuhkan..." class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors resize-none" required></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN KANAN: DATA SPV & STATUS -->
                    <div class="w-full md:w-2/5 p-6 bg-gray-50 flex flex-col justify-between">
                        
                        <div class="space-y-4">
                            <h4 class="font-bold text-sm text-gray-800 border-b border-gray-200 pb-2 mb-3">
                                <i class="fas fa-user-tie text-vokasi-primary mr-1"></i> Data Supervisor (SPV)
                            </h4>
                            
                            <div class="bg-blue-50 border border-blue-200 p-3 rounded-xl mb-4 text-[10px] text-blue-800 leading-relaxed font-medium">
                                <i class="fas fa-info-circle text-blue-600"></i> Jika email SPV belum terdaftar, akun SPV akan otomatis dibuatkan dan Password akan dimunculkan setelah Anda klik Simpan.
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Lengkap SPV <span class="text-red-500">*</span></label>
                                <input type="text" name="spv_name" :value="isEdit ? (editData.spv?.name || '') : ''" placeholder="Nama Supervisor Divisi" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary" required>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Email Resmi SPV <span class="text-red-500">*</span></label>
                                <input type="email" name="spv_email" :value="isEdit ? (editData.spv?.email || '') : ''" placeholder="email_spv@perusahaan.com" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary" required>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">No. HP/WA (Opsional)</label>
                                <input type="text" name="spv_no_hp" placeholder="081234567..." class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary">
                            </div>

                            <hr class="my-4 border-gray-200">

                            <!-- RADIO BUTTONS STATUS -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2">Status Publikasi Lowongan</label>
                                <div class="flex flex-col gap-2">
                                    <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer">
                                        <input type="radio" name="status" value="published" :checked="!isEdit || editData.status === 'published'" class="text-vokasi-primary focus:ring-vokasi-primary">
                                        Langsung Dipublikasikan (Aktif)
                                    </label>
                                    <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer">
                                        <input type="radio" name="status" value="draft" :checked="isEdit && editData.status === 'draft'" class="text-vokasi-primary focus:ring-vokasi-primary">
                                        Simpan Sebagai Draft
                                    </label>
                                    <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer">
                                        <input type="radio" name="status" value="closed" :checked="isEdit && editData.status === 'closed'" class="text-vokasi-primary focus:ring-vokasi-primary">
                                        Pendaftaran Ditutup
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- SUBMIT BUTTON -->
                        <div class="mt-6 pt-4 border-t border-gray-200 flex gap-2">
                            <button type="button" @click="openModal = false" class="w-1/3 py-2.5 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-bold text-xs transition-colors">
                                Batal
                            </button>
                            <button type="submit" class="w-2/3 py-2.5 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-xl font-bold text-xs transition-colors shadow-md flex items-center justify-center gap-2">
                                <i class="fas fa-save"></i> <span x-text="isEdit ? 'Simpan Perubahan' : 'Buat Lowongan'"></span>
                            </button>
                        </div>

                    </div>
                </form>

            </div>
        </div>

    </div>
@endsection