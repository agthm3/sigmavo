@extends('layouts.dashboard')

@section('content')
<!-- CDN PDF-LIB Untuk Kompresi PDF Client-Side -->
<script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>

<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative custom-scrollbar"
      x-data="ajukanMandiriComponent()">
    
    <div class="max-w-4xl mx-auto w-full flex-1">
        
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Formulir Pengajuan Magang Mandiri</h2>
            <p class="text-sm text-gray-500 mt-1">Isi formulir ini jika Anda telah diterima atau berencana magang di instansi/perusahaan yang tidak terdaftar di portal.</p>
        </div>

        <!-- NOTIFIKASI SUKSES / ERROR -->
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
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-xs shadow-sm space-y-1">
            <div class="font-bold flex items-center gap-1">
                <i class="fas fa-exclamation-triangle"></i> Gagal Mengirim Pengajuan Mandiri:
            </div>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($pendaftaranAktif)
            <!-- ALERT JIKA SUDAH MEMILIKI PENGAJUAN AKTIF -->
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-amber-900 space-y-3 mb-6 shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-amber-600 text-2xl"></i>
                    <h4 class="font-bold text-base">Pengajuan Magang Masih Aktif</h4>
                </div>
                <p class="text-xs leading-relaxed text-amber-800">
                    Anda saat ini sudah memiliki pendaftaran magang aktif yang sedang dalam proses verifikasi atau diterima. Anda tidak dapat membuat pengajuan baru hingga proses selesai.
                </p>
                <div class="pt-2">
                    <a href="{{ route('dashboard-mahasiswa-status-pengajuan') }}" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl transition shadow-sm">
                        <i class="fas fa-file-alt mr-2"></i> Lihat Status Pengajuan Saya
                    </a>
                </div>
            </div>
        @else

            <!-- Alert Information -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-2xl shadow-sm mb-6 flex items-start">
                <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-3 text-lg shrink-0"></i>
                <div class="text-xs">
                    <h4 class="font-bold text-blue-800">Informasi Penting Pengajuan Mandiri</h4>
                    <p class="text-blue-700 mt-1 leading-relaxed">
                        Pengajuan mandiri akan diverifikasi oleh Admin Prodi & Fakultas. Pastikan instansi dan deskripsi pekerjaan sesuai dengan kompetensi program studi Anda. <b>Data Supervisor Lapangan wajib diisi dengan benar</b> agar sistem dapat mengirimkan instruksi penilaian magang.
                    </p>
                </div>
            </div>

            <form id="formAjukanMandiri" @submit.prevent="submitForm()" class="space-y-6">
                @csrf
                
                <!-- SECTION 1: Informasi Instansi -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6">
                    <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center border-b border-gray-100 pb-3">
                        <span class="w-6 h-6 rounded-full bg-vokasi-primary text-white text-xs flex items-center justify-center mr-2 font-bold">1</span>
                        Informasi Instansi / Perusahaan
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                        <div class="md:col-span-2">
                            <label class="block font-bold text-gray-700 uppercase mb-1">Nama Instansi/Perusahaan <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.nama_instansi" placeholder="Contoh: PT. Inovasi Teknologi Nusantara" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20 focus:border-vokasi-primary transition-colors text-sm" required>
                        </div>
                        
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Sektor / Bidang Industri <span class="text-red-500">*</span></label>
                            <select x-model="formData.sektor_industri" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20 focus:border-vokasi-primary transition-colors text-sm" required>
                                <option value="" disabled selected>Pilih Bidang Industri</option>
                                <option value="Teknologi Informasi">Teknologi Informasi / Software</option>
                                <option value="Pertanian">Pertanian / Agrikultur</option>
                                <option value="Manufaktur">Manufaktur / Industri</option>
                                <option value="Pemerintahan">Instansi Pemerintah</option>
                                <option value="Perbankan">Perbankan / Keuangan</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Website Instansi (Opsional)</label>
                            <input type="url" x-model="formData.website_instansi" placeholder="https://www.example.com" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20 focus:border-vokasi-primary transition-colors text-sm">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block font-bold text-gray-700 uppercase mb-1">Alamat Lengkap Instansi <span class="text-red-500">*</span></label>
                            <textarea x-model="formData.alamat_instansi" rows="2" placeholder="Jl. Perintis Kemerdekaan KM..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20 focus:border-vokasi-primary transition-colors resize-none text-sm" required></textarea>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: Detail Magang -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6">
                    <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center border-b border-gray-100 pb-3">
                        <span class="w-6 h-6 rounded-full bg-vokasi-primary text-white text-xs flex items-center justify-center mr-2 font-bold">2</span>
                        Detail Rencana Magang
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                        <div class="md:col-span-2">
                            <label class="block font-bold text-gray-700 uppercase mb-1">Posisi / Departemen <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.posisi" placeholder="Contoh: Staf IT Analyst / Departemen Produksi" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20 focus:border-vokasi-primary transition-colors text-sm" required>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Rencana Tanggal Mulai <span class="text-red-500">*</span></label>
                            <input type="date" x-model="formData.tanggal_mulai" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20 focus:border-vokasi-primary transition-colors text-sm" required>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Rencana Tanggal Selesai <span class="text-red-500">*</span></label>
                            <input type="date" x-model="formData.tanggal_selesai" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20 focus:border-vokasi-primary transition-colors text-sm" required>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block font-bold text-gray-700 uppercase mb-1">Rencana Deskripsi Pekerjaan (Jobdesc) <span class="text-red-500">*</span></label>
                            <textarea x-model="formData.jobdesc" rows="3" placeholder="Jelaskan secara singkat apa saja tugas dan tanggung jawab Anda selama magang..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20 focus:border-vokasi-primary transition-colors resize-none text-sm" required></textarea>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: Data Supervisor -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6">
                    <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-3">
                        <h3 class="text-base font-bold text-gray-800 flex items-center">
                            <span class="w-6 h-6 rounded-full bg-vokasi-primary text-white text-xs flex items-center justify-center mr-2 font-bold">3</span>
                            Data Supervisor Lapangan (SPV)
                        </h3>
                        <span class="text-[10px] font-bold bg-red-100 text-red-600 px-2.5 py-1 rounded-full border border-red-200">Sangat Penting</span>
                    </div>
                    
                    <p class="text-xs text-gray-500 mb-4 leading-relaxed">Admin akan mencatat data Supervisor Lapangan menggunakan kredensial email di bawah ini agar beliau dapat mengevaluasi dan menilai kinerja magang Anda.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Nama Lengkap Supervisor <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.nama_supervisor" placeholder="Beserta gelar jika ada" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20 focus:border-vokasi-primary transition-colors text-sm" required>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Jabatan Supervisor <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.jabatan_supervisor" placeholder="Contoh: Manager Operasional" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20 focus:border-vokasi-primary transition-colors text-sm" required>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Email Aktif Supervisor <span class="text-red-500">*</span></label>
                            <input type="email" x-model="formData.email_supervisor" placeholder="email@perusahaan.com" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20 focus:border-vokasi-primary transition-colors text-sm" required>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">No. HP / WhatsApp <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.no_hp_supervisor" placeholder="08..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20 focus:border-vokasi-primary transition-colors text-sm" required>
                        </div>
                    </div>
                </div>

                <!-- SECTION 4: Upload Dokumen -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6">
                    <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center border-b border-gray-100 pb-3">
                        <span class="w-6 h-6 rounded-full bg-vokasi-primary text-white text-xs flex items-center justify-center mr-2 font-bold">4</span>
                        Dokumen Pendukung Mandiri
                    </h3>
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2">
                            Surat Balasan / Bukti Diterima Magang <span class="text-red-500">*</span>
                            <span class="text-vokasi-primary font-normal text-[11px] ml-1">(Bisa pilih s.d 10 MB, Otomatis dikompres)</span>
                        </label>
                        
                        <!-- Drag & Drop Zone Isolated Picker -->
                        <div class="relative border-2 border-dashed border-gray-300 hover:border-vokasi-primary rounded-2xl p-6 text-center bg-gray-50/50 hover:bg-vokasi-primary/5 transition-all cursor-pointer group">
                            <input type="file" id="standalone_file_picker" accept=".pdf,.jpg,.jpeg,.png,.webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="onFileSelected($event)">
                            
                            <div class="space-y-2">
                                <div class="w-12 h-12 bg-vokasi-primary/10 text-vokasi-primary rounded-full flex items-center justify-center mx-auto text-xl group-hover:scale-110 transition-transform">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div class="text-sm font-semibold text-gray-700">
                                    <span class="text-vokasi-primary">Klik untuk memilih file</span> atau tarik & lepas di sini
                                </div>
                                <p class="text-xs text-gray-400">PDF, JPG, PNG, WEBP (Maksimal 10 MB)</p>
                            </div>
                        </div>

                        <!-- STATUS ANIMASI KOMPRESI -->
                        <div x-show="isCompressing" x-cloak class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-xl flex items-center gap-3 text-xs text-blue-800 font-semibold">
                            <i class="fas fa-spinner fa-spin text-vokasi-primary text-base"></i>
                            <span>Mengompresi berkas di browser... Harap tunggu sejenak.</span>
                        </div>

                        <!-- INFORMASI HASIL KOMPRESI SUKSES -->
                        <div x-show="isCompressedReady && fileInfo" x-cloak class="mt-2 p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs text-emerald-800 font-bold">
                            <i class="fas fa-check-circle text-emerald-600 text-base"></i>
                            <span x-text="fileInfo"></span>
                        </div>

                        <!-- INFORMASI ERROR -->
                        <div x-show="errorMessage" x-cloak class="mt-2 p-3 bg-red-50 border border-red-200 rounded-xl flex items-center gap-2 text-xs text-red-800 font-bold">
                            <i class="fas fa-times-circle text-red-600 text-base"></i>
                            <span x-text="errorMessage"></span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 mb-8">
                    <a href="{{ route('dashboard-mahasiswa-status-pengajuan') }}" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-bold text-xs transition-colors shadow-sm">
                        Batal
                    </a>
                    <button type="submit" 
                            :disabled="isCompressing || !isCompressedReady || isSubmitting" 
                            :class="(isCompressing || !isCompressedReady || isSubmitting) ? 'opacity-60 cursor-not-allowed bg-gray-400' : 'bg-vokasi-primary hover:bg-vokasi-dark'"
                            class="px-8 py-3 text-white rounded-xl font-bold text-xs transition-colors shadow-sm flex items-center justify-center gap-2">
                        <template x-if="isSubmitting">
                            <span class="flex items-center gap-2"><i class="fas fa-spinner fa-spin"></i> Mengirim Pengajuan...</span>
                        </template>
                        <template x-if="!isSubmitting && isCompressedReady">
                            <span class="flex items-center gap-2"><i class="fas fa-paper-plane"></i> Kirim Pengajuan Mandiri</span>
                        </template>
                        <template x-if="!isSubmitting && isCompressing">
                            <span class="flex items-center gap-2"><i class="fas fa-spinner fa-spin"></i> Mengompresi File...</span>
                        </template>
                        <template x-if="!isSubmitting && !isCompressing && !isCompressedReady">
                            <span class="flex items-center gap-2"><i class="fas fa-file-upload"></i> Unggah Surat Balasan Dahulu</span>
                        </template>
                    </button>
                </div>

            </form>
        @endif

    </div>

    <!-- FOOTER -->
    <footer class="mt-auto py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
        Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
    </footer>

