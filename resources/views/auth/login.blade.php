<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - SIGMAVO | Sistem Informasi Magang Vokasi UNHAS</title>
    
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
<body class="bg-gray-50 font-sans text-gray-800 antialiased min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">

    <div class="max-w-4xl w-full bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 flex flex-col md:flex-row min-h-[550px]">
        
        <!-- KOLOM KIRI: HERO / BRANDING (35%) -->
        <div class="md:w-5/12 bg-gradient-to-br from-vokasi-primary to-vokasi-dark p-8 sm:p-10 text-white flex flex-col justify-between relative overflow-hidden">
            <!-- Decorative Circles -->
            <div class="w-40 h-40 bg-white/10 rounded-full absolute -top-10 -left-10 blur-xl pointer-events-none"></div>
            <div class="w-32 h-32 bg-black/10 rounded-full absolute -bottom-10 -right-10 blur-lg pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center font-bold text-white text-lg">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <span class="font-extrabold text-xl tracking-wider">SIGMAVO</span>
                </div>
                <h2 class="text-2xl font-bold leading-tight">Portal Magang Industri & MBKM</h2>
                <p class="text-xs text-white/80 mt-2 leading-relaxed">
                    Sistem Informasi Terpadu Pelaksanaan & Pengawasan Magang Fakultas Vokasi Universitas Hasanuddin.
                </p>
            </div>

            <div class="relative z-10 pt-6 border-t border-white/20">
                <p class="text-[11px] text-white/70">
                    &copy; {{ date('Y') }} Fakultas Vokasi UNHAS. All rights reserved.
                </p>
            </div>
        </div>

        <!-- KOLOM KANAN: FORM LOGIN (65%) -->
        <div class="md:w-7/12 p-8 sm:p-12 flex flex-col justify-center bg-white" x-data="{ showPassword: false }">
            
            <div class="mb-6">
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Selamat Datang Kembali</h1>
                <p class="text-xs text-gray-500 mt-1">Silakan masukkan kredensial akun Anda untuk mengakses portal.</p>
            </div>

            <!-- PESAN NOTIFIKASI / ERROR (Termasuk Blokir Register) -->
            @if (session('status'))
                <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-600 text-base shrink-0"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-5 p-3.5 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl text-xs flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-amber-600 text-base shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 p-3.5 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-xs space-y-1">
                    <div class="font-bold flex items-center gap-1.5">
                        <i class="fas fa-times-circle"></i> Gagal Masuk:
                    </div>
                    <ul class="list-disc pl-5 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- EMAIL -->
                <div>
                    <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Email Resmi <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                               placeholder="nama@unhas.ac.id / email pendaftaran" 
                               class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-vokasi-primary/30 focus:border-vokasi-primary transition-all">
                        <i class="fas fa-envelope absolute left-3.5 top-3 text-gray-400 text-xs"></i>
                    </div>
                </div>

                <!-- PASSWORD -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Kata Sandi <span class="text-red-500">*</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-vokasi-primary hover:text-vokasi-dark transition-colors">
                                Lupa kata sandi?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required 
                               placeholder="Masukkan kata sandi akun Anda" 
                               class="w-full pl-10 pr-10 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-vokasi-primary/30 focus:border-vokasi-primary transition-all">
                        <i class="fas fa-lock absolute left-3.5 top-3 text-gray-400 text-xs"></i>
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-3.5 top-3 text-gray-400 hover:text-gray-600 text-xs focus:outline-none">
                            <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <!-- REMEMBER ME -->
                <div class="flex items-center justify-between pt-1">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-vokasi-primary border-gray-300 rounded focus:ring-vokasi-primary">
                        <span class="ml-2 text-xs font-medium text-gray-600">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- SUBMIT BUTTON -->
                <div class="pt-2">
                    <button type="submit" class="w-full py-3 bg-vokasi-primary hover:bg-vokasi-dark text-white font-bold text-xs rounded-xl shadow-md transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-sign-in-alt"></i> Masuk ke Portal
                    </button>
                </div>

            </form>

            <div class="mt-8 border-t border-gray-100 pt-4 text-center">
                <p class="text-xs text-gray-500">
                    Belum memiliki akun? <span class="font-semibold text-gray-700">Registrasi dilakukan secara terpusat oleh Admin Prodi / Fakultas Vokasi.</span>
                </p>
            </div>

        </div>

    </div>

</body>
</html>