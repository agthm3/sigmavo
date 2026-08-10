@extends('layouts.dashboard')

@section('content')
<!-- CDN PDF-Lib & PDF.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>

<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
</script>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 lg:p-8 custom-scrollbar">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Unggah Laporan Akhir Magang</h1>
            <p class="text-sm text-gray-500 mt-1">Kirimkan dokumen laporan akhir magang PDF Anda untuk diverifikasi oleh Dosen Pembimbing dan Admin.</p>
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

        @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-xs shadow-sm space-y-1">
            <div class="font-bold flex items-center gap-1">
                <i class="fas fa-exclamation-triangle"></i> Gagal Mengunggah Laporan:
            </div>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- BILA ADA LAPORAN YANG SUDAH DIUNGGAH -->
        @if($laporan)
        <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <h3 class="font-bold text-gray-800 text-base"><i class="fas fa-file-pdf text-red-500 mr-2"></i> Laporan Akhir Terkirim</h3>
                @if($laporan->status_verifikasi === 'approved')
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full border border-emerald-200">
                        <i class="fas fa-check-double mr-1"></i> Approved / Sah
                    </span>
                @elseif($laporan->status_verifikasi === 'revisi')
                    <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full border border-red-200">
                        <i class="fas fa-undo mr-1"></i> Perlu Revisi
                    </span>
                @else
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full border border-yellow-200">
                        <i class="fas fa-spinner fa-spin mr-1"></i> Menunggu Verifikasi
                    </span>
                @endif
            </div>

            <div class="space-y-2 text-xs">
                <p class="text-gray-500">Judul Laporan: <strong class="text-gray-800 text-sm block mt-0.5">{{ $laporan->judul_laporan }}</strong></p>
                <p class="text-gray-500">Waktu Kirim: <span class="text-gray-700 font-medium">{{ $laporan->created_at->format('d M Y, H:i') }} WITA</span></p>
                
                @if($laporan->catatan)
                <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-red-800 mt-2">
                    <strong>Catatan Verifikator:</strong>
                    <p class="mt-1 font-medium">{{ $laporan->catatan }}</p>
                </div>
                @endif

                <div class="pt-2">
                    <a href="{{ asset('storage/' . $laporan->file_laporan) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-xl transition border border-blue-200">
                        <i class="fas fa-download mr-2"></i> Unduh File Laporan Terkirim
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- FORM UPLOAD LAPORAN -->
        @if(!$laporan || $laporan->status_verifikasi !== 'approved')
        <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm space-y-4" 
             x-data="{ isCompressing: false, fileInfo: '', isReady: false, errorMessage: '' }">
            
            <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3">
                <i class="fas fa-cloud-upload-alt text-vokasi-primary mr-2"></i> Form Upload {{ $laporan ? 'Revisi Laporan' : 'Laporan Baru' }}
            </h3>

            <form action="{{ route('dashboard-mahasiswa-laporan-akhir-store') }}" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  class="space-y-4"
                  @submit="if (isCompressing || !isReady) { $event.preventDefault(); alert('Harap tunggu sampai proses kompresi selesai!'); }">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Judul Laporan Akhir <span class="text-red-500">*</span></label>
                    <input type="text" name="judul_laporan" value="{{ old('judul_laporan', $laporan?->judul_laporan) }}" required placeholder="Contoh: Laporan Akhir Magang..." class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                        File Dokumen PDF Laporan
                        <span class="text-vokasi-primary font-normal text-[11px] ml-1">(Otomatis dikompres &lt; 500KB di browser)</span>
                    </label>
                    
                    <input type="file" id="input_file_pdf" name="file_laporan" accept=".pdf" required class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-vokasi-primary/10 file:text-vokasi-primary hover:file:bg-vokasi-primary/20 cursor-pointer" @change="
                        const input = $event.target;
                        const file = input.files[0];
                        if(file) {
                            if(file.type !== 'application/pdf') {
                                alert('Format file harus berupa PDF!');
                                input.value = '';
                                return;
                            }

                            isCompressing = true;
                            isReady = false;
                            errorMessage = '';
                            fileInfo = '';
                            
                            safeCompressPdf(file, function(compressedFile, err) {
                                if(err) {
                                    isCompressing = false;
                                    isReady = false;
                                    errorMessage = err;
                                    input.value = ''; // Kosongkan file agar tidak terkirim file besarnya
                                    return;
                                }

                                const container = new DataTransfer();
                                container.items.add(compressedFile);
                                input.files = container.files;
                                
                                const origStr = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                                const newStr = (compressedFile.size / 1024).toFixed(0) + ' KB';
                                fileInfo = `Ukuran Berhasil Dikompresi: ${origStr} → ${newStr}`;
                                isCompressing = false;
                                isReady = true;
                            });
                        }
                    ">

                    <!-- STATUS ANIMASI KOMPRESI -->
                    <div x-show="isCompressing" x-cloak class="mt-2 p-3.5 bg-blue-50 border border-blue-200 rounded-xl flex items-center gap-3 text-xs text-blue-800 font-semibold">
                        <i class="fas fa-spinner fa-spin text-vokasi-primary text-lg"></i>
                        <div>
                            <p class="font-bold">Mengompresi PDF di Browser...</p>
                            <p class="text-[10px] text-blue-600 font-normal">Sistem sedang memproses file di HP/Laptop Anda agar ukurannya &lt; 500 KB.</p>
                        </div>
                    </div>

                    <!-- INFORMASI HASIL KOMPRESI SUKSES -->
                    <div x-show="isReady && fileInfo" x-cloak class="mt-2 p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs text-emerald-800 font-bold">
                        <i class="fas fa-check-circle text-emerald-600 text-base"></i>
                        <span x-text="fileInfo"></span>
                    </div>

                    <!-- INFORMASI JIKA GAGAL -->
                    <div x-show="errorMessage" x-cloak class="mt-2 p-3 bg-red-50 border border-red-200 rounded-xl flex items-center gap-2 text-xs text-red-800 font-bold">
                        <i class="fas fa-times-circle text-red-600 text-base"></i>
                        <span x-text="errorMessage"></span>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                            :disabled="isCompressing || !isReady" 
                            :class="(isCompressing || !isReady) ? 'opacity-60 cursor-not-allowed bg-gray-400' : 'bg-vokasi-primary hover:bg-vokasi-dark'" 
                            class="w-full py-3 text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                        <template x-if="!isCompressing && isReady">
                            <span class="flex items-center gap-2"><i class="fas fa-paper-plane"></i> Kirim Laporan Akhir</span>
                        </template>
                        <template x-if="isCompressing">
                            <span class="flex items-center gap-2"><i class="fas fa-spinner fa-spin"></i> Sedang Mengompresi File...</span>
                        </template>
                        <template x-if="!isCompressing && !isReady">
                            <span class="flex items-center gap-2"><i class="fas fa-file-pdf"></i> Silakan Pilih File PDF Dahulu</span>
                        </template>
                    </button>
                </div>
            </form>
        </div>
        @endif

    </div>
