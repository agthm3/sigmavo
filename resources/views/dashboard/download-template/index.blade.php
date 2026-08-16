@extends('layouts.dashboard')

@section('content')
<div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50"
     x-data="{ 
         openModal: false, 
         isEdit: false, 
         editData: {}, 
         activeUrl: '',
         openAddModal() {
             this.isEdit = false;
             this.editData = { prodi_id: 'all', kategori: 'Wajib', versi: 'v1.0' };
             this.activeUrl = '{{ route('dashboard-pelaporan-download-template-store') }}';
             this.openModal = true;
         },
         openEditModal(item, url) {
             this.isEdit = true;
             this.editData = item;
             this.activeUrl = url;
             this.openModal = true;
         }
     }">

    <main class="flex-1 overflow-y-auto bg-gray-50 p-4 lg:p-8 custom-scrollbar">
        
        <!-- HEADER HALAMAN -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Download Template Dokumen Magang</h1>
                <p class="text-sm text-gray-500 mt-1">Unduh dan kelola format resmi laporan, lembar pengesahan, dan berkas administrasi Magang Vokasi UNHAS.</p>
            </div>
            
            <div class="flex items-center gap-3">
                @if($canManage)
                    <button type="button" @click="openAddModal()" class="bg-vokasi-primary hover:bg-vokasi-dark text-white font-bold text-xs py-2.5 px-4 rounded-xl transition shadow-sm flex items-center gap-2">
                        <i class="fas fa-cloud-upload-alt text-sm"></i> Tambah Template Dokumen
                    </button>
                @else
                    <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-teal-50 text-vokasi-primary border border-teal-200">
                        <i class="fas fa-file-signature mr-1.5"></i> Standar Akademik Vokasi
                    </span>
                @endif
            </div>
        </div>

        <!-- NOTIFIKASI FLASH -->
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

        @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-xs shadow-sm">
            <p class="font-bold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal Menyimpan Dokumen:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- CARDS SUMMARY -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 bg-teal-50 text-vokasi-primary rounded-xl flex items-center justify-center text-xl font-bold shrink-0">
                    <i class="fas fa-file-word"></i>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Format Dokumen</p>
                    <h3 class="text-lg font-bold text-gray-800">.DOCX / .PPTX / .PDF</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Dapat disunting di MS Office / Google Docs</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl font-bold shrink-0">
                    <i class="fas fa-book"></i>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Standar Pedoman</p>
                    <h3 class="text-lg font-bold text-gray-800">Panduan Akademik 2026</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Tata letak & format resmi magang</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl font-bold shrink-0">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Ketersediaan Dokumen</p>
                    <h3 class="text-lg font-bold text-gray-800">{{ $templates->count() }} Template Tersedia</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Dikelola Program Studi & Kampus</p>
                </div>
            </div>
        </div>

        <!-- LIST DOKUMEN TEMPLATE -->
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gray-50/50">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Daftar Berkas & Format Laporan</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Pilih dan unduh template sesuai kebutuhan tahapan magang Anda.</p>
                </div>
                
                <!-- Filter Search & Prodi (Khusus Superadmin/Admin) -->
                <form action="{{ route('dashboard-pelaporan-download-template') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    @if($isSuperOrAdmin)
                        <select name="prodi_id" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-xs rounded-xl px-3 py-2 outline-none shadow-sm focus:ring-2 focus:ring-vokasi-primary">
                            <option value="semua" {{ request('prodi_id') == 'semua' ? 'selected' : '' }}>Semua Program Studi</option>
                            <option value="umum" {{ request('prodi_id') == 'umum' ? 'selected' : '' }}>Umum Kampus (Tanpa Prodi)</option>
                            @foreach($prodis as $pr)
                                <option value="{{ $pr->id }}" {{ request('prodi_id') == $pr->id ? 'selected' : '' }}>{{ $pr->nama_prodi }}</option>
                            @endforeach
                        </select>
                    @endif

                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari template..." class="pl-8 pr-3 py-1.5 bg-white border border-gray-300 rounded-xl text-xs outline-none focus:ring-2 focus:ring-vokasi-primary">
                    </div>
                </form>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($templates as $item)
                    @php
                        $ext = strtolower($item->file_extension);
                        $bgIcon = 'bg-blue-100 text-blue-600';
                        $iconClass = 'fas fa-file-word';
                        if (in_array($ext, ['pptx', 'ppt'])) {
                            $bgIcon = 'bg-amber-100 text-amber-600';
                            $iconClass = 'fas fa-file-powerpoint';
                        } elseif ($ext === 'pdf') {
                            $bgIcon = 'bg-red-100 text-red-600';
                            $iconClass = 'fas fa-file-pdf';
                        } elseif (in_array($ext, ['xlsx', 'xls'])) {
                            $bgIcon = 'bg-emerald-100 text-emerald-600';
                            $iconClass = 'fas fa-file-excel';
                        } elseif (in_array($ext, ['zip', 'rar'])) {
                            $bgIcon = 'bg-purple-100 text-purple-600';
                            $iconClass = 'fas fa-file-archive';
                        }
                    @endphp

                    <div class="p-6 hover:bg-gray-50/80 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-start space-x-4">
                            <div class="w-11 h-11 {{ $bgIcon }} rounded-xl flex items-center justify-center text-xl font-bold shrink-0 mt-1 md:mt-0 shadow-sm">
                                <i class="{{ $iconClass }}"></i>
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-bold text-gray-800 text-base">{{ $item->judul_dokumen }}</h3>
                                    
                                    <!-- Badge Kategori -->
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded border
                                        {{ $item->kategori == 'Wajib' ? 'bg-red-50 text-red-700 border-red-200' : '' }}
                                        {{ $item->kategori == 'Resmi' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                                        {{ $item->kategori == 'Penilaian' ? 'bg-purple-50 text-purple-700 border-purple-200' : '' }}
                                        {{ !in_array($item->kategori, ['Wajib', 'Resmi', 'Penilaian']) ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}">
                                        {{ $item->kategori }}
                                    </span>

                                    <!-- Badge Prodi / Kampus -->
                                    @if($item->prodi)
                                        <span class="bg-teal-50 text-vokasi-primary text-[10px] font-bold px-2 py-0.5 rounded border border-teal-200">
                                            {{ $item->prodi->nama_prodi }}
                                        </span>
                                    @else
                                        <span class="bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-0.5 rounded border border-gray-200">
                                            Umum (Semua Prodi)
                                        </span>
                                    @endif
                                </div>

                                <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $item->deskripsi ?? 'Template dokumen resmi untuk kegiatan magang industri.' }}</p>
                                
                                <div class="flex flex-wrap items-center gap-4 text-xs text-gray-400 mt-2">
                                    <span><i class="fas fa-file mr-1 text-gray-500"></i> .{{ strtoupper($item->file_extension) }} ({{ $item->file_size ?? '-' }})</span>
                                    <span><i class="fas fa-code-branch mr-1 text-gray-500"></i> Versi: {{ $item->versi ?? 'v1.0' }}</span>
                                    <span><i class="fas fa-clock mr-1 text-gray-500"></i> Diperbarui: {{ $item->updated_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- ACTIONS: Unduh / Edit / Hapus -->
                        <div class="shrink-0 flex items-center gap-2">
                            <!-- Tombol Download -->
                            <a href="{{ route('dashboard-pelaporan-download-template-file', $item->id) }}" class="inline-flex items-center px-4 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white font-bold text-xs rounded-xl transition shadow-sm hover:shadow-md gap-2">
                                <i class="fas fa-download"></i> Unduh
                            </a>

                            <!-- Tombol Edit & Hapus (Superadmin, Admin, Admin Prodi, Dosen) -->
                            @if($canManage)
                                <button type="button" @click="openEditModal({{ json_encode($item) }}, '{{ route('dashboard-pelaporan-download-template-update', $item->id) }}')" class="p-2 text-gray-600 hover:text-vokasi-primary bg-gray-100 hover:bg-gray-200 rounded-xl transition text-xs font-bold" title="Edit Template">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <form action="{{ route('dashboard-pelaporan-download-template-destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus template ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-xl transition text-xs font-bold" title="Hapus Template">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-gray-400">
                        <i class="fas fa-folder-open text-4xl mb-3 block"></i>
                        <p class="font-bold text-gray-600 text-sm">Belum Ada Template Dokumen Tersedia</p>
                        <p class="text-xs mt-1">Dokumen template yang ditambahkan akan tampil di sini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- PETUNJUK PENULISAN -->
        <div class="bg-amber-50/70 border border-amber-200 rounded-2xl p-6 text-xs text-amber-900 flex items-start space-x-4">
            <i class="fas fa-lightbulb text-amber-600 text-xl shrink-0 mt-0.5"></i>
            <div class="space-y-1">
                <h4 class="font-bold text-sm text-amber-900">Petunjuk Penting Penggunaan Template:</h4>
                <ul class="list-disc list-inside space-y-1 text-amber-800/90">
                    <li>Pastikan format font dan tata letak tidak diubah dari template standar resmi Vokasi UNHAS.</li>
                    <li>Lembar pengesahan wajib ditandatangani oleh Supervisor Lapangan dan Dosen Pembimbing sebelum ujian seminar hasil.</li>
                    <li>Konsultasikan draft laporan akhir kepada Dosen Pembimbing secara berkala melalui menu Asistensi Logbook.</li>
                </ul>
            </div>
        </div>

    </main>

    <!-- ========================================================================= -->
    <!-- MODAL POPUP: FORM TAMBAH / EDIT TEMPLATE DOKUMEN -->
    <!-- ========================================================================= -->
    @if($canManage)
    <div x-show="openModal" 
         x-cloak 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        
        <div @click.away="openModal = false" class="bg-white rounded-3xl shadow-2xl border border-gray-200 w-full max-w-xl overflow-hidden max-h-[90vh] flex flex-col">
            
            <!-- Modal Header -->
            <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center shrink-0">
                <div class="flex items-center gap-2.5">
                    <i class="fas fa-file-upload text-xl"></i>
                    <div>
                        <h3 class="font-bold text-base leading-none" x-text="isEdit ? 'Edit Template Dokumen' : 'Unggah Template Dokumen Baru'"></h3>
                        <p class="text-[11px] text-white/80 mt-0.5">Kelola berkas resmi panduan dan format laporan magang</p>
                    </div>
                </div>
                <button type="button" @click="openModal = false" class="text-white/80 hover:text-white hover:bg-white/10 p-1.5 rounded-xl transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form :action="activeUrl" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 overflow-y-auto custom-scrollbar flex-1 text-xs">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <!-- Judul Dokumen -->
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Judul Dokumen / Template <span class="text-red-500">*</span></label>
                    <input type="text" name="judul_dokumen" :value="isEdit ? editData.judul_dokumen : ''" placeholder="Contoh: Template Laporan Akhir Magang 900 Jam" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none" required>
                </div>

                <!-- PILIH PROGRAM STUDI: TAMPIL JIKA SUPERADMIN ATAU ADMIN -->
                @if($isSuperOrAdmin)
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Target Program Studi <span class="text-red-500">*</span></label>
                    <select name="prodi_id" :value="isEdit ? (editData.prodi_id ?? 'all') : 'all'" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none font-medium">
                        <option value="all">🌐 Semua Program Studi (Umum Kampus Vokasi)</option>
                        @foreach($prodis as $pr)
                            <option value="{{ $pr->id }}">🎓 {{ $pr->nama_prodi }}</option>
                        @endforeach
                    </select>
                    <span class="text-[10px] text-gray-400 mt-1 block">Pilih "Semua Program Studi" jika template berlaku umum untuk seluruh mahasiswa vokasi.</span>
                </div>
                @endif

                <div class="grid grid-cols-2 gap-3">
                    <!-- Kategori -->
                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Kategori Dokumen <span class="text-red-500">*</span></label>
                        <select name="kategori" :value="isEdit ? editData.kategori : 'Wajib'" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none" required>
                            <option value="Wajib">Wajib</option>
                            <option value="Resmi">Resmi</option>
                            <option value="Penilaian">Penilaian</option>
                            <option value="Opsional">Opsional / Panduan</option>
                        </select>
                    </div>

                    <!-- Versi -->
                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Versi Dokumen</label>
                        <input type="text" name="versi" :value="isEdit ? editData.versi : 'v1.0'" placeholder="Contoh: v2.1" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none">
                    </div>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Deskripsi Singkat / Petunjuk</label>
                    <textarea name="deskripsi" x-text="isEdit ? editData.deskripsi : ''" rows="3" placeholder="Jelaskan isi template dan bagian yang wajib diisi mahasiswa..." class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none resize-none"></textarea>
                </div>

                <!-- File Dokumen -->
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">
                        Berkas File Template 
                        <template x-if="isEdit"><span class="text-gray-400 font-normal lowercase">(kosongkan jika tidak diganti)</span></template>
                        <template x-if="!isEdit"><span class="text-red-500">*</span></template>
                    </label>
                    <input type="file" name="file_template" :required="!isEdit" class="w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-vokasi-primary hover:file:bg-teal-100" accept=".docx,.doc,.pptx,.ppt,.pdf,.xlsx,.xls,.zip,.rar">
                    <p class="text-[10px] text-gray-400 mt-1">Mendukung file: DOCX, PPTX, PDF, XLSX, ZIP (Maks. 20 MB).</p>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                    <button type="button" @click="openModal = false" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-medium text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-xl font-bold text-xs shadow-sm flex items-center gap-1.5">
                        <i class="fas fa-save"></i> <span x-text="isEdit ? 'Simpan Perubahan' : 'Unggah Template'"></span>
                    </button>
                </div>
            </form>

        </div>
    </div>
    @endif

</div>
@endsection