</main>

<script>
    function ajukanMandiriComponent() {
        return {
            formData: {
                nama_instansi: '',
                sektor_industri: '',
                website_instansi: '',
                alamat_instansi: '',
                posisi: '',
                tanggal_mulai: '',
                tanggal_selesai: '',
                jobdesc: '',
                nama_supervisor: '',
                jabatan_supervisor: '',
                email_supervisor: '',
                no_hp_supervisor: ''
            },
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

                compressMandiriFileOffline(file, function(compressedFile, err) {
                    if (err) {
                        self.isCompressing = false;
                        self.isCompressedReady = false;
                        self.errorMessage = err;
                        picker.value = '';
                        return;
                    }

                    self.compressedBlobFile = compressedFile;
                    const origStr = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                    const newStr = (compressedFile.size / 1024).toFixed(0) + ' KB';
                    self.fileInfo = `Berhasil Dikompresi di Browser: ${origStr} → ${newStr}`;
                    self.isCompressing = false;
                    self.isCompressedReady = true;
                });
            },

            submitForm() {
                if (!this.compressedBlobFile || !this.isCompressedReady) {
                    alert('Surat balasan belum selesai dikompresi!');
                    return;
                }

                this.isSubmitting = true;

                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                
                // Binding Seluruh Field
                Object.keys(this.formData).forEach(key => {
                    formData.append(key, this.formData[key]);
                });

                // Attach File Terkompres
                formData.append('surat_balasan', this.compressedBlobFile, this.compressedBlobFile.name);

                fetch('{{ route("dashboard-mahasiswa-ajukan-mandiri-store") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                        return;
                    }
                    if (!response.ok) {
                        return response.json().then(errData => {
                            throw new Error(errData.message || 'Terjadi kesalahan saat menyimpan pengajuan.');
                        });
                    }
                    window.location.href = '{{ route("dashboard-mahasiswa-status-pengajuan") }}';
                })
                .catch(err => {
                    this.isSubmitting = false;
                    alert('Gagal mengirim pengajuan: ' + err.message);
                });
            }
        };
    }

    // ENGINE KOMPRESI BERKAS MANDIRI (PDF & GAMBAR)
    async function compressMandiriFileOffline(file, callback) {
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
                    }, 'image/jpeg', 0.7);
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
                callback(null, 'Gagal mengompresi berkas PDF di browser.');
            }
        } else {
            callback(file, null);
        }
    }
</script>
@endsection