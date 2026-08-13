@extends('layouts.dashboard')

@section('content')
<!-- CDN PDF-LIB Untuk Kompresi PDF Client-Side -->
<script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 lg:p-8 custom-scrollbar">
    
    <!-- HEADER HALAMAN -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Upload Dokumen Laporan Magang</h1>
            <p class="text-sm text-gray-500 mt-1">Unggah berkas laporan akhir, lembar pengesahan, dan dokumen kelengkapan Magang Industri Vokasi UNHAS.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                <i class="fas fa-clock mr-1.5"></i> Batas Akhir Pelaporan
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

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- KOLOM KIRI: FORM UPLOAD DOKUMEN & TABEL REPOSITORI (8 Cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6" 
                 x-data="uploadDokumenComponent()">
                
                <h2 class="text-lg font-bold text-gray-800 mb-1">Form Unggah Berkas Baru</h2>
                <p class="text-xs text-gray-500 mb-6">Pilih jenis dokumen dan lampirkan file resmi dalam format PDF atau Gambar.</p>

                <form id="uploadDokumenForm" @submit.prevent="submitForm()" class="space-y-5">
                    @csrf

                    <!-- Jenis Dokumen -->
                    <div>
                        <label for="jenis_dokumen" class="block text-sm font-semibold text-gray-700 mb-2">
                            Jenis Dokumen <span class="text-red-500">*</span>
                        </label>
                        <select id="jenis_dokumen" x-model="jenisDokumen" required class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20 focus:border-vokasi-primary transition-colors">
                            <option value="" disabled selected>-- Pilih Jenis Dokumen --</option>
                            <option value="laporan_akhir">Laporan Akhir Magang (PDF)</option>
                            <option value="lembar_pengesahan">Lembar Pengesahan Ber-TTD (PDF/Gambar)</option>
                            <option value="sertifikat_industri">Sertifikat Magang Mitra (PDF/Gambar)</option>
                            <option value="lampiran_pendukung">Dokumen Lampiran Pendukung (PDF/Gambar)</option>
                        </select>
                    </div>

                    <!-- Judul Laporan -->
                    <div>
                        <label for="judul_laporan" class="block text-sm font-semibold text-gray-700 mb-2">
                            Judul / Nama Dokumen <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="judul_laporan" x-model="judulLaporan" required placeholder="Contoh: Laporan Akhir Magang Bab 1-5 / Sertifikat PT..." class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20 focus:border-vokasi-primary transition-colors">
                    </div>

                    <!-- Keterangan Singkat -->
                    <div>
                        <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-2">
                            Catatan / Keterangan Tambahan (Opsional)
                        </label>
                        <textarea id="keterangan" x-model="keterangan" rows="2" placeholder="Catatan tambahan untuk DPL..." class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20 focus:border-vokasi-primary transition-colors resize-none"></textarea>
                    </div>

                    <!-- DRAG & DROP FILE UPLOAD AREA -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Lampiran File 
                            <span class="text-vokasi-primary font-normal text-xs ml-1">(Batas Maksimal 5 MB, Otomatis dikompres)</span>
                        </label>
                        
                        <div class="border-2 border-dashed border-gray-300 hover:border-vokasi-primary rounded-2xl p-6 text-center bg-gray-50/50 hover:bg-vokasi-primary/5 transition-all cursor-pointer relative group">
                            <!-- INPUT PEMILIH FILE (ISOLATED) -->
                            <input type="file" id="standalone_file_picker" accept=".pdf,.jpg,.jpeg,.png,.webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="onFileSelected($event)">
                            
                            <div class="space-y-2">
                                <div class="w-12 h-12 bg-vokasi-primary/10 text-vokasi-primary rounded-full flex items-center justify-center mx-auto text-xl group-hover:scale-110 transition-transform">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div class="text-sm font-semibold text-gray-700">
                                    <span class="text-vokasi-primary">Klik untuk memilih file</span> atau tarik & lepas di sini
                                </div>
                                <p class="text-xs text-gray-400">PDF, JPG, PNG, WEBP (Maksimal 5 MB)</p>
                            </div>
                        </div>

                        <!-- STATUS ANIMASI KOMPRESI -->
                        <div x-show="isCompressing" x-cloak class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-xl flex items-center gap-3 text-xs text-blue-800 font-semibold">
                            <i class="fas fa-spinner fa-spin text-vokasi-primary text-base"></i>
                            <div>
                                <p class="font-bold">Mengompresi Berkas di Browser...</p>
                                <p class="text-[10px] text-blue-600 font-normal">Memproses file di perangkat Anda sebelum dikirim ke server.</p>
                            </div>
                        </div>

                        <!-- INFORMASI HASIL KOMPRESI SUKSES -->
                        <div x-show="isCompressedReady && fileInfo" x-cloak class="mt-2 p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs text-emerald-800 font-bold">
                            <i class="fas fa-check-circle text-emerald-600 text-base"></i>
                            <span x-text="fileInfo"></span>
                        </div>

                        <!-- INFORMASI EROR -->
                        <div x-show="errorMessage" x-cloak class="mt-2 p-3 bg-red-50 border border-red-200 rounded-xl flex items-center gap-2 text-xs text-red-800 font-bold">
                            <i class="fas fa-times-circle text-red-600 text-base"></i>
                            <span x-text="errorMessage"></span>
                        </div>
                    </div>

                    <!-- Button Submit -->
                    <div class="flex justify-end pt-2">
                        <button type="submit" 
                                :disabled="isCompressing || !isCompressedReady || isSubmitting" 
                                :class="(isCompressing || !isCompressedReady || isSubmitting) ? 'opacity-60 cursor-not-allowed bg-gray-400' : 'bg-vokasi-primary hover:bg-vokasi-dark'"
                                class="px-6 py-3 text-white font-semibold text-sm rounded-xl transition-all shadow-md flex items-center gap-2">
                            <template x-if="isSubmitting">
                                <span class="flex items-center gap-2"><i class="fas fa-spinner fa-spin"></i> Mengirim Dokumen...</span>
                            </template>
                            <template x-if="!isSubmitting && isCompressedReady">
                                <span class="flex items-center gap-2"><i class="fas fa-upload"></i> Unggah Dokumen</span>
                            </template>
                            <template x-if="!isSubmitting && isCompressing">
                                <span class="flex items-center gap-2"><i class="fas fa-spinner fa-spin"></i> Sedang Mengompresi...</span>
                            </template>
                            <template x-if="!isSubmitting && !isCompressing && !isCompressedReady">
                                <span class="flex items-center gap-2"><i class="fas fa-file-upload"></i> Pilih File Dahulu</span>
                            </template>
                        </button>
                    </div>
                </form>
            </div>

            <!-- TABEL RIWAYAT DOKUMEN YANG DIUNGGAH -->
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-800">Berkas Terunggah</h2>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full">
                        Total: {{ $laporans->count() }} Dokumen
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-[11px] font-bold tracking-wider">
                                <th class="p-4 pl-6">Dokumen</th>
                                <th class="p-4">Tanggal Unggah</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right pr-6">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse($laporans as $item)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-4 pl-6">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-9 h-9 bg-red-100 text-red-600 rounded-lg flex items-center justify-center font-bold shrink-0">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800 text-xs">{{ $item->judul_laporan }}</p>
                                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $item->catatan ?? 'Tidak ada catatan' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $item->created_at->format('d M Y, H:i') }} WITA
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    @if($item->status_verifikasi === 'approved')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fas fa-check-circle mr-1 text-[10px]"></i> Disetujui
                                        </span>
                                    @elseif($item->status_verifikasi === 'revisi')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                            <i class="fas fa-undo mr-1 text-[10px]"></i> Revisi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            <i class="fas fa-hourglass-half mr-1 text-[10px]"></i> Perlu Verifikasi
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-right pr-6 space-x-2 whitespace-nowrap">
                                    <a href="{{ asset('storage/' . $item->file_laporan) }}" target="_blank" class="p-1.5 text-gray-400 hover:text-vokasi-primary transition-colors" title="Lihat/Download"><i class="fas fa-eye"></i></a>
                                    
                                    @if($item->status_verifikasi !== 'approved')
                                    <form action="{{ route('dashboard-pelaporan-upload-dokumen-destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus berkas ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 transition-colors" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-gray-400">
                                    <i class="fas fa-folder-open text-3xl mb-2 block"></i> Belum ada dokumen terunggah.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- KOLOM KANAN: CHECKLIST KELENGKAPAN (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-gray-800 border-b border-gray-100 pb-3">Checklist Persyaratan</h3>
                <ul class="space-y-3 text-xs">
                    <li class="flex items-start gap-3 text-gray-700">
                        <i class="fas fa-check-circle text-emerald-500 text-base shrink-0 mt-0.5"></i>
                        <div>
                            <p class="font-bold">Laporan Akhir Lengkap</p>
                            <p class="text-gray-400">Sesuai template standar Vokasi UNHAS.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3 text-gray-700">
                        <i class="fas fa-check-circle text-emerald-500 text-base shrink-0 mt-0.5"></i>
                        <div>
                            <p class="font-bold">Lembar Pengesahan Resmi</p>
                            <p class="text-gray-400">Wajib bertanda tangan DPL & Supervisor.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3 text-gray-700">
                        <i class="fas fa-check-circle text-emerald-500 text-base shrink-0 mt-0.5"></i>
                        <div>
                            <p class="font-bold">Sertifikat Magang Industri</p>
                            <p class="text-gray-400">Dikeluarkan oleh pihak mitra/perusahaan.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

    </div>

</main>

<script>
    function uploadDokumenComponent() {
        return {
            jenisDokumen: '',
            judulLaporan: '',
            keterangan: '',
            compressedBlobFile: null,
            isCompressing: false,
            isCompressedReady: false,
            isSubmitting: false,
            fileInfo: '',
            errorMessage: '',

            onFileSelected(event) {
                const picker = event.target;
                const file = picker.files[0];
                if (!file) return;

                // Batas file awal sebelum kompresi: 10MB
                if (file.size > 10 * 1024 * 1024) {
                    alert('Ukuran file awal melebihi batas maksimal 10 MB!');
                    picker.value = '';
                    return;
                }

                this.isCompressing = true;
                this.isCompressedReady = false;
                this.errorMessage = '';
                this.fileInfo = '';
                this.compressedBlobFile = null;

                const self = this;

                // Kompresi Berkas Offline di Browser
                compressDokumenOffline(file, function(compressedFile, err) {
                    if (err) {
                        self.isCompressing = false;
                        self.isCompressedReady = false;
                        self.errorMessage = err;
                        picker.value = '';
                        return;
                    }

                    // Proteksi ukuran setelah kompresi: maksimal 5 MB (5120 KB)
                    if (compressedFile.size > 5120 * 1024) {
                        self.isCompressing = false;
                        self.isCompressedReady = false;
                        self.errorMessage = 'Ukuran file PDF setelah kompresi masih melebihi 5 MB (' + (compressedFile.size / (1024 * 1024)).toFixed(2) + ' MB). Harap gunakan PDF Compressor online untuk memperkecil ukuran PDF Anda.';
                        picker.value = '';
                        return;
                    }

                    self.compressedBlobFile = compressedFile;
                    const origStr = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                    const newStr = compressedFile.size >= 1024 * 1024 
                        ? (compressedFile.size / (1024 * 1024)).toFixed(2) + ' MB' 
                        : (compressedFile.size / 1024).toFixed(0) + ' KB';

                    self.fileInfo = `Berhasil Dikompresi di Browser: ${origStr} → ${newStr}`;
                    self.isCompressing = false;
                    self.isCompressedReady = true;
                });
            },

            submitForm() {
                if (!this.compressedBlobFile || !this.isCompressedReady) {
                    alert('File belum selesai dikompresi!');
                    return;
                }

                this.isSubmitting = true;

                // SUSUN FORMDATA DENGAN FILE TERKOMPRES (<5MB)
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('jenis_dokumen', this.jenisDokumen);
                formData.append('judul_laporan', this.judulLaporan);
                formData.append('keterangan', this.keterangan || '');
                formData.append('file_dokumen', this.compressedBlobFile, this.compressedBlobFile.name);

                fetch('{{ route("dashboard-pelaporan-upload-dokumen-store") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(async response => {
                    const contentType = response.headers.get('content-type');
                    
                    if (!response.ok) {
                        if (response.status === 413) {
                            throw new Error('Ukuran berkas terlalu besar untuk server (413 Payload Too Large).');
                        }
                        if (contentType && contentType.includes('application/json')) {
                            const errData = await response.json();
                            throw new Error(errData.message || 'Gagal mengunggah berkas.');
                        } else {
                            throw new Error('Terjadi kesalahan pada server (Status ' + response.status + ').');
                        }
                    }

                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    }
                    return { status: 'success' };
                })
                .then(data => {
                    window.location.reload();
                })
                .catch(err => {
                    this.isSubmitting = false;
                    alert('Gagal Mengunggah Berkas: ' + err.message);
                });
            }
        };
    }

    // FUNCTION ENGINE KOMPRESI OFFLINE
    async function compressDokumenOffline(file, callback) {
        const isImage = file.type.startsWith('image/');
        const isPdf = file.type === 'application/pdf';

        if (isImage) {
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

                    canvas.toBlob(function(blob) {
                        if (!blob) {
                            callback(null, 'Gagal mengompresi gambar.');
                            return;
                        }
                        const compressedFile = new File([blob], file.name, {
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        });
                        callback(compressedFile, null);
                    }, 'image/jpeg', 0.65);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else if (isPdf) {
            try {
                const arrayBuffer = await file.arrayBuffer();
                const pdfDoc = await PDFLib.PDFDocument.load(arrayBuffer, { ignoreEncryption: true });

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

                callback(compressedFile, null);
            } catch (err) {
                console.error('Error Kompresi PDF:', err);
                callback(null, 'Gagal mengompresi PDF di browser.');
            }
        } else {
            callback(file, null);
        }
    }
</script>
@endsection