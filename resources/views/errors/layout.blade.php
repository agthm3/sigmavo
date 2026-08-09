@php
    use App\Models\Setting;
    $showErrorDetail = Setting::getByKey('show_error_detail', 'true') === 'true';
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('code') - @yield('title') | SIGMAVO</title>
    
    <!-- Tailwind CSS CDN & FontAwesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'vokasi-primary': '#37A7AC',
                        'vokasi-dark': '#2C868A',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 antialiased min-h-screen flex items-center justify-center p-4">

    <div class="max-w-xl w-full bg-white rounded-3xl border border-gray-200/80 shadow-xl overflow-hidden p-6 sm:p-10 text-center relative">
        
        <!-- DEKORASI LINGKARAN BACKDROP -->
        <div class="w-32 h-32 bg-vokasi-primary/10 rounded-full absolute -top-10 -right-10 blur-2xl pointer-events-none"></div>

        <!-- KODE ERROR BIG BADGE -->
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-vokasi-primary/10 text-vokasi-primary text-3xl font-black mb-6 shadow-inner">
            @yield('code')
        </div>

        <!-- JUDUL & PESAN UTAMA -->
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight mb-2">@yield('title')</h1>
        <p class="text-sm text-gray-500 leading-relaxed mb-8">@yield('message')</p>

        <!-- DROPDOWN ACCORDION TECHNICAL ERROR DETAIL (KUNCI UTAMA) -->
        @if($showErrorDetail)
        <div x-data="{ openDetail: false }" class="mb-8 text-left">
            <button @click="openDetail = !openDetail" 
                    type="button" 
                    class="w-full px-4 py-2.5 bg-gray-100 hover:bg-gray-200/80 text-gray-700 font-bold text-xs rounded-xl flex items-center justify-between transition-colors">
                <span class="flex items-center gap-2">
                    <i class="fas fa-terminal text-vokasi-primary"></i> Tampilkan Rincian Kode Error (Technical Detail)
                </span>
                <i class="fas fa-chevron-down text-xs transition-transform duration-300" :class="{ 'rotate-180': openDetail }"></i>
            </button>

            <div x-show="openDetail" 
                 x-collapse 
                 x-cloak 
                 class="mt-2 p-4 bg-slate-900 text-emerald-400 font-mono text-xs rounded-xl overflow-x-auto shadow-inner border border-slate-800 max-h-48 custom-scrollbar">
                <p class="text-slate-400 font-sans text-[10px] uppercase tracking-wider mb-2 border-b border-slate-800 pb-1">Technical Stack Log / Code Info:</p>
                <div class="whitespace-pre-wrap leading-relaxed">@yield('detail_code')</div>
            </div>
        </div>
        @endif

        <!-- ACTION BUTTONS -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="javascript:history.back()" class="w-full sm:w-auto px-5 py-2.5 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold text-xs rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali ke Halaman Sebelumnya
            </a>
            <a href="{{ route('dashboard-analitik') }}" class="w-full sm:w-auto px-6 py-2.5 bg-vokasi-primary hover:bg-vokasi-dark text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                <i class="fas fa-home"></i> Beranda Utama
            </a>
        </div>

        <p class="text-[11px] text-gray-400 mt-8 border-t border-gray-100 pt-4">SIGMAVO — Sistem Informasi Magang Vokasi Universitas Hasanuddin</p>

    </div>

</body>
</html>