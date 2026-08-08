@extends('layouts.dashboard')

@section('content')
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 lg:p-8 custom-scrollbar">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Unggah Laporan Akhir Magang</h1>
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

        <!-- FORM UPLOAD / UPDATE LAPORAN -->
        @if(!$laporan || $laporan->status_verifikasi !== 'approved')
        <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm space-y-4">
            <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3">
                <i class="fas fa-cloud-upload-alt text-vokasi-primary mr-2"></i> Form Upload {{ $laporan ? 'Revisi Laporan' : 'Laporan Baru' }}
            </h3>

            <form action="{{ route('dashboard-mahasiswa-laporan-akhir-store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Judul Laporan Akhir <span class="text-red-500">*</span></label>
                    <input type="text" name="judul_laporan" value="{{ old('judul_laporan', $laporan?->judul_laporan) }}" required placeholder="Contoh: Laporan Akhir Magang Pengembangan Sistem SIGMAVO pada PT..." class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">File Dokumen PDF <span class="text-red-500">* Maks 10MB</span></label>
                    <input type="file" name="file_laporan" accept=".pdf" required class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-vokasi-primary/10 file:text-vokasi-primary hover:file:bg-vokasi-primary/20">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 bg-vokasi-primary hover:bg-vokasi-dark text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i> Kirim Laporan Akhir
                    </button>
                </div>
            </form>
        </div>
        @endif

    </div>
</main>
@endsection