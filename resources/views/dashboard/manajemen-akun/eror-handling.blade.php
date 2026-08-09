@extends('layouts.dashboard')

@section('content')
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 lg:p-8 custom-scrollbar">
    
    <!-- HEADER -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Konfigurasi Error Handling & Debug</h1>
            <p class="text-sm text-gray-500 mt-1">Atur visibilitas rincian kode kesalahan (Technical Stack Trace & Error Code) pada halaman error kustom (404, 403, 500, dll).</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                <i class="fas fa-bug mr-1.5"></i> System Debug Control
            </span>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between text-sm">
        <div class="flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900"><i class="fas fa-times"></i></button>
    </div>
    @endif

    <div class="max-w-3xl">
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden p-6 md:p-8">
            <form action="{{ route('dashboard-manajemen-error-handling-toggle') }}" method="POST" class="space-y-6">
                @csrf

                <div class="flex items-center justify-between p-5 bg-gray-50 border border-gray-200 rounded-2xl">
                    <div class="space-y-1 pr-4">
                        <label for="toggleError" class="text-base font-bold text-gray-800 block cursor-pointer">
                            Tampilkan Dropdown Detail Error Code
                        </label>
                        <p class="text-xs text-gray-500 leading-relaxed">
                            Jika diaktifkan, halaman kesalahan (404, 403, 500) akan memiliki tombol accordion/dropdown tersembunyi yang berisi rincian kode teknis error saat diklik pengguna.
                        </p>
                    </div>

                    <!-- TOGGLE SWITCH -->
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" id="toggleError" name="show_error_detail" value="1" onchange="this.form.submit()" {{ $showErrorDetail ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-vokasi-primary"></div>
                    </label>
                </div>

                <!-- INDIKATOR STATUS -->
                <div class="p-4 rounded-xl text-xs flex items-center gap-3 {{ $showErrorDetail ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                    <i class="fas {{ $showErrorDetail ? 'fa-eye text-emerald-600' : 'fa-eye-slash text-amber-600' }} text-lg"></i>
                    <div>
                        <p class="font-bold">Status Saat Ini: {{ $showErrorDetail ? 'AKTIF (Detail Error Ditampilkan dalam Collapsible)' : 'NON-AKTIF (Detail Error Disembunyikan)' }}</p>
                        <p class="mt-0.5 opacity-90">{{ $showErrorDetail ? 'User dapat mengklik dropdown di halaman 404/403/500 untuk melihat kode pesan teknis.' : 'Halaman error hanya menampilkan pesan ramah umum tanpa opsi melihat kode kesalahan teknis.' }}</p>
                    </div>
                </div>

                <!-- PREVIEW UJI COBA -->
                <div class="pt-4 border-t border-gray-100">
                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Uji Coba Tampilan Error Page:</h4>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ url('/testing-error-403') }}" target="_blank" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">Test Page 403</a>
                        <a href="{{ url('/testing-error-404') }}" target="_blank" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">Test Page 404</a>
                        <a href="{{ url('/testing-error-500') }}" target="_blank" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">Test Page 500</a>
                    </div>
                </div>

            </form>
        </div>
    </div>

</main>
@endsection