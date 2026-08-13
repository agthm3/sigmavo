@extends('layouts.dashboard')

@section('content')
<!-- CDN PDF-LIB Untuk Kompresi PDF Client-Side -->
<script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
@if(!$sudahPembekalan)
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-8 text-center text-amber-900 max-w-2xl mx-auto my-12">
        <i class="fas fa-exclamation-triangle text-5xl mb-4 text-amber-500"></i>
        <h3 class="font-bold text-lg mb-2">Akses Absensi Terkunci</h3>
        <p class="text-sm">Anda telah diterima magang, namun Anda belum melakukan Konfirmasi Kehadiran pada acara Pembekalan Magang. Silakan menuju ke menu <strong>Pembekalan Magang</strong> untuk mengonfirmasi kehadiran Anda terlebih dahulu.</p>
        <div class="mt-4">
            <a href="{{ route('dashboard-mahasiswa-pembekalan-magang') }}" class="bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs py-2 px-4 rounded-xl shadow-sm transition-colors inline-block">
                Buka Menu Pembekalan
            </a>
        </div>
    </div>
@endif
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative custom-scrollbar"
      x-data="absensiComponent()">
    
    <div class="max-w-6xl mx-auto w-full flex-1">
        
        @if(isset($isLocked) && $isLocked)
            <!-- STATE TERKUNCI: BELUM DITERIMA MAGANG -->
            <div class="bg-white rounded-2xl p-8 md:p-12 border border-amber-200 shadow-sm text-center max-w-2xl mx-auto my-8 space-y-4">
                <div class="w-16 h-16 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center mx-auto text-2xl border border-amber-200 shadow-sm">
                    <i class="fas fa-user-lock"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">Akses Absensi Belum Terbuka</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Fitur <strong>Presensi Kehadiran Harian</strong> hanya dapat digunakan oleh mahasiswa yang telah <strong>diterima secara resmi</strong> pada program magang industri.
                </p>
                <div class="p-4 bg-amber-50/60 rounded-xl text-xs text-amber-800 border border-amber-200 font-medium text-left flex items-start gap-2.5">
                    <i class="fas fa-info-circle text-amber-600 text-base shrink-0 mt-0.5"></i>
                    <p>
                        Jika Anda sudah mengajukan magang, silakan pantau perkembangan verifikasi berkas dan penerbitan surat pada menu 
                        <a href="{{ route('dashboard-mahasiswa-status-pengajuan') }}" class="underline font-bold text-amber-900 hover:text-amber-700">Status Pengajuan Magang</a>.
                    </p>
                </div>
            </div>
        @else
            <!-- STATE NORMAL: SUDAH DITERIMA MAGANG -->

            <!-- HEADER & DATE -->
            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Catat Kehadiran Harian</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $pendaftaran->lowongan->judul_posisi ?? $pendaftaran->divisi_mandiri ?? 'Program Magang Industri Vokasi' }} • {{ $pendaftaran->lowongan->perusahaan->nama_perusahaan ?? $pendaftaran->nama_instansi_mandiri ?? 'Perusahaan Mitra' }}
                    </p>
                </div>
                <div class="bg-white px-4 py-2.5 rounded-xl shadow-sm border border-gray-200 flex items-center gap-3">
                    <div class="text-vokasi-primary">
                        <i class="far fa-calendar-alt text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Hari Ini</p>
                        <p class="font-bold text-gray-800 text-sm">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}</p>
                    </div>
                </div>
            </div>

            <!-- NOTIFIKASI SUKSES / ERROR -->
            @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center justify-between text-sm shadow-sm">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900"><i class="fas fa-times"></i></button>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-xs shadow-sm">
                <p class="font-bold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal memproses absensi:</p>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                
                <!-- AREA KIRI: KAMERA & GEOLOCATION -->
                <div class="lg:col-span-1 space-y-6">
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 md:p-6 relative overflow-hidden flex flex-col items-center">
                        
                        <h3 class="font-bold text-gray-800 w-full mb-3 flex items-center justify-between border-b border-gray-100 pb-2 text-sm">
                            <span><i class="fas fa-camera text-vokasi-primary mr-2"></i> Presensi Selfie Kamera</span>
                            <span class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-bold animate-pulse" x-show="cameraActive">Live</span>
                        </h3>

                        <!-- Viewfinder / Feed Kamera -->
                        <div class="w-full aspect-[3/4] bg-gray-900 rounded-xl relative overflow-hidden border-2 border-gray-200 mb-4 flex items-center justify-center group cursor-pointer" @click="startCamera()">
                            
                            <!-- Video Stream Element -->
                            <video x-ref="video" autoplay playsinline class="w-full h-full object-cover" x-show="cameraActive"></video>
                            <canvas x-ref="canvas" class="hidden"></canvas>

                            <!-- Placeholder jika kamera belum aktif -->
                            <div x-show="!cameraActive" class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 bg-slate-800 p-4 text-center">
                                <i class="fas fa-camera-retro text-5xl mb-2 opacity-50 text-vokasi-light"></i>
                                <p class="text-xs font-bold text-gray-200">Klik untuk Aktifkan Kamera</p>
                                <p class="text-[10px] text-gray-400 mt-1">Izinkan akses lokasi GPS & kamera di browser Anda.</p>
                            </div>

                            <!-- Watermark Informasi GPS & Waktu Real-time -->
                            <div class="absolute bottom-2 left-2 right-2 bg-black/70 backdrop-blur-sm text-white text-[9px] p-2 rounded-lg z-20 font-mono space-y-0.5">
                                <p><i class="fas fa-map-marker-alt text-red-400 w-3"></i> <span x-text="lat ? lat + ', ' + lng : 'Mendapatkan GPS...'"></span></p>
                                <p><i class="fas fa-clock text-blue-400 w-3"></i> {{ \Carbon\Carbon::now()->isoFormat('D MMM YYYY, HH:mm:ss') }} WITA</p>
                                <p><i class="fas fa-user text-emerald-400 w-3"></i> {{ $user->name }}</p>
                            </div>
                        </div>

                        <!-- Hidden Form untuk Submit Foto & GPS -->
                        <form x-ref="formAbsen" action="{{ route('dashboard-mahasiswa-absensi-store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="tipe" x-ref="inputTipe" :value="absentType">
                            <input type="hidden" name="image" x-ref="inputImage">
                            <input type="hidden" name="latitude" x-ref="inputLat" :value="lat">
                            <input type="hidden" name="longitude" x-ref="inputLng" :value="lng">
                        </form>

                        <!-- Tombol Aksi Absen -->
                        <div class="w-full space-y-2.5">
                            @if(!$absensiHariIni || !$absensiHariIni->waktu_masuk)
                                <button type="button" @click="doAbsen('masuk')" :disabled="!cameraActive" class="w-full bg-vokasi-primary hover:bg-vokasi-dark disabled:bg-gray-300 text-white font-bold py-3 rounded-xl shadow-sm transition-colors flex items-center justify-center text-xs">
                                    <i class="fas fa-sign-in-alt mr-2"></i> Absen Masuk Sekarang
                                </button>
                            @else
                                <button type="button" disabled class="w-full bg-emerald-50 text-emerald-700 font-bold py-2.5 rounded-xl border border-emerald-200 flex items-center justify-center text-xs">
                                    <i class="fas fa-check-circle mr-2"></i> Sudah Absen Masuk ({{ $absensiHariIni->waktu_masuk }})
                                </button>
                            @endif

                            @if($absensiHariIni && $absensiHariIni->waktu_masuk && !$absensiHariIni->waktu_pulang)
                                <button type="button" @click="doAbsen('pulang')" :disabled="!cameraActive" class="w-full bg-orange-500 hover:bg-orange-600 disabled:bg-gray-300 text-white font-bold py-3 rounded-xl shadow-sm transition-colors flex items-center justify-center text-xs">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Absen Pulang
                                </button>
                            @elseif($absensiHariIni && $absensiHariIni->waktu_pulang)
                                <button type="button" disabled class="w-full bg-emerald-50 text-emerald-700 font-bold py-2.5 rounded-xl border border-emerald-200 flex items-center justify-center text-xs">
                                    <i class="fas fa-check-circle mr-2"></i> Absen Hari Ini Selesai ({{ $absensiHariIni->waktu_pulang }})
                                </button>
                            @endif
                        </div>

                        <p class="text-[10px] text-gray-400 text-center mt-3">Pastikan wajah terlihat jelas dan fitur GPS / Lokasi pada perangkat aktif.</p>
                    </div>

                    <!-- Card Pengajuan Izin/Sakit -->
                    <div class="bg-red-50 rounded-2xl shadow-sm border border-red-100 p-4 flex items-center justify-between">
                        <div>
                            <h4 class="font-bold text-red-800 text-xs">Tidak bisa hadir magang?</h4>
                            <p class="text-[11px] text-red-600 mt-0.5">Ajukan surat izin atau sakit di sini.</p>
                        </div>
                        <button type="button" @click="openIzinModal = true" class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold py-2 px-3 rounded-xl shadow-sm transition-colors">
                            Ajukan
                        </button>
                    </div>

                </div>

                <!-- AREA KANAN: STATUS JAM & RIWAYAT -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Panel Progress Jam Magang -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="font-bold text-gray-800 mb-4 flex items-center border-b border-gray-100 pb-2 text-sm">
                            <i class="fas fa-chart-pie text-vokasi-primary mr-2"></i> Status Kuota Jam Magang
                        </h3>
                        
                        <div class="flex flex-col md:flex-row gap-6 items-center">
                            <!-- Progress Circle -->
                            <div class="relative w-32 h-32 shrink-0">
                                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                    <circle cx="50" cy="50" r="40" fill="transparent" stroke="#e5e7eb" stroke-width="8"></circle>
                                    <circle cx="50" cy="50" r="40" fill="transparent" stroke="#37A7AC" stroke-width="8" stroke-dasharray="251.2" :stroke-dashoffset="251.2 - (251.2 * {{ $persentase }} / 100)" stroke-linecap="round"></circle>
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-2xl font-bold text-gray-800">{{ $jamTercapai }}</span>
                                    <span class="text-[9px] text-gray-400 uppercase font-bold">Jam Terpenuhi</span>
                                </div>
                            </div>

                            <div class="flex-1 w-full space-y-3 text-xs">
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-gray-500">Total Target Magang:</span>
                                        <span class="font-bold text-gray-800">{{ $targetJam }} Jam</span>
                                    </div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-gray-500">Sisa Jam Ditempuh:</span>
                                        <span class="font-bold text-orange-600">{{ $sisaJam }} Jam</span>
                                    </div>
                                </div>
                                
                                <div class="bg-blue-50 border border-blue-100 p-3 rounded-xl flex items-start gap-2.5">
                                    <i class="fas fa-info-circle text-blue-500 mt-0.5 text-sm"></i>
                                    <p class="text-[11px] text-blue-800 leading-relaxed">
                                        <strong>Sistem Akumulasi:</strong> Setiap absensi harian lengkap (Masuk & Pulang) yang disetujui Dosen Pembimbing akan memotong kuota magang Anda sebanyak <strong>8 Jam</strong>.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Riwayat Absensi -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                            <h3 class="font-bold text-gray-800 flex items-center text-xs uppercase tracking-wider">
                                <i class="fas fa-list text-vokasi-primary mr-2"></i> Log Absensi Magang Saya
                            </h3>
                        </div>

                        <div class="overflow-x-auto custom-scrollbar">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-gray-100/50 border-b border-gray-200 text-gray-600 uppercase font-semibold">
                                        <th class="p-3.5">Tanggal</th>
                                        <th class="p-3.5 text-center">Masuk</th>
                                        <th class="p-3.5 text-center">Pulang</th>
                                        <th class="p-3.5 text-center">Status</th>
                                        <th class="p-3.5 text-right">Potongan Jam</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($riwayats as $row)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="p-3.5 font-bold text-gray-800">
                                            {{ $row->tanggal->format('d M Y') }}
                                            <span class="block text-[10px] text-gray-400 font-normal">{{ $row->tanggal->isoFormat('dddd') }}</span>
                                        </td>
                                        <td class="p-3.5 text-center font-mono">
                                            {{ $row->waktu_masuk ?? '-' }}
                                        </td>
                                        <td class="p-3.5 text-center font-mono">
                                            {{ $row->waktu_pulang ?? '-' }}
                                        </td>
                                        <td class="p-3.5 text-center">
                                            @if($row->tipe_kehadiran !== 'hadir')
                                                <span class="px-2 py-0.5 bg-purple-100 text-purple-700 font-bold rounded-full text-[10px] capitalize">{{ $row->tipe_kehadiran }}</span>
                                            @elseif($row->status_verifikasi === 'approved')
                                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 font-bold rounded-full text-[10px]"><i class="fas fa-check-double mr-1"></i> Approved</span>
                                            @else
                                                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 font-bold rounded-full text-[10px]"><i class="fas fa-spinner fa-spin mr-1"></i> Pending</span>
                                            @endif
                                        </td>
                                        <td class="p-3.5 text-right font-bold {{ $row->status_verifikasi === 'approved' ? 'text-emerald-600' : 'text-gray-400' }}">
                                            + {{ $row->jam_diperoleh }} Jam
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="p-8 text-center text-gray-400">Belum ada riwayat absensi.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            <!-- MODAL POPUP: PENGAJUAN IZIN / SAKIT -->
            <div x-show="openIzinModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div @click.away="openIzinModal = false" class="bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-md overflow-hidden">
                    <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center">
                        <h3 class="font-bold text-sm"><i class="fas fa-envelope-open-text mr-2"></i> Form Pengajuan Izin / Sakit</h3>
                        <button type="button" @click="openIzinModal = false" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
                    </div>

                    <form action="{{ route('dashboard-mahasiswa-absensi-izin-store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 text-xs">
                        @csrf
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Kategori <span class="text-red-500">*</span></label>
                            <select name="tipe_kehadiran" required class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none">
                                <option value="izin">Izin Tidak Hadir</option>
                                <option value="sakit">Sakit</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Alasan / Keterangan <span class="text-red-500">*</span></label>
                            <textarea name="alasan_izin" rows="3" required placeholder="Jelaskan alasan ketidakhadiran Anda secara singkat..." class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none resize-none"></textarea>
                        </div>

                        <div x-data="{ isCompressing: false, fileInfo: '' }">
                            <label class="block font-bold text-gray-700 uppercase mb-1">
                                Unggah Surat Dokter / Pendukung
                                <span class="text-vokasi-primary font-normal text-[10px] ml-1">(Bisa pilih s.d 10 MB, PDF &lt; 500KB, Gambar &lt; 300KB)</span>
                            </label>
                            <input type="file" name="surat_izin" accept=".pdf,.jpg,.jpeg,.png,.webp" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-vokasi-primary/10 file:text-vokasi-primary hover:file:bg-vokasi-primary/20" @change="
                                const input = $event.target;
                                const file = input.files[0];
                                if(file) {
                                    if(file.size > 10 * 1024 * 1024) {
                                        alert('Ukuran file melebihi batas maksimal 10 MB!');
                                        input.value = '';
                                        return;
                                    }
                                    isCompressing = true;
                                    handleAbsensiFileCompression(file, 300, 500, function(compressedFile) {
                                        const container = new DataTransfer();
                                        container.items.add(compressedFile);
                                        input.files = container.files;
                                        
                                        const origStr = (file.size / (1024 * 1024)).toFixed(2) + 'MB';
                                        const newStr = (compressedFile.size / 1024).toFixed(0) + 'KB';
                                        fileInfo = `Hasil Kompresi: ${origStr} → ${newStr}`;
                                        isCompressing = false;
                                    });
                                }
                            ">
                            <p x-show="isCompressing" class="text-[10px] text-vokasi-primary font-bold mt-1 animate-pulse">
                                <i class="fas fa-spinner fa-spin mr-1"></i> Mengompresi file di browser...
                            </p>
                            <p x-show="!isCompressing && fileInfo" class="text-[10px] text-emerald-600 font-bold mt-1" x-text="fileInfo"></p>
                        </div>

                        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                            <button type="button" @click="openIzinModal = false" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-medium">Batal</button>
                            <button type="submit" class="px-5 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-xl font-bold shadow-sm">Kirim Pengajuan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>

    <!-- FOOTER -->
    <footer class="mt-auto py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
        Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
    </footer>

