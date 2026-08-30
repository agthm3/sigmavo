@extends('layouts.dashboard')

@section('content')
    <!-- CDN PDF-LIB Untuk Kompresi PDF Client-Side -->
    <script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>

    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50"
         x-data="{ 
            openFotoModal: false, 
            activeLogbook: null, 
            fotoUrl: '',
         }">

        <!-- CONTENT -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative custom-scrollbar">
            
            <div class="max-w-7xl mx-auto w-full flex-1 space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-history text-red-500"></i> Logbook Susulan (Terlewat)
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Isi catatan harian untuk hari-hari magang yang belum sempat Anda catat ke dalam sistem.</p>
                    </div>
                </div>
                
                <!-- PEMBERITAHUAN KHUSUS SUSULAN -->
                <div class="bg-red-50 border border-red-200 p-4 rounded-2xl flex items-start gap-3 text-red-800 text-xs shadow-sm">
                    <i class="fas fa-info-circle text-red-600 text-base mt-0.5 shrink-0"></i>
                    <div>
                        <strong class="block mb-1 text-sm text-red-900">Akses Khusus Susulan Terbuka!</strong>
                        <p>Dosen pembimbing telah membukakan akses susulan untuk Anda. Silakan isi kegiatan magang dengan <strong>memilih tanggal yang terlewat secara teliti</strong>. Setiap form yang dikirim akan otomatis men-generate data absensi kehadiran untuk tanggal tersebut agar dapat disahkan oleh Dosen.</p>
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

                <!-- FORM PENGISIAN LOGBOOK SUSULAN -->
                <div class="bg-white rounded-xl shadow-sm border border-red-200 relative z-20">
                    <div class="bg-red-50/50 px-6 py-4 border-b border-red-100 flex items-center justify-between rounded-t-xl">
                        <h3 class="font-bold text-red-900 flex items-center">
                            <i class="fas fa-pen-alt text-red-600 mr-2"></i> Form Pengisian Logbook Susulan
                        </h3>
                    </div>
                    
                    <form action="{{ route('dashboard-mahasiswa-logbook-terlewat-store') }}" 
                          method="POST" 
                          enctype="multipart/form-data" 
                          class="p-6"
                          onsubmit="showLogbookLoading(event)">
                        @csrf
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            
                            <!-- Kolom Kiri: Tanggal, Uraian & Multi-Select CPMK -->
                            <div class="lg:col-span-2 space-y-4">
                                
                                <!-- Tanggal Terlewat -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1 uppercase text-xs">Pilih Tanggal Terlewat <span class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal" max="{{ date('Y-m-d', strtotime('-1 day')) }}" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400 transition-colors text-sm font-semibold text-gray-700">
                                </div>

                                <!-- Uraian Kegiatan -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Uraian Kegiatan / Pekerjaan <span class="text-red-500">*</span></label>
                                    <textarea name="uraian_kegiatan" rows="4" placeholder="Jelaskan aktivitas, tugas, alat/software yang digunakan, dan hasil pekerjaan pada tanggal tersebut..." class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400 transition-colors resize-none text-sm" required></textarea>
                                </div>

                                <!-- MULTI-SELECT DROPDOWN CPMK TERKAIT -->
                                <div x-data="dropdownCpmkComponent()" class="relative z-30">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Capaian Pembelajaran (CPMK) Terkait <span class="text-gray-400 font-normal text-xs">(Pilih satu atau lebih)</span>
                                    </label>
                                    
                                    <div @click="toggleDropdown()" class="min-h-[42px] p-2 bg-gray-50 border border-gray-300 rounded-lg cursor-pointer flex flex-wrap items-center gap-1.5 focus-within:ring-2 focus-within:ring-red-200 focus-within:border-red-400">
                                        <template x-for="item in selectedCpmk" :key="item">
                                            <span class="inline-flex items-center gap-1 bg-red-50 text-red-700 border border-red-200 text-xs font-semibold px-2.5 py-1 rounded-md max-w-full truncate">
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
                                            <input type="text" x-model="searchCpmk" placeholder="Cari kode atau deskripsi CPMK..." class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-md text-xs focus:outline-none focus:border-red-400" @click.stop>
                                        </div>
                                        <div class="space-y-1">
                                            <template x-for="c in filteredCpmk" :key="c">
                                                <div @click="toggleCpmk(c)" class="px-3 py-2 text-xs rounded-md cursor-pointer hover:bg-red-50 flex items-center justify-between transition-colors" :class="selectedCpmk.includes(c) ? 'bg-red-50 text-red-700 font-bold' : 'text-gray-700'">
                                                    <span x-text="c" class="pr-2 leading-relaxed"></span>
                                                    <i x-show="selectedCpmk.includes(c)" class="fas fa-check text-red-600 text-xs shrink-0"></i>
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
                            <div class="space-y-4 mt-6 lg:mt-0">
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

                                            <input type="file" name="foto_dokumentasi" class="hidden" accept="image/jpeg,image/png,image/webp" required @change="
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
                                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl transition-colors shadow-sm flex items-center justify-center gap-2 text-sm">
                                        <i class="fas fa-paper-plane"></i> Kirim Logbook Susulan
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>

                <!-- TABEL RIWAYAT LOGBOOK SUSULAN -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative z-10 mt-8">
                    
                    <div class="p-5 border-b border-gray-100 flex items-center bg-gray-50/50">
                        <i class="fas fa-history text-red-500 mr-2 text-lg"></i>
                        <h3 class="font-bold text-gray-800">Riwayat Catatan Susulan Terkirim</h3>
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
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                
                                @forelse($logbooks as $index => $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-center text-gray-500 font-medium">{{ $logbooks->firstItem() + $index }}</td>
                                    <td class="p-4 whitespace-nowrap">
                                        <p class="font-bold text-gray-800">{{ $item->tanggal->format('d M Y') }}</p>
                                        <p class="text-xs text-gray-500">{{ $item->tanggal->isoFormat('dddd') }}</p>
                                        <span class="inline-block mt-1 px-2 py-0.5 text-[9px] font-bold rounded bg-red-100 text-red-700 border border-red-200 uppercase">Jalur Susulan</span>
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
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-gray-400">
                                        <i class="fas fa-book-open text-3xl mb-2 block"></i> Belum ada riwayat logbook susulan.
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

            </div>

            <!-- FOOTER -->
            <footer class="mt-8 py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
                Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
            </footer>

        </main>

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

    </div>

    <!-- GLOBAL COMPRESSION ENGINE SCRIPT (GAMBAR ONLY) -->
    <script>
        async function handleSmartFileCompression(file, maxImgKb = 300, maxPdfKb = 500, callback) {
            compressImageToTarget(file, maxImgKb, callback);
        }

        function compressImageToTarget(file, targetKb, callback) {
            const targetBytes = targetKb * 1024;
            const reader = new FileReader();

            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    let width = img.width;
                    let height = img.height;

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

        document.addEventListener('alpine:init', () => {
            Alpine.data('dropdownCpmkComponent', () => ({
                openCpmk: false,
                searchCpmk: '',
                selectedCpmk: [],
                // Default Cpmk jika belum ada di database
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

        function showLogbookLoading(event) {
            Swal.fire({
                title: 'Mengunggah Logbook Susulan...',
                html: '<p class="text-xs text-gray-600 mt-2">Mohon tunggu, berkas Anda sedang diproses oleh sistem.</p>',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            return true;
        }
    </script>
@endsection