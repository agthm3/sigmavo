@extends('layouts.dashboard')

@section('content')
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 lg:p-8 custom-scrollbar">
    
    <!-- HEADER HALAMAN -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Download Template Dokumen Magang</h1>
            <p class="text-sm text-gray-500 mt-1">Unduh format resmi laporan, lembar pengesahan, dan berkas administrasi Magang Industri 900 Jam Vokasi UNHAS.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-brand-50 text-vokasi-primary border border-vokasi-primary/20">
                <i class="fas fa-file-signature mr-1.5"></i> Standar Akademik Vokasi
            </span>
        </div>
    </div>

    <!-- CARDS SUMMARY / INFORMASI CEPAT -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 bg-teal-50 text-vokasi-primary rounded-xl flex items-center justify-center text-xl font-bold shrink-0">
                <i class="fas fa-file-word"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Format Utama</p>
                <h3 class="text-lg font-bold text-gray-800">Dokumen .DOCX</h3>
                <p class="text-xs text-gray-500 mt-0.5">Dapat disunting di MS Word / Google Docs</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl font-bold shrink-0">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Aturan Pengisian</p>
                <h3 class="text-lg font-bold text-gray-800">Sesuai Panduan 2026</h3>
                <p class="text-xs text-gray-500 mt-0.5">Wajib menggunakan margin & margin resmi</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl font-bold shrink-0">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Status Template</p>
                <h3 class="text-lg font-bold text-gray-800">Versi Terbaru v2.1</h3>
                <p class="text-xs text-gray-500 mt-0.5">Diperbarui untuk periode berjalan</p>
            </div>
        </div>
    </div>

    <!-- LIST DOKUMEN TEMPLATE -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden mb-8">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Daftar Berkas & Format Laporan</h2>
                <p class="text-xs text-gray-500 mt-0.5">Pilih dan unduh template sesuai kebutuhan tahapan magang Anda.</p>
            </div>
            <span class="text-xs text-gray-400">Total: 4 Dokumen</span>
        </div>

        <div class="divide-y divide-gray-100">
            
            <!-- Item 1: Template Laporan Akhir -->
            <div class="p-6 hover:bg-gray-50/80 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-lg font-bold shrink-0 mt-1 md:mt-0">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                            Template Laporan Akhir Magang Industri 900 Jam
                            <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded">Wajib</span>
                        </h3>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                            Format lengkap bab I sampai bab V, daftar isi otomatis, tata letak gambar/tabel, serta lampiran kegiatan harian.
                        </p>
                        <div class="flex items-center gap-4 text-xs text-gray-400 mt-2">
                            <span><i class="fas fa-file-word text-blue-500 mr-1"></i> .DOCX (2.4 MB)</span>
                            <span><i class="fas fa-clock mr-1"></i> Update: Feb 2026</span>
                        </div>
                    </div>
                </div>
                <div class="shrink-0 flex items-center gap-2">
                    <a href="#" class="inline-flex items-center px-4 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white font-semibold text-sm rounded-xl transition shadow-sm hover:shadow-md">
                        <i class="fas fa-download mr-2"></i> Unduh
                    </a>
                </div>
            </div>

            <!-- Item 2: Lembar Pengesahan -->
            <div class="p-6 hover:bg-gray-50/80 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center text-lg font-bold shrink-0 mt-1 md:mt-0">
                        <i class="fas fa-signature"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                            Template Lembar Pengesahan Laporan Magang
                            <span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded">Resmi</span>
                        </h3>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                            Halaman pengesahan tanda tangan Supervisor Industri, Dosen Pembimbing Lapangan, dan Ketua Departemen.
                        </p>
                        <div class="flex items-center gap-4 text-xs text-gray-400 mt-2">
                            <span><i class="fas fa-file-word text-blue-500 mr-1"></i> .DOCX (512 KB)</span>
                            <span><i class="fas fa-clock mr-1"></i> Update: Jan 2026</span>
                        </div>
                    </div>
                </div>
                <div class="shrink-0 flex items-center gap-2">
                    <a href="#" class="inline-flex items-center px-4 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white font-semibold text-sm rounded-xl transition shadow-sm hover:shadow-md">
                        <i class="fas fa-download mr-2"></i> Unduh
                    </a>
                </div>
            </div>

            <!-- Item 3: Template Presentation (PPT) Seminar -->
            <div class="p-6 hover:bg-gray-50/80 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center text-lg font-bold shrink-0 mt-1 md:mt-0">
                        <i class="fas fa-file-powerpoint"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-base">Template Slide Presentasi Seminar Hasil (PPTX)</h3>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                            Design slide resmi Vokasi UNHAS bertema Teal untuk dipresentasikan saat ujian seminar magang.
                        </p>
                        <div class="flex items-center gap-4 text-xs text-gray-400 mt-2">
                            <span><i class="fas fa-file-powerpoint text-amber-500 mr-1"></i> .PPTX (4.8 MB)</span>
                            <span><i class="fas fa-clock mr-1"></i> Update: Feb 2026</span>
                        </div>
                    </div>
                </div>
                <div class="shrink-0 flex items-center gap-2">
                    <a href="#" class="inline-flex items-center px-4 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white font-semibold text-sm rounded-xl transition shadow-sm hover:shadow-md">
                        <i class="fas fa-download mr-2"></i> Unduh
                    </a>
                </div>
            </div>

            <!-- Item 4: Form Penilaian Industri (Khusus Supervisor) -->
            <div class="p-6 hover:bg-gray-50/80 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center text-lg font-bold shrink-0 mt-1 md:mt-0">
                        <i class="fas fa-poll-h"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-base">Formulir Penilaian Kinerja oleh Pembimbing Industri</h3>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                            Formulir evaluasi cetak / fisik jika pembimbing mitra memilih penilaian manual berbasis berkas fisik.
                        </p>
                        <div class="flex items-center gap-4 text-xs text-gray-400 mt-2">
                            <span><i class="fas fa-file-pdf text-red-500 mr-1"></i> .PDF (850 KB)</span>
                            <span><i class="fas fa-clock mr-1"></i> Update: Jan 2026</span>
                        </div>
                    </div>
                </div>
                <div class="shrink-0 flex items-center gap-2">
                    <a href="#" class="inline-flex items-center px-4 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white font-semibold text-sm rounded-xl transition shadow-sm hover:shadow-md">
                        <i class="fas fa-download mr-2"></i> Unduh
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- CATATAN PETUNJUK PENULISAN -->
    <div class="bg-amber-50/70 border border-amber-200 rounded-2xl p-6 text-xs text-amber-900 flex items-start space-x-4">
        <i class="fas fa-lightbulb text-amber-600 text-xl shrink-0 mt-0.5"></i>
        <div class="space-y-1">
            <h4 class="font-bold text-sm text-amber-900">Petunjuk Penting Sebelum Mengunggah Laporan:</h4>
            <ul class="list-disc list-inside space-y-1 text-amber-800/90">
                <li>Pastikan format font (Times New Roman 12pt, Spasi 1.5) tidak diubah dari template standar.</li>
                <li>Halaman pengesahan harus ditandatangani dan di-scan/diunggah dalam format PDF yang jelas saat pengajuan.</li>
                <li>Laporan akhir yang telah diketik wajib dikonsultasikan dulu dengan Dosen Pembimbing sebelum diunggah ke portal.</li>
            </ul>
        </div>
    </div>

</main>
@endsection