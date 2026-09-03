@extends('layouts.dashboard')

@section('content')
<!-- CDN PDF-LIB Untuk Kompresi PDF Client-Side -->
<script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>

<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative custom-scrollbar"
      x-data="ajukanMandiriComponent()">
    
    <div class="max-w-4xl mx-auto w-full flex-1">
        
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Formulir Pengajuan Magang Mandiri</h2>
            <p class="text-sm text-gray-500 mt-1">Pilih instansi mitra yang sudah ada atau daftarkan instansi baru jika belum tercatat di portal.</p>
        </div>

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
            <!-- ALERT Pendaftaran Aktif -->
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-amber-900 space-y-3 mb-6 shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-amber-600 text-2xl"></i>
                    <h4 class="font-bold text-base">Pengajuan Magang Masih Aktif</h4>
                </div>
                <p class="text-xs leading-relaxed text-amber-800">
                    Anda saat ini sudah memiliki pendaftaran magang aktif yang sedang dalam proses verifikasi atau diterima.
                </p>
                <div class="pt-2">
                    <a href="{{ route('dashboard-mahasiswa-status-pengajuan') }}" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl transition shadow-sm">
                        <i class="fas fa-file-alt mr-2"></i> Lihat Status Pengajuan Saya
                    </a>
                </div>
            </div>
        @else

            <form id="formAjukanMandiri" @submit.prevent="submitForm()" class="space-y-6">
                @csrf
                
                <!-- SECTION 1: Informasi Instansi (Searchable Dropdown) -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6">
                    <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center border-b border-gray-100 pb-3">
                        <span class="w-6 h-6 rounded-full bg-vokasi-primary text-white text-xs flex items-center justify-center mr-2 font-bold">1</span>
                        Informasi Instansi / Perusahaan
                    </h3>
                    
                    <div class="space-y-4 text-xs">
                        
                        <!-- Dropdown Search Instansi -->
                        <div class="relative">
                            <label class="block font-bold text-gray-700 uppercase mb-1">Cari Instansi / Perusahaan <span class="text-red-500">*</span></label>
                            <p class="text-gray-400 text-[11px] mb-2">Ketik nama instansi (misal: Puskesmas, PT, Dinas). Pilih dari daftar jika sudah tersedia agar tidak ada duplikasi data.</p>
                            
                            <div class="relative">
                                <i class="fas fa-search absolute left-3.5 top-3 text-gray-400 text-sm"></i>
                                <input type="text" 
                                       x-model="searchQuery" 
                                       @focus="dropdownOpen = true" 
                                       @input="dropdownOpen = true; isNewCompany = false"
                                       placeholder="Ketik nama instansi untuk mencari..." 
                                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20 focus:border-vokasi-primary text-sm font-medium">
                            </div>

                            <!-- List Hasil Pencarian -->
                            <div x-show="dropdownOpen" 
                                 @click.away="dropdownOpen = false" 
                                 x-cloak
                                 class="absolute z-30 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-xl max-h-60 overflow-y-auto custom-scrollbar">
                                
                                <!-- Opsi Tambah Baru (Muncul jika user mengetik sesuatu) -->
                                <template x-if="searchQuery.trim().length > 0">
                                    <div @click="selectNewCompany()" class="p-3 bg-purple-50 hover:bg-purple-100 border-b border-purple-100 cursor-pointer text-purple-700 font-bold flex items-center gap-2 transition">
                                        <i class="fas fa-plus-circle text-purple-600 text-base"></i>
                                        <span>Instansi tidak ada di daftar? Daftarkan Instansi Baru: "<span x-text="searchQuery"></span>"</span>
                                    </div>
                                </template>

                                <!-- Opsi Perusahaan Terdaftar -->
                                <template x-for="p in filteredPerusahaans" :key="p.id">
                                    <div @click="selectExistingCompany(p)" class="p-3 hover:bg-teal-50 cursor-pointer border-b border-gray-100 text-gray-700 transition">
                                        <p class="font-bold text-sm text-gray-800" x-text="p.nama_perusahaan"></p>
                                        <p class="text-[11px] text-gray-400 mt-0.5" x-text="(p.sektor_industri || 'Lainnya') + ' • ' + (p.alamat || '-')"></p>
                                    </div>
                                </template>

                                <template x-if="filteredPerusahaans.length === 0 && searchQuery.trim().length === 0">
                                    <div class="p-3 text-gray-400 text-center text-xs italic">
                                        Ketik nama instansi untuk memulai pencarian...
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Badge Konfirmasi Terpilih -->
                        <template x-if="selectedCompany">
                            <div class="p-3 bg-teal-50 border border-teal-200 rounded-xl flex items-center justify-between text-teal-800">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-check-circle text-teal-600 text-base"></i>
                                    <div>
                                        <span class="font-bold text-xs">Instansi Terdaftar Terpilih:</span>
                                        <p class="text-sm font-bold text-teal-900" x-text="selectedCompany.nama_perusahaan"></p>
                                    </div>
                                </div>
                                <button type="button" @click="resetCompanySelection()" class="px-3 py-1.5 bg-white text-xs text-red-600 border border-red-200 hover:bg-red-50 rounded-lg font-semibold shadow-sm transition">Ganti Pilihan</button>
                            </div>
                        </template>

                        <!-- Form Detail Instansi -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                            <div>
                                <label class="block font-bold text-gray-700 uppercase mb-1">Sektor Industri <span class="text-red-500">*</span></label>
                                <select x-model="formData.sektor_industri" :disabled="selectedCompany !== null" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold focus:border-vokasi-primary disabled:bg-gray-100 disabled:text-gray-500" required>
                                    <option value="" disabled selected>Pilih Bidang Industri</option>
                                    <option value="Kesehatan">Kesehatan / Rumah Sakit / Puskesmas</option>
                                    <option value="Teknologi Informasi">Teknologi Informasi / Software</option>
                                    <option value="Pertanian">Pertanian / Agrikultur</option>
                                    <option value="Manufaktur">Manufaktur / Industri</option>
                                    <option value="Pemerintahan">Instansi Pemerintah</option>
                                    <option value="Perbankan">Perbankan / Keuangan</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-gray-700 uppercase mb-1">Website Instansi</label>
                                <input type="url" x-model="formData.website_instansi" :disabled="selectedCompany !== null" placeholder="https://..." class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:border-vokasi-primary disabled:bg-gray-100 disabled:text-gray-500">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block font-bold text-gray-700 uppercase mb-1">Alamat Instansi <span class="text-red-500">*</span></label>
                                <textarea x-model="formData.alamat_instansi" :disabled="selectedCompany !== null" rows="2" placeholder="Alamat lengkap instansi..." class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs resize-none focus:border-vokasi-primary disabled:bg-gray-100 disabled:text-gray-500" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: Detail Magang -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6">
                    <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center border-b border-gray-100 pb-3">
                        <span class="w-6 h-6 rounded-full bg-vokasi-primary text-white text-xs flex items-center justify-center mr-2 font-bold">2</span>
                        Detail Rencana Magang
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div class="md:col-span-2">
                            <label class="block font-bold text-gray-700 uppercase mb-1">Posisi / Departemen <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.posisi" placeholder="Contoh: Staf IT Analyst / Terapis Gigi" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm" required>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <input type="date" x-model="formData.tanggal_mulai" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm" required>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                            <input type="date" x-model="formData.tanggal_selesai" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm" required>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block font-bold text-gray-700 uppercase mb-1">Uraian Tugas / Jobdesc <span class="text-red-500">*</span></label>
                            <textarea x-model="formData.jobdesc" rows="3" placeholder="Rincian tugas selama magang..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm resize-none" required></textarea>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: Data Supervisor Lapangan -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6">
                    <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-3">
                        <h3 class="text-base font-bold text-gray-800 flex items-center">
                            <span class="w-6 h-6 rounded-full bg-vokasi-primary text-white text-xs flex items-center justify-center mr-2 font-bold">3</span>
                            Data Supervisor Lapangan (SPV)
                        </h3>
                        <span class="text-[10px] font-bold bg-red-100 text-red-600 px-2.5 py-1 rounded-full border border-red-200">Wajib Diisi</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Nama Lengkap SPV <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.nama_supervisor" placeholder="Nama beserta gelar" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm" required>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Jabatan SPV <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.jabatan_supervisor" placeholder="Contoh: Kepala Puskesmas / Manager" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm" required>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Email Aktif SPV <span class="text-red-500">*</span></label>
                            <input type="email" x-model="formData.email_supervisor" placeholder="email.spv@instansi.com" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm" required>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">No. HP / WhatsApp SPV <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.no_hp_supervisor" placeholder="08..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm" required>
                        </div>
                    </div>
                </div>

                <!-- SECTION 4: Upload Dokumen -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6">
                    <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center border-b border-gray-100 pb-3">
                        <span class="w-6 h-6 rounded-full bg-vokasi-primary text-white text-xs flex items-center justify-center mr-2 font-bold">4</span>
                        Dokumen Pendukung
                    </h3>
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2">
                            Surat Balasan / Bukti Diterima Magang <span class="text-red-500">*</span>
                        </label>
                        
                        <div class="relative border-2 border-dashed border-gray-300 hover:border-vokasi-primary rounded-2xl p-6 text-center bg-gray-50/50 cursor-pointer">
                            <input type="file" accept=".pdf,.jpg,.jpeg,.png,.webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="onFileSelected($event)">
                            <div class="space-y-1">
                                <i class="fas fa-cloud-upload-alt text-2xl text-vokasi-primary"></i>
                                <p class="text-xs font-semibold text-gray-700">Pilih berkas atau seret ke sini</p>
                                <p class="text-[11px] text-gray-400">PDF atau Gambar (Maks 10 MB, Otomatis dikompres)</p>
                            </div>
                        </div>

                        <div x-show="isCompressing" x-cloak class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-800 font-semibold flex items-center gap-2">
                            <i class="fas fa-spinner fa-spin text-vokasi-primary"></i> Mengompresi berkas...
                        </div>

                        <div x-show="isCompressedReady && fileInfo" x-cloak class="mt-2 p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-800 font-bold flex items-center gap-2">
                            <i class="fas fa-check-circle text-emerald-600"></i> <span x-text="fileInfo"></span>
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="flex justify-end gap-3 mb-8">
                    <a href="{{ route('dashboard-mahasiswa-status-pengajuan') }}" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-bold text-xs">Batal</a>
                    <button type="submit" 
                            :disabled="isCompressing || !isCompressedReady || isSubmitting" 
                            :class="(isCompressing || !isCompressedReady || isSubmitting) ? 'opacity-60 cursor-not-allowed bg-gray-400' : 'bg-vokasi-primary hover:bg-vokasi-dark'"
                            class="px-8 py-3 text-white rounded-xl font-bold text-xs shadow-sm flex items-center gap-2">
                        <template x-if="isSubmitting">
                            <span><i class="fas fa-spinner fa-spin mr-1"></i> Mengirim...</span>
                        </template>
                        <template x-if="!isSubmitting">
                            <span><i class="fas fa-paper-plane mr-1"></i> Kirim Pengajuan Mandiri</span>
                        </template>
                    </button>
                </div>

            </form>
        @endif

    </div>

