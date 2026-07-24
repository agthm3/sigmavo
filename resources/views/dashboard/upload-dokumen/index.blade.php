@extends('layouts.dashboard')

@section('content')
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 lg:p-8 custom-scrollbar">
    
    <!-- HEADER HALAMAN -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Upload Dokumen Laporan Magang</h1>
            <p class="text-sm text-gray-500 mt-1">Unggah berkas laporan akhir, lembar pengesahan, dan dokumen kelengkapan Magang Industri 900 Jam.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                <i class="fas fa-clock mr-1.5"></i> Batas Akhir: 15 Agustus 2026
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- KOLOM KIRI: FORM UPLOAD DOKUMEN (8 Cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-1">Form Unggah Berkas Baru</h2>
                <p class="text-xs text-gray-500 mb-6">Pilih jenis dokumen dan lampirkan file resmi dalam format PDF/Word.</p>

                <form action="#" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <!-- Jenis Dokumen -->
                    <div>
                        <label for="jenis_dokumen" class="block text-sm font-semibold text-gray-700 mb-2">
                            Jenis Dokumen <span class="text-red-500">*</span>
                        </label>
                        <select id="jenis_dokumen" name="jenis_dokumen" required class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20 focus:border-vokasi-primary transition-colors">
                            <option value="" disabled selected>-- Pilih Jenis Dokumen --</option>
                            <option value="laporan_akhir">Laporan Akhir Magang (PDF - Gabungan)</option>
                            <option value="lembar_pengesahan">Lembar Pengesahan Ber-TTD (PDF)</option>
                            <option value="sertifikat_industri">Sertifikat Magang Mitra (PDF/JPG)</option>
                            <option value="lampiran_pendukung">Dokumen Lampiran Pendukung (ZIP/PDF)</option>
                        </select>
                    </div>

                    <!-- Catatan / Keterangan Singkat -->
                    <div>
                        <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-2">
                            CatatanTambahan (Opsional)
                        </label>
                        <textarea id="keterangan" name="keterangan" rows="2" placeholder="Contoh: Revisi Bab 3 sesuai masukan DPL..." class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20 focus:border-vokasi-primary transition-colors"></textarea>
                    </div>

                    <!-- Drag & Drop File Upload Area -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Lampiran File <span class="text-red-500">*</span>
                        </label>
                        <div class="border-2 border-dashed border-gray-300 hover:border-vokasi-primary rounded-2xl p-8 text-center bg-gray-50/50 hover:bg-vokasi-primary/5 transition-all cursor-pointer relative group">
                            <input type="file" name="file_dokumen" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <div class="space-y-2">
                                <div class="w-12 h-12 bg-vokasi-primary/10 text-vokasi-primary rounded-full flex items-center justify-center mx-auto text-xl group-hover:scale-110 transition-transform">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div class="text-sm font-semibold text-gray-700">
                                    <span class="text-vokasi-primary">Klik untuk memilih file</span> atau tarik & lepas di sini
                                </div>
                                <p class="text-xs text-gray-400">Format yang didukung: PDF, DOCX, ZIP (Maksimal 15 MB)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Button Submit -->
                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-6 py-3 bg-vokasi-primary hover:bg-vokasi-dark text-white font-semibold text-sm rounded-xl transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                            <i class="fas fa-upload"></i> Unggah Dokumen
                        </button>
                    </div>
                </form>
            </div>

            <!-- TABEL RIWAYAT DOKUMEN YANG DIUNGGAH -->
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-800">Berkas Terunggah</h2>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full">2/3 Dokumen Wajib</span>
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
                            <!-- Item 1 -->
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-4 pl-6">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-9 h-9 bg-red-100 text-red-600 rounded-lg flex items-center justify-center font-bold shrink-0">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800">Laporan_Akhir_Magang_Fadehl.pdf</p>
                                            <p class="text-xs text-gray-400">2.4 MB • Laporan Akhir</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-xs text-gray-500">20 Jul 2026, 14:30 WITA</td>
                                <td class="p-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fas fa-check-circle mr-1 text-[10px]"></i> Disetujui
                                    </span>
                                </td>
                                <td class="p-4 text-right pr-6 space-x-2">
                                    <a href="#" class="p-2 text-gray-400 hover:text-vokasi-primary transition-colors" title="Lihat/Download"><i class="fas fa-eye"></i></a>
                                    <a href="#" class="p-2 text-gray-400 hover:text-red-500 transition-colors" title="Hapus"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>

                            <!-- Item 2 -->
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-4 pl-6">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-9 h-9 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center font-bold shrink-0">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800">Lembar_Pengesahan_TTD.pdf</p>
                                            <p class="text-xs text-gray-400">850 KB • Lembar Pengesahan</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-xs text-gray-500">22 Jul 2026, 09:15 WITA</td>
                                <td class="p-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="fas fa-hourglass-half mr-1 text-[10px]"></i> Perlu Verifikasi
                                    </span>
                                </td>
                                <td class="p-4 text-right pr-6 space-x-2">
                                    <a href="#" class="p-2 text-gray-400 hover:text-vokasi-primary transition-colors" title="Lihat/Download"><i class="fas fa-eye"></i></a>
                                    <a href="#" class="p-2 text-gray-400 hover:text-red-500 transition-colors" title="Hapus"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
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
                        <i class="fas fa-clock text-amber-500 text-base shrink-0 mt-0.5"></i>
                        <div>
                            <p class="font-bold">Lembar Pengesahan Resmi</p>
                            <p class="text-gray-400">Wajib bertanda tangan DPL & Supervisor.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3 text-gray-400">
                        <i class="far fa-circle text-gray-300 text-base shrink-0 mt-0.5"></i>
                        <div>
                            <p class="font-bold text-gray-500">Sertifikat Magang Industri</p>
                            <p class="text-gray-400">Dikeluarkan oleh pihak mitra/perusahaan.</p>
                        </div>
                    </li>
                </ul>

                <div class="pt-2">
                    <div class="bg-teal-50 border border-teal-100 rounded-xl p-4 text-xs text-vokasi-dark">
                        <p class="font-bold mb-1"><i class="fas fa-info-circle mr-1"></i> Prosedur Validasi</p>
                        <p class="leading-relaxed">Dokumen yang telah diunggah akan diverifikasi oleh Dosen Pembimbing Lapangan sebelum dinilai akhir.</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

</main>
@endsection