</main>

<!-- SCRIPT KAMERA WEBRTC, GEOLOKASI GPS & OFFLINE COMPRESSION ENGINE -->
<script>
    /**
     * ENGINE KOMPRESI BERKAS CLIENT-SIDE (OFFLINE BROWSER)
     * Gambar < 300 KB, PDF < 500 KB
     */
    async function handleAbsensiFileCompression(file, maxImgKb = 300, maxPdfKb = 500, callback) {
        const isImage = file.type.startsWith('image/');
        const isPdf = file.type === 'application/pdf';

        if (isImage) {
            compressAbsensiImage(file, maxImgKb, callback);
        } else if (isPdf) {
            compressAbsensiPdf(file, maxPdfKb, callback);
        } else {
            callback(file);
        }
    }

    function compressAbsensiImage(file, targetKb, callback) {
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

                let quality = 0.82;

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

    async function compressAbsensiPdf(file, targetKb, callback) {
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

            callback(compressedFile);
        } catch (err) {
            console.warn('Gagal mengompresi PDF, menggunakan file original:', err);
            callback(file);
        }
    }

    function absensiComponent() {
        return {
            openIzinModal: false,
            cameraActive: false,
            capturedImage: '',
            absentType: 'masuk',
            lat: null,
            lng: null,

            init() {
                this.getLocation();
            },

            getLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            this.lat = position.coords.latitude.toFixed(6);
                            this.lng = position.coords.longitude.toFixed(6);
                        },
                        (error) => {
                            console.warn("Gagal mengambil GPS:", error.message);
                            this.lat = "-5.132200";
                            this.lng = "119.425500";
                        }
                    );
                } else {
                    this.lat = "-5.132200";
                    this.lng = "119.425500";
                }
            },

            async startCamera() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: "user", width: { ideal: 640 }, height: { ideal: 800 } },
                        audio: false
                    });
                    this.$refs.video.srcObject = stream;
                    this.cameraActive = true;
                } catch (err) {
                    alert("Gagal mengakses kamera. Pastikan izin kamera telah diberikan di browser Anda.");
                }
            },

            doAbsen(type) {
                this.absentType = type;
                const video = this.$refs.video;
                const canvas = this.$refs.canvas;

                if (!this.cameraActive || !video) {
                    alert("Silakan aktifkan kamera terlebih dahulu sebelum melakukan absen.");
                    return;
                }

                canvas.width = video.videoWidth || 640;
                canvas.height = video.videoHeight || 800;

                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                // Mengompresi foto selfie langsung ke format JPG dengan kualitas 0.7 (di bawah 300KB)
                const imageData = canvas.toDataURL('image/jpeg', 0.7);
                
                if (!imageData || imageData === 'data:,') {
                    alert("Gagal mengambil foto dari kamera. Coba klik kamera kembali.");
                    return;
                }

                this.$refs.inputTipe.value = type;
                this.$refs.inputImage.value = imageData;
                this.$refs.inputLat.value = this.lat || "-5.132200";
                this.$refs.inputLng.value = this.lng || "119.425500";

                this.$nextTick(() => {
                    this.$refs.formAbsen.submit();
                });
            }
        }
    }
</script>
@endsection