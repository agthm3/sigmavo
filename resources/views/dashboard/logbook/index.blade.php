@extends('layouts.dashboard')

@section('content')
    <!-- CDN PDF-LIB Untuk Kompresi PDF Client-Side -->
    <script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>

    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50"
         x-data="{ 
            openEditModal: false, 
            openFotoModal: false, 
            activeLogbook: null, 
            activeEditUrl: '', 
            fotoUrl: '',
            editSelectedCpmk: []
         }">

        <!-- CONTENT -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative custom-scrollbar">
            
            <div class="max-w-7xl mx-auto w-full flex-1 space-y-6">
                
                @if(isset($isLocked) && $isLocked)
                    
                    @if(isset($sudahPembekalan) && !$sudahPembekalan)
                        <!-- STATE TERKUNCI: SUDAH DITERIMA TAPI BELUM PEMBEKALAN -->
                        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-8 text-center max-w-2xl mx-auto my-12 shadow-sm text-amber-900">
                            <i class="fas fa-exclamation-triangle text-5xl mb-4 text-amber-500"></i>
                            <h3 class="font-bold text-lg mb-2 text-gray-800">Akses Logbook Terkunci</h3>
                            <p class="text-sm text-gray-700">Anda telah diterima magang, namun Anda belum melakukan Konfirmasi Kehadiran pada acara Pembekalan Magang. Silakan menuju ke menu <strong>Pembekalan Magang</strong> untuk mengonfirmasi kehadiran Anda terlebih dahulu.</p>
                            <div class="mt-5">
                                <a href="{{ route('dashboard-mahasiswa-pembekalan-magang') }}" class="bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs py-2.5 px-5 rounded-xl shadow-sm transition-colors inline-block">
                                    <i class="fas fa-arrow-right mr-1.5"></i> Buka Menu Pembekalan
                                </a>
                            </div>
                        </div>
                    @else
                        <!-- STATE TERKUNCI: BELUM DITERIMA MAGANG -->
                        <div class="bg-white rounded-2xl p-8 md:p-12 border border-amber-200 shadow-sm text-center max-w-2xl mx-auto my-8 space-y-4">
                            <div class="w-16 h-16 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center mx-auto text-2xl border border-amber-200 shadow-sm">
                                <i class="fas fa-lock"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Akses Logbook Belum Terbuka</h3>
                            <p class="text-sm text-gray-500 leading-relaxed">
                                Fitur pengisian <strong>Logbook Kegiatan Harian</strong> hanya dapat digunakan oleh mahasiswa yang telah <strong>diterima secara resmi</strong> pada program magang industri.
                            </p>
                            <div class="p-4 bg-amber-50/60 rounded-xl text-xs text-amber-800 border border-amber-200 font-medium text-left flex items-start gap-2.5">
                                <i class="fas fa-info-circle text-amber-600 text-base shrink-0 mt-0.5"></i>
                                <p>
                                    Jika Anda sudah mengajukan magang, silakan pantau perkembangan verifikasi berkas dan penerbitan surat pada menu 
                                    <a href="{{ route('dashboard-mahasiswa-status-pengajuan') }}" class="underline font-bold text-amber-900 hover:text-amber-700">Status Pengajuan Magang</a>.
                                </p>
                            </div>
                        </div>
                    @endif

                @else
                    <!-- STATE NORMAL: SUDAH DITERIMA MAGANG & SUDAH PEMBEKALAN -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Logbook Harian Magang</h2>
                            <p class="text-sm text-gray-500 mt-1">Isi catatan harian aktivitas magang Anda beserta keterkaitan Capaian Pembelajaran Mata Kuliah (CPMK) dan foto kegiatan.</p>
                        </div>
                        <div>
                            <a href="{{ route('dashboard-mahasiswa-logbook-export-word') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2.5 px-4 rounded-xl transition-colors shadow-sm flex items-center gap-2">
                                <i class="fas fa-file-word text-sm"></i> Export Logbook (Word)
                            </a>
                        </div>
                    </div>
                    
                    <!-- NOTIFIKASI SUKSES / ERROR -->
                    @if(session('success'))
                    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center justify-between text-sm shadow-sm">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900"><i class="fas fa-times"></i></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center justify-between text-sm shadow-sm">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-exclamation-circle text-red-600 text-lg"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900"><i class="fas fa-times"></i></button>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm shadow-sm">
                        <p class="font-bold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal menyimpan logbook:</p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- FORM PENGISIAN LOGBOOK -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 relative z-20">
                        <div class="bg-vokasi-primary/5 px-6 py-4 border-b border-gray-100 flex items-center justify-between rounded-t-xl">
                            <h3 class="font-bold text-gray-800 flex items-center">
                                <i class="fas fa-pen-alt text-vokasi-primary mr-2"></i> Form Pengisian Logbook Harian
                            </h3>
                            <span class="text-xs font-semibold bg-blue-100 text-blue-700 px-3 py-1 rounded-full border border-blue-200">
                                Hari Ini: {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}
                            </span>
                        </div>
                        
                        <form action="{{ route('dashboard-mahasiswa-logbook-store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                            @csrf
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                
                                <!-- Kolom Kiri: Uraian & Multi-Select CPMK (2 Cols) -->
                                <div class="lg:col-span-2 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Uraian Kegiatan / Pekerjaan <span class="text-red-500">*</span></label>
                                        <textarea name="uraian_kegiatan" rows="4" placeholder="Jelaskan aktivitas, tugas, alat/software yang digunakan, dan hasil pekerjaan hari ini..." class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors resize-none text-sm" required></textarea>
                                    </div>

                                    <!-- MULTI-SELECT DROPDOWN CPMK TERKAIT -->
                                    <div x-data="dropdownCpmkComponent()" class="relative z-30">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Capaian Pembelajaran (CPMK) Terkait <span class="text-gray-400 font-normal text-xs">(Pilih satu atau lebih)</span>
                                        </label>
                                        
                                        <!-- Box Input Trigger -->
                                        <div @click="toggleDropdown()" class="min-h-[42px] p-2 bg-gray-50 border border-gray-300 rounded-lg cursor-pointer flex flex-wrap items-center gap-1.5 focus-within:ring-2 focus-within:ring-vokasi-primary">
                                            <template x-for="item in selectedCpmk" :key="item">
                                                <span class="inline-flex items-center gap-1 bg-vokasi-primary/10 text-vokasi-primary border border-vokasi-primary/20 text-xs font-semibold px-2.5 py-1 rounded-md max-w-full truncate">
                                                    <i class="fas fa-bullseye text-[10px]"></i>
                                                    <span x-text="item" class="truncate"></span>
                                                    <button type="button" @click.stop="removeCpmk(item)" class="hover:text-red-500 text-xs font-bold shrink-0">&times;</button>
                                                    <input type="hidden" name="mata_kuliah[]" :value="item">
                                                </span>
                                            </template>

                                            <template x-if="selectedCpmk.length === 0">
                                                <span class="text-xs text-gray-400 pl-1">-- Pilih / Cari CPMK Terkait --</span>
                                            </template>

                                            <i class="fas fa-chevron-down text-gray-400 text-xs ml-auto pr-2"></i>
                                        </div>

                                        <!-- Dropdown Popover dengan Search -->
                                        <div x-show="openCpmk" @click.away="openCpmk = false" x-cloak class="absolute left-0 right-0 top-full mt-1.5 bg-white border border-gray-200 rounded-xl shadow-2xl p-2 max-h-60 overflow-y-auto custom-scrollbar z-50">
                                            <div class="p-1 mb-2 border-b border-gray-100">
                                                <input type="text" x-model="searchCpmk" placeholder="Cari kode atau deskripsi CPMK..." class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-md text-xs focus:outline-none focus:border-vokasi-primary" @click.stop>
                                            </div>
                                            <div class="space-y-1">
                                                <template x-for="c in filteredCpmk" :key="c">
                                                    <div @click="toggleCpmk(c)" class="px-3 py-2 text-xs rounded-md cursor-pointer hover:bg-teal-50 flex items-center justify-between transition-colors" :class="selectedCpmk.includes(c) ? 'bg-teal-50 text-vokasi-primary font-bold' : 'text-gray-700'">
                                                        <span x-text="c" class="pr-2 leading-relaxed"></span>
                                                        <i x-show="selectedCpmk.includes(c)" class="fas fa-check text-vokasi-primary text-xs shrink-0"></i>
                                                    </div>
                                                </template>
                                                <template x-if="filteredCpmk.length === 0">
                                                    <div class="p-3 text-center text-xs text-gray-400">Tidak ada CPMK ditemukan.</div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <!-- Kolom Kanan: Upload Foto & Submit Button -->
                                <div class="space-y-4">
                                    <div x-data="{ fileName: null, filePreview: null, isCompressing: false, fileSizeInfo: '' }">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Dokumentasi Foto 
                                            <span class="text-gray-400 font-normal text-xs ml-1">(Bisa pilih s.d 10 MB)</span>
                                        </label>
                                        <div class="flex items-center justify-center w-full">
                                            <label class="flex flex-col items-center justify-center w-full h-36 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors relative overflow-hidden">
                                                
                                                <template x-if="!filePreview && !isCompressing">
                                                    <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-2">
                                                        <i class="fas fa-cloud-upload-alt text-gray-400 text-2xl mb-1"></i>
                                                        <p class="mb-1 text-xs text-gray-500"><span class="font-semibold text-vokasi-primary">Klik upload foto</span></p>
                                                        <p class="text-[10px] text-gray-400">PNG, JPG, WEBP (Max 10 MB)</p>
                                                    </div>
                                                </template>

                                                <template x-if="isCompressing">
                                                    <div class="flex flex-col items-center justify-center p-4 text-center">
                                                        <i class="fas fa-spinner fa-spin text-vokasi-primary text-2xl mb-2"></i>
                                                        <p class="text-xs font-bold text-gray-700">Mengompresi Gambar...</p>
                                                        <p class="text-[10px] text-emerald-600 font-medium mt-0.5">Menyesuaikan ukuran ke &lt; 300KB</p>
                                                    </div>
                                                </template>

                                                <template x-if="filePreview && !isCompressing">
                                                    <div class="w-full h-full relative group">
                                                        <img :src="filePreview" class="w-full h-full object-cover">
                                                        <div class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center text-white text-xs font-bold opacity-0 group-hover:opacity-100 transition-opacity p-2 text-center">
                                                            <span>Ganti Foto</span>
                                                            <span class="text-[10px] font-semibold text-emerald-300 mt-1" x-text="fileSizeInfo"></span>
                                                        </div>
                                                    </div>
                                                </template>

                                                <input type="file" id="foto_store_input" name="foto_dokumentasi" class="hidden" accept="image/jpeg,image/png,image/webp" @change="
                                                    const input = $event.target;
                                                    const file = input.files[0];
                                                    if(file) {
                                                        if(file.size > 10 * 1024 * 1024) {
                                                            alert('Ukuran file melebihi batas maksimal 10 MB!');
                                                            input.value = '';
                                                            return;
                                                        }
                                                        isCompressing = true;
                                                        const origSizeStr = (file.size / (1024 * 1024)).toFixed(2) + 'MB';
                                                        handleSmartFileCompression(file, 300, 500, function(compressedFile) {
                                                            const container = new DataTransfer();
                                                            container.items.add(compressedFile);
                                                            input.files = container.files;
                                                            
                                                            const newSizeStr = (compressedFile.size / 1024).toFixed(0) + 'KB';
                                                            fileSizeInfo = `Awal: ${origSizeStr} → Hasil: ${newSizeStr}`;
                                                            fileName = compressedFile.name;
                                                            
                                                            const reader = new FileReader();
                                                            reader.onload = (e) => { 
                                                                filePreview = e.target.result; 
                                                                isCompressing = false;
                                                            };
                                                            reader.readAsDataURL(compressedFile);
                                                        });
                                                    }
                                                " />
                                            </label>
                                        </div>
                                    </div>

                                    <div class="pt-2">
                                        <button type="submit" class="w-full bg-vokasi-primary hover:bg-vokasi-dark text-white font-bold py-3 rounded-xl transition-colors shadow-sm flex items-center justify-center gap-2 text-sm">
                                            <i class="fas fa-save"></i> Simpan Logbook
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>

                    <!-- TABEL RIWAYAT LOGBOOK -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative z-10">
                        
                        <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row justify-between md:items-center gap-4 bg-gray-50/50">
                            <div class="flex items-center">
                                <i class="fas fa-table text-vokasi-primary mr-2 text-lg"></i>
                                <h3 class="font-bold text-gray-800">Tabel Riwayat Kegiatan Logbook</h3>
                            </div>
                            
                            <form action="{{ route('dashboard-mahasiswa-logbook') }}" method="GET" class="flex items-center gap-2">
                                <select name="bulan" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-xs rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                    <option value="semua" {{ request('bulan') == 'semua' ? 'selected' : '' }}>Semua Bulan</option>
                                    <option value="2026-08" {{ request('bulan') == '2026-08' ? 'selected' : '' }}>Agustus 2026</option>
                                    <option value="2026-07" {{ request('bulan') == '2026-07' ? 'selected' : '' }}>Juli 2026</option>
                                </select>
                            </form>
                        </div>

                        <div class="overflow-x-auto custom-scrollbar">
                            <table class="w-full text-left border-collapse min-w-[1000px]">
                                <thead>
                                    <tr class="bg-gray-100/50 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <th class="p-4 w-12 text-center">No</th>
                                        <th class="p-4 w-32">Tanggal</th>
                                        <th class="p-4 min-w-[280px]">Uraian Kegiatan & CPMK Terkait</th>
                                        <th class="p-4 w-28 text-center">Foto</th>
                                        <th class="p-4 w-36">Status</th>
                                        <th class="p-4 w-48">Catatan Dosen</th>
                                        <th class="p-4 w-28 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm divide-y divide-gray-100">
                                    
                                    @forelse($logbooks as $index => $item)
                                    <tr class="hover:bg-gray-50 transition-colors {{ $item->status_asistensi == 'revisi' ? 'bg-red-50/20' : '' }}">
                                        <td class="p-4 text-center text-gray-500 font-medium">{{ $logbooks->firstItem() + $index }}</td>
                                        <td class="p-4 whitespace-nowrap">
                                            <p class="font-bold text-gray-800">{{ $item->tanggal->format('d M Y') }}</p>
                                            <p class="text-xs text-gray-500">{{ $item->tanggal->isoFormat('dddd') }}</p>
                                        </td>
                                        <td class="p-4 text-gray-700 leading-relaxed">
                                            <p class="mb-2 text-justify">{{ $item->uraian_kegiatan }}</p>

                                            <!-- Badge CPMK Terkait -->
                                            @if(!empty($item->mata_kuliah) && is_array($item->mata_kuliah))
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    @foreach($item->mata_kuliah as $cpmk)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-teal-50 text-vokasi-primary text-[10px] font-semibold border border-vokasi-primary/20">
                                                            <i class="fas fa-bullseye text-[9px] mr-1"></i> {{ $cpmk }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td class="p-4 text-center">
                                            @if($item->foto_dokumentasi)
                                                <button type="button" @click="fotoUrl = '{{ asset('storage/' . $item->foto_dokumentasi) }}'; openFotoModal = true" class="w-12 h-12 rounded-lg bg-gray-100 border border-gray-200 mx-auto flex items-center justify-center text-vokasi-primary overflow-hidden hover:opacity-80 transition-opacity shadow-sm">
                                                    <img src="{{ asset('storage/' . $item->foto_dokumentasi) }}" alt="Foto" class="w-full h-full object-cover">
                                                </button>
                                            @else
                                                <span class="text-gray-400 text-xs italic">-</span>
                                            @endif
                                        </td>
                                        <td class="p-4">
                                            @if($item->status_asistensi == 'approved')
                                                <span class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full border border-green-200 whitespace-nowrap">
                                                    <i class="fas fa-check-double mr-1"></i> Approved
                                                </span>
                                            @elseif($item->status_asistensi == 'revisi')
                                                <span class="inline-flex items-center px-2.5 py-1 bg-red-100 text-red-700 text-[10px] font-bold rounded-full border border-red-200 whitespace-nowrap">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i> Perlu Revisi
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 bg-yellow-100 text-yellow-700 text-[10px] font-bold rounded-full border border-yellow-200 whitespace-nowrap">
                                                    <i class="fas fa-spinner fa-spin mr-1"></i> Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-4">
                                            @if($item->catatan_dosen)
                                                <p class="text-xs p-2 rounded border
                                                    {{ $item->status_asistensi == 'revisi' ? 'bg-red-50 text-red-700 border-red-100 font-medium' : 'bg-green-50 text-green-700 border-green-100' }}">
                                                    {{ $item->catatan_dosen }}
                                                </p>
                                            @else
                                                <span class="text-gray-400 italic text-xs">Belum ada catatan</span>
                                            @endif
                                        </td>
                                        <td class="p-4 text-center">
                                            @if($item->status_asistensi == 'approved')
                                                <span class="text-gray-400 text-xs" title="Telah disetujui"><i class="fas fa-lock"></i></span>
                                            @else
                                                <div class="flex items-center justify-center gap-1.5">
                                                    <button type="button" @click="activeLogbook = {{ json_encode($item) }}; editSelectedCpmk = {{ json_encode($item->mata_kuliah ?? []) }}; activeEditUrl = '{{ route('dashboard-mahasiswa-logbook-update', $item->id) }}'; openEditModal = true" 
                                                            class="{{ $item->status_asistensi == 'revisi' ? 'bg-red-500 hover:bg-red-600 text-white font-bold px-2.5 py-1 text-xs' : 'text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-1.5' }} rounded transition-colors" 
                                                            title="Edit / Revisi">
                                                        @if($item->status_asistensi == 'revisi')
                                                            Revisi
                                                        @else
                                                            <i class="fas fa-edit"></i>
                                                        @endif
                                                    </button>

                                                    <form action="{{ route('dashboard-mahasiswa-logbook-destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus entri logbook ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-1.5 rounded transition-colors" title="Hapus">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="p-8 text-center text-gray-400">
                                            <i class="fas fa-book-open text-3xl mb-2 block"></i> Belum ada catatan logbook harian.
                                        </td>
                                    </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                        
                        <div class="p-4 border-t border-gray-100 bg-white">
                            {{ $logbooks->links() }}
                        </div>

                    </div>
                @endif

            </div>

            <!-- FOOTER -->
            <footer class="mt-8 py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
                Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
            </footer>

        </main>

        @if(!isset($isLocked) || !$isLocked)
            <!-- MODAL POPUP: EDIT / REVISI LOGBOOK -->
            <div x-show="openEditModal" 
                 x-cloak 
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                
                <div @click.away="openEditModal = false" class="bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-xl overflow-hidden" x-if="activeLogbook">
                    
                    <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center">
                        <h3 class="font-bold text-lg"><i class="fas fa-edit mr-2"></i> Edit / Perbaiki Logbook</h3>
                        <button type="button" @click="openEditModal = false" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
                    </div>

                    <form :action="activeEditUrl" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                        @csrf
                        @method('PUT')

                        <template x-if="activeLogbook?.status_asistensi === 'revisi'">
                            <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-800">
                                <strong>Catatan Dosen untuk Perbaikan:</strong>
                                <p class="mt-1 font-medium" x-text="activeLogbook?.catatan_dosen"></p>
                            </div>
                        </template>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Uraian Kegiatan / Pekerjaan <span class="text-red-500">*</span></label>
                            <textarea name="uraian_kegiatan" x-text="activeLogbook?.uraian_kegiatan" rows="4" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-primary resize-none" required></textarea>
                        </div>

                        <!-- Multi-select CPMK Terkait (Edit Mode) -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Capaian Pembelajaran (CPMK) Terkait</label>
                            <div class="grid grid-cols-1 gap-2 p-3 bg-gray-50 border border-gray-200 rounded-xl max-h-40 overflow-y-auto custom-scrollbar">
                                @foreach($daftarCpmk as $cpmk)
                                <label class="flex items-start space-x-2 text-xs text-gray-700 cursor-pointer">
                                    <input type="checkbox" name="mata_kuliah[]" value="{{ $cpmk }}" :checked="editSelectedCpmk.includes('{{ $cpmk }}')" class="mt-0.5 rounded border-gray-300 text-vokasi-primary focus:ring-vokasi-primary shrink-0">
                                    <span class="leading-relaxed">{{ $cpmk }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                                Ganti Foto Dokumentasi (Opsional) 
                                <span class="text-vokasi-primary font-normal text-[11px] ml-1">(Bisa upload s.d 10 MB)</span>
                            </label>
                            <input type="file" name="foto_dokumentasi" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-vokasi-primary/10 file:text-vokasi-primary hover:file:bg-vokasi-primary/20" accept="image/jpeg,image/png,image/webp" @change="
                                const input = $event.target;
                                const file = input.files[0];
                                if(file) {
                                    if(file.size > 10 * 1024 * 1024) {
                                        alert('Ukuran file melebihi batas maksimal 10 MB!');
                                        input.value = '';
                                        return;
                                    }
                                    handleSmartFileCompression(file, 300, 500, function(compressedFile) {
                                        const container = new DataTransfer();
                                        container.items.add(compressedFile);
                                        input.files = container.files;
                                    });
                                }
                            ">
                        </div>

                        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                            <button type="button" @click="openEditModal = false" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-medium text-xs">Batal</button>
                            <button type="submit" class="px-5 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-xl font-bold text-xs shadow-sm">Simpan Perubahan</button>
                        </div>
                    </form>

                </div>
            </div>

            <!-- MODAL POPUP: PRATINJAU FOTO DOKUMENTASI -->
            <div x-show="openFotoModal" 
                 x-cloak 
                 class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                
                <div @click.away="openFotoModal = false" class="bg-white rounded-2xl shadow-xl overflow-hidden max-w-2xl w-full">
                    <div class="p-3 bg-gray-100 flex justify-between items-center border-b">
                        <span class="text-xs font-bold text-gray-700">Foto Dokumentasi Kegiatan Logbook</span>
                        <button type="button" @click="openFotoModal = false" class="text-gray-500 hover:text-gray-800"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="p-4 flex items-center justify-center bg-black/90 min-h-[300px]">
                        <img :src="fotoUrl" alt="Dokumentasi" class="max-h-[80vh] max-w-full object-contain rounded">
                    </div>
                </div>
            </div>
        @endif

    </div>

    <!-- GLOBAL COMPRESSION ENGINE SCRIPT (PDF & GAMBAR) -->
    <script>
        /**
         * FUNGSI UTAMA PENGOMPRES FILE OTOMATIS OFFLINE DI BROWSER
         * Target: Gambar < maxImgKb (Default 300 KB), PDF < maxPdfKb (Default 500 KB)
         */
        async function handleSmartFileCompression(file, maxImgKb = 300, maxPdfKb = 500, callback) {
            const isImage = file.type.startsWith('image/');
            const isPdf = file.type === 'application/pdf';

            // Jika Gambar, kompres via HTML5 Canvas
            if (isImage) {
                compressImageToTarget(file, maxImgKb, callback);
            } 
            // Jika PDF, optimalkan via PDF-Lib
            else if (isPdf) {
                compressPdfToTarget(file, maxPdfKb, callback);
            } 
            // File tipe lain
            else {
                callback(file);
            }
        }

        // 1. KOMPRESI GAMBAR (< 300 KB)
        function compressImageToTarget(file, targetKb, callback) {
            const targetBytes = targetKb * 1024;
            const reader = new FileReader();

            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    let width = img.width;
                    let height = img.height;

                    // Skala rasio resolusi maks 1280px
                    const maxDim = 1280;
                    if (width > maxDim || height > maxDim) {
                        if (width > height) {
                            height = Math.round((height * maxDim) / width);
                            width = maxDim;
                        } else {
                            width = Math.round((width * maxDim) / height);
                            height = maxDim;
                        }
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    let quality = 0.85;

                    function attemptCompress() {
                        canvas.toBlob(function(blob) {
                            if (!blob) {
                                callback(file);
                                return;
                            }

                            // Jika ukuran masih > 300KB dan kualitas > 0.3, kurangi kualitas bertahap
                            if (blob.size > targetBytes && quality > 0.35) {
                                quality -= 0.12;
                                attemptCompress();
                            } else {
                                const compressedFile = new File([blob], file.name, {
                                    type: 'image/jpeg',
                                    lastModified: Date.now()
                                });
                                callback(compressedFile);
                            }
                        }, 'image/jpeg', quality);
                    }

                    attemptCompress();
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        // 2. KOMPRESI PDF (< 500 KB)
        async function compressPdfToTarget(file, targetKb, callback) {
            try {
                const arrayBuffer = await file.arrayBuffer();
                const pdfDoc = await PDFLib.PDFDocument.load(arrayBuffer, { ignoreEncryption: true });

                // Bersihkan metadata bawaan untuk mengurangi ukuran byte PDF
                pdfDoc.setTitle('');
                pdfDoc.setAuthor('');
                pdfDoc.setSubject('');
                pdfDoc.setKeywords([]);
                pdfDoc.setProducer('');
                pdfDoc.setCreator('');

                const pdfBytes = await pdfDoc.save({ useObjectStreams: true });
                const compressedFile = new File([pdfBytes], file.name, {
                    type: 'application/pdf',
                    lastModified: Date.now()
                });

                callback(compressedFile);
            } catch (err) {
                console.warn('Gagal mengompresi PDF secara penuh, mengirimkan file asli:', err);
                callback(file);
            }
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('dropdownCpmkComponent', () => ({
                openCpmk: false,
                searchCpmk: '',
                selectedCpmk: [],
                allCpmk: {!! json_encode($daftarCpmk) !!},
                
                toggleDropdown() {
                    this.openCpmk = !this.openCpmk;
                },
                
                get filteredCpmk() {
                    if (!this.searchCpmk) return this.allCpmk;
                    return this.allCpmk.filter(c => c.toLowerCase().includes(this.searchCpmk.toLowerCase()));
                },

                toggleCpmk(c) {
                    if (this.selectedCpmk.includes(c)) {
                        this.selectedCpmk = this.selectedCpmk.filter(item => item !== c);
                    } else {
                        this.selectedCpmk.push(c);
                    }
                },

                removeCpmk(c) {
                    this.selectedCpmk = this.selectedCpmk.filter(item => item !== c);
                }
            }));
        });
    </script>
@endsection