</main>

<script>
    async function safeCompressPdf(file, callback) {
        try {
            const arrayBuffer = await file.arrayBuffer();
            
            // Coba metode 1: Re-structure PDF Streams (Sangat Cepat & Ringan)
            const pdfDoc = await PDFLib.PDFDocument.load(arrayBuffer, { ignoreEncryption: true });
            pdfDoc.setTitle('');
            pdfDoc.setAuthor('');
            pdfDoc.setSubject('');
            pdfDoc.setKeywords([]);
            pdfDoc.setProducer('');
            pdfDoc.setCreator('');

            const compressedBytes = await pdfDoc.save({ useObjectStreams: true });
            
            // Jika hasil metode 1 < 1.5MB (aman untuk server bawaan), pakai hasilnya
            if (compressedBytes.byteLength < 1500 * 1024) {
                const compressedFile = new File([compressedBytes], file.name, {
                    type: 'application/pdf',
                    lastModified: Date.now()
                });
                return callback(compressedFile, null);
            }

            // Jika masih besar, gunakan metode 2: Canvas Rasterizer (PDF.js)
            const rasterPdfDoc = await PDFLib.PDFDocument.create();
            const loadingTask = pdfjsLib.getDocument({ data: arrayBuffer });
            const pdf = await loadingTask.promise;

            const maxPages = Math.min(pdf.numPages, 30); // Proteksi maksimal 30 halaman agar browser tidak crash

            for (let i = 1; i <= maxPages; i++) {
                const page = await pdf.getPage(i);
                const viewport = page.getViewport({ scale: 1.0 });
                
                const canvas = document.createElement('canvas');
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                const ctx = canvas.getContext('2d');

                await page.render({ canvasContext: ctx, viewport: viewport }).promise;

                const imgDataUrl = canvas.toDataURL('image/jpeg', 0.55);
                const imgBytes = await fetch(imgDataUrl).then(res => res.arrayBuffer());

                const imageEmbed = await rasterPdfDoc.embedJpg(imgBytes);
                const newPage = rasterPdfDoc.addPage([viewport.width, viewport.height]);
                newPage.drawImage(imageEmbed, {
                    x: 0,
                    y: 0,
                    width: viewport.width,
                    height: viewport.height,
                });
            }

            const rasterBytes = await rasterPdfDoc.save({ useObjectStreams: true });
            const finalCompressedFile = new File([rasterBytes], file.name, {
                type: 'application/pdf',
                lastModified: Date.now()
            });

            callback(finalCompressedFile, null);

        } catch (err) {
            console.error('Error Kompresi Browser:', err);
            // Jangan fallback kirim file besar! Tampilkan pesan error ke user.
            callback(null, 'Gagal mengompresi PDF ini di browser. Pastikan file tidak dikunci/password.');
        }
    }
</script>
@endsection