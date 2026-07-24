@extends('layouts.dashboard')


@section('content')
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative">
            
            <div class="max-w-4xl mx-auto w-full flex-1">
                
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Formulir Pengajuan Magang Mandiri</h2>
                    <p class="text-sm text-gray-500 mt-1">Isi formulir ini jika Anda telah diterima atau berencana magang di instansi yang tidak terdaftar di sistem.</p>
                </div>

                <!-- Alert Information -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg shadow-sm mb-6 flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-3 text-lg"></i>
                    <div>
                        <h4 class="text-sm font-bold text-blue-800">Informasi Penting</h4>
                        <p class="text-xs text-blue-700 mt-1">Pengajuan mandiri akan direview oleh Admin Prodi. Pastikan instansi dan deskripsi pekerjaan sesuai dengan kompetensi program studi Anda. <b>Data Supervisor Lapangan wajib diisi</b> agar sistem dapat mengirimkan akses evaluasi magang.</p>
                    </div>
                </div>

                <form action="#" method="POST">
                    
                    <!-- SECTION 1: Informasi Instansi -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center border-b border-gray-100 pb-2">
                            <span class="w-6 h-6 rounded-full bg-vokasi-primary text-white text-xs flex items-center justify-center mr-2">1</span>
                            Informasi Instansi / Perusahaan
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Instansi/Perusahaan <span class="text-red-500">*</span></label>
                                <input type="text" placeholder="Contoh: PT. Inovasi Teknologi Nusantara" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sektor / Bidang Industri <span class="text-red-500">*</span></label>
                                <select class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                                    <option value="" disabled selected>Pilih Bidang Industri</option>
                                    <option value="Teknologi Informasi">Teknologi Informasi / Software</option>
                                    <option value="Pertanian">Pertanian / Agrikultur</option>
                                    <option value="Manufaktur">Manufaktur / Industri</option>
                                    <option value="Pemerintahan">Instansi Pemerintah</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Website Instansi (Opsional)</label>
                                <input type="url" placeholder="https://www.example.com" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap Instansi <span class="text-red-500">*</span></label>
                                <textarea rows="2" placeholder="Jl. Perintis Kemerdekaan KM..." class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors resize-none" required></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: Detail Magang -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center border-b border-gray-100 pb-2">
                            <span class="w-6 h-6 rounded-full bg-vokasi-primary text-white text-xs flex items-center justify-center mr-2">2</span>
                            Detail Rencana Magang
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Posisi / Departemen <span class="text-red-500">*</span></label>
                                <input type="text" placeholder="Contoh: Staf IT Analyst / Departemen Produksi" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rencana Tanggal Mulai <span class="text-red-500">*</span></label>
                                <input type="date" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rencana Tanggal Selesai <span class="text-red-500">*</span></label>
                                <input type="date" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rencana Deskripsi Pekerjaan (Jobdesc) <span class="text-red-500">*</span></label>
                                <textarea rows="3" placeholder="Jelaskan secara singkat apa saja tugas dan tanggung jawab Anda selama magang..." class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors resize-none" required></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: Data Supervisor -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                        <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-2">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                                <span class="w-6 h-6 rounded-full bg-vokasi-primary text-white text-xs flex items-center justify-center mr-2">3</span>
                                Data Supervisor Lapangan
                            </h3>
                            <span class="text-xs font-semibold bg-red-100 text-red-600 px-2 py-1 rounded">Sangat Penting</span>
                        </div>
                        
                        <p class="text-xs text-gray-500 mb-4">Admin akan membuatkan akun sistem untuk Supervisor menggunakan email di bawah ini agar beliau dapat memberikan penilaian magang Anda.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap Supervisor <span class="text-red-500">*</span></label>
                                <input type="text" placeholder="Beserta gelar jika ada" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan Supervisor <span class="text-red-500">*</span></label>
                                <input type="text" placeholder="Contoh: Manager Operasional" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email Aktif Supervisor <span class="text-red-500">*</span></label>
                                <input type="email" placeholder="email@perusahaan.com" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. HP / WhatsApp <span class="text-red-500">*</span></label>
                                <input type="text" placeholder="08..." class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 4: Upload Dokumen -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center border-b border-gray-100 pb-2">
                            <span class="w-6 h-6 rounded-full bg-vokasi-primary text-white text-xs flex items-center justify-center mr-2">4</span>
                            Dokumen Pendukung
                        </h3>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Surat Balasan / Bukti Diterima Magang <span class="text-red-500">*</span></label>
                            
                            <!-- Drag & Drop Zone -->
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:bg-gray-50 transition-colors cursor-pointer group">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 group-hover:text-vokasi-primary transition-colors" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-vokasi-primary hover:text-vokasi-dark focus-within:outline-none">
                                            <span>Upload file dokumen</span>
                                            <input id="file-upload" name="file-upload" type="file" class="sr-only" accept=".pdf,.doc,.docx">
                                        </label>
                                        <p class="pl-1">atau *drag and drop*</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF, DOC, DOCX maksimal 5MB</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-3 mb-8">
                        <button type="button" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 font-medium transition-colors shadow-sm">
                            Batal
                        </button>
                        <button type="submit" class="px-8 py-2.5 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-lg font-bold transition-colors shadow-sm flex items-center">
                            <i class="fas fa-paper-plane mr-2"></i> Ajukan Sekarang
                        </button>
                    </div>

                </form>

            </div>

            <!-- FOOTER -->
            <footer class="py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
                Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
            </footer>

        </main>

@endsection