</main>

<script>
    function ajukanMandiriComponent() {
        const perusahaans = @json($perusahaans ?? []);

        return {
            searchQuery: '',
            dropdownOpen: false,
            selectedCompany: null,
            isNewCompany: false,
            allPerusahaans: perusahaans,

            formData: {
                perusahaan_id: '',
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

            get filteredPerusahaans() {
                if (!this.searchQuery) return this.allPerusahaans.slice(0, 5);
                const q = this.searchQuery.toLowerCase();
                return this.allPerusahaans.filter(p => p.nama_perusahaan.toLowerCase().includes(q)).slice(0, 15);
            },

            selectExistingCompany(p) {
                this.selectedCompany = p;
                this.searchQuery = p.nama_perusahaan;
                this.formData.perusahaan_id = p.id;
                this.formData.nama_instansi = p.nama_perusahaan;
                this.formData.sektor_industri = p.sektor_industri || '';
                this.formData.website_instansi = p.website || '';
                this.formData.alamat_instansi = p.alamat || '';
                this.dropdownOpen = false;
            },

            selectNewCompany() {
                this.selectedCompany = null;
                this.isNewCompany = true;
                this.formData.perusahaan_id = 'baru';
                this.formData.nama_instansi = this.searchQuery;
                this.formData.sektor_industri = '';
                this.formData.website_instansi = '';
                this.formData.alamat_instansi = '';
                this.dropdownOpen = false;
            },

            resetCompanySelection() {
                this.selectedCompany = null;
                this.searchQuery = '';
                this.formData.perusahaan_id = '';
                this.formData.nama_instansi = '';
                this.formData.sektor_industri = '';
                this.formData.website_instansi = '';
                this.formData.alamat_instansi = '';
            },

            onFileSelected(event) {
                const file = event.target.files[0];
                if (!file) return;

                this.isCompressing = true;
                this.isCompressedReady = false;
                const self = this;

                compressMandiriFileOffline(file, function(compressedFile, err) {
                    self.isCompressing = false;
                    if (err) {
                        alert(err);
                        return;
                    }
                    self.compressedBlobFile = compressedFile;
                    self.fileInfo = `File siap diunggah: ${(compressedFile.size / 1024).toFixed(0)} KB`;
                    self.isCompressedReady = true;
                });
            },

            submitForm() {
                if (!this.formData.nama_instansi) {
                    alert('Silakan cari dan pilih instansi terlebih dahulu!');
                    return;
                }
                if (!this.compressedBlobFile || !this.isCompressedReady) {
                    alert('Surat balasan belum diunggah atau belum selesai diproses!');
                    return;
                }

                this.isSubmitting = true;
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');

                Object.keys(this.formData).forEach(key => {
                    formData.append(key, this.formData[key]);
                });
                formData.append('surat_balasan', this.compressedBlobFile, this.compressedBlobFile.name);

                fetch('{{ route("dashboard-mahasiswa-ajukan-mandiri-store") }}', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(res => {
                    if (res.redirected) {
                        window.location.href = res.url;
                        return;
                    }
                    if (!res.ok) {
                        return res.json().then(d => { throw new Error(d.message || 'Gagal menyimpan.'); });
                    }
                    window.location.href = '{{ route("dashboard-mahasiswa-status-pengajuan") }}';
                })
                .catch(err => {
                    this.isSubmitting = false;
                    alert(err.message);
                });
            }
        };
    }

    async function compressMandiriFileOffline(file, callback) {
        const isImage = file.type.startsWith('image/');
        const isPdf = file.type === 'application/pdf';

        if (isImage) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    let width = img.width, height = img.height;
                    const maxDim = 1280;
                    if (width > maxDim || height > maxDim) {
                        if (width > height) { height = Math.round((height * maxDim) / width); width = maxDim; }
                        else { width = Math.round((width * maxDim) / height); height = maxDim; }
                    }
                    const canvas = document.createElement('canvas');
                    canvas.width = width; canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);
                    canvas.toBlob(blob => {
                        callback(new File([blob], file.name, { type: 'image/jpeg' }), null);
                    }, 'image/jpeg', 0.7);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else if (isPdf) {
            try {
                const arrayBuffer = await file.arrayBuffer();
                const pdfDoc = await PDFLib.PDFDocument.load(arrayBuffer, { ignoreEncryption: true });
                const pdfBytes = await pdfDoc.save({ useObjectStreams: true });
                callback(new File([pdfBytes], file.name, { type: 'application/pdf' }), null);
            } catch (err) {
                callback(file, null);
            }
        } else {
            callback(file, null);
        }
    }
</script>
@endsection