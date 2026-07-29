@extends('layouts.dashboard')

@section('content')
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col custom-scrollbar">
            
            <div class="flex-1 max-w-7xl mx-auto w-full space-y-6">
                
                <!-- Page Header -->
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Profil & Pengaturan Akun</h3>
                    <p class="text-sm text-gray-500 mt-1">Kelola informasi pribadi dan pengaturan keamanan akun Anda.</p>
                </div>

                <!-- NOTIFIKASI SUKSES / ERROR -->
                @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between text-sm shadow-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900"><i class="fas fa-times"></i></button>
                </div>
                @endif

                @if($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-xs shadow-sm">
                    <p class="font-bold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal menyimpan perubahan:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="flex flex-col lg:flex-row gap-6">
                    
                    <!-- LEFT COLUMN: PROFILE CARD -->
                    <div class="w-full lg:w-1/3">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            <!-- Cover Image -->
                            <div class="h-24 bg-gradient-to-r from-vokasi-primary to-vokasi-dark"></div>
                            
                            <div class="px-6 pb-6 relative">
                                <!-- Avatar Upload Trigger -->
                                <div class="flex justify-center -mt-12 mb-4">
                                    <div class="relative group">
                                        @if($user->avatar)
                                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Profile" class="w-24 h-24 rounded-full border-4 border-white shadow-md object-cover bg-white">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=37A7AC&color=fff&size=128" alt="Profile" class="w-24 h-24 rounded-full border-4 border-white shadow-md bg-white">
                                        @endif

                                        <label for="foto-input" class="absolute bottom-0 right-0 bg-gray-800 hover:bg-vokasi-primary text-white p-2 rounded-full cursor-pointer transition-colors text-xs shadow-md" title="Ganti Foto">
                                            <i class="fas fa-camera"></i>
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Profile Info -->
                                <div class="text-center mb-6">
                                    <h4 class="text-lg font-bold text-gray-800">{{ $user->name }}</h4>
                                    <p class="text-gray-500 text-xs font-semibold mt-0.5">NIM: {{ $user->mahasiswaProfile->nim ?? '-' }}</p>
                                    <div class="mt-2 inline-block px-3 py-1 bg-[#e6f4f5] text-vokasi-dark text-[11px] font-bold rounded-full">
                                        Mahasiswa Vokasi Aktif
                                    </div>
                                </div>

                                <!-- Details -->
                                <div class="space-y-3 text-xs">
                                    <div class="flex justify-between border-b border-gray-100 pb-2.5">
                                        <span class="text-gray-500 font-medium">Program Studi</span>
                                        <span class="font-bold text-gray-800 text-right">{{ $user->mahasiswaProfile->prodi->nama_prodi ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-gray-100 pb-2.5">
                                        <span class="text-gray-500 font-medium">Fakultas</span>
                                        <span class="font-bold text-gray-800 text-right">Vokasi UNHAS</span>
                                    </div>
                                    <div class="flex justify-between border-b border-gray-100 pb-2.5">
                                        <span class="text-gray-500 font-medium">Tahun Masuk</span>
                                        <span class="font-bold text-gray-800">{{ $user->mahasiswaProfile->angkatan ?? '2023' }}</span>
                                    </div>
                                    <div class="flex justify-between pt-1 items-center">
                                        <span class="text-gray-500 font-medium">Status Magang</span>
                                        @if($activeMagang)
                                            <span class="font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                                                <i class="fas fa-check-circle mr-1"></i> Magang Aktif
                                            </span>
                                        @else
                                            <span class="font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">
                                                <i class="fas fa-clock mr-1"></i> Belum Magang
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: FORM EDIT PROFILE & PASSWORD -->
                    <div class="w-full lg:w-2/3 space-y-6">
                        
                        <!-- Form Informasi Pribadi -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                            <h4 class="text-base font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3 flex items-center">
                                <i class="fas fa-user-edit text-vokasi-primary mr-2"></i> Informasi Pribadi & Kontak
                            </h4>
                            
                            <form action="{{ route('dashboard-mahasiswa-akun-update-profile') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                                @csrf
                                
                                <input type="file" id="foto-input" name="foto" class="hidden" accept="image/*" onchange="this.form.submit()">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Read-only inputs (dari SIAKAD) -->
                                    <div>
                                        <label class="block font-bold text-gray-700 uppercase mb-1">Nama Lengkap</label>
                                        <input type="text" value="{{ $user->name }}" readonly class="w-full px-3.5 py-2 bg-gray-100 border border-gray-300 rounded-xl text-gray-500 font-medium cursor-not-allowed focus:outline-none">
                                        <p class="text-[10px] text-gray-400 mt-1">*Nama tersinkronisasi dari SIAKAD</p>
                                    </div>
                                    <div>
                                        <label class="block font-bold text-gray-700 uppercase mb-1">NIM</label>
                                        <input type="text" value="{{ $user->mahasiswaProfile->nim ?? '-' }}" readonly class="w-full px-3.5 py-2 bg-gray-100 border border-gray-300 rounded-xl text-gray-500 font-medium cursor-not-allowed focus:outline-none">
                                    </div>

                                    <!-- Editable inputs -->
                                    <div>
                                        <label class="block font-bold text-gray-700 uppercase mb-1">Email <span class="text-red-500">*</span></label>
                                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-vokasi-primary">
                                    </div>
                                    <div>
                                        <label class="block font-bold text-gray-700 uppercase mb-1">No. WhatsApp / HP <span class="text-red-500">*</span></label>
                                        <input type="text" name="no_wa" value="{{ old('no_wa', $user->mahasiswaProfile->no_wa ?? '') }}" placeholder="Contoh: 081234567890" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-vokasi-primary">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block font-bold text-gray-700 uppercase mb-1">Alamat Domisili <span class="text-red-500">*</span></label>
                                    <textarea name="alamat_domisili" rows="3" placeholder="Masukkan alamat lengkap tempat tinggal saat ini..." class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-vokasi-primary resize-none">{{ old('alamat_domisili', $user->mahasiswaProfile->alamat_domisili ?? '') }}</textarea>
                                </div>

                                <div class="flex justify-end pt-2">
                                    <button type="submit" class="bg-vokasi-primary hover:bg-vokasi-dark text-white font-bold py-2.5 px-6 rounded-xl transition-colors shadow-sm text-xs">
                                        <i class="fas fa-save mr-1.5"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Form Ubah Password -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                            <h4 class="text-base font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3 flex items-center">
                                <i class="fas fa-key text-vokasi-primary mr-2"></i> Keamanan & Ubah Password
                            </h4>
                            
                            <form action="{{ route('dashboard-mahasiswa-akun-update-password') }}" method="POST" class="space-y-4 text-xs">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block font-bold text-gray-700 uppercase mb-1">Password Saat Ini <span class="text-red-500">*</span></label>
                                        <input type="password" name="current_password" required placeholder="Masukkan password lama Anda" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-vokasi-primary">
                                    </div>
                                    <div>
                                        <label class="block font-bold text-gray-700 uppercase mb-1">Password Baru <span class="text-red-500">*</span></label>
                                        <input type="password" name="password" required placeholder="Minimal 8 karakter" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-vokasi-primary">
                                    </div>
                                    <div>
                                        <label class="block font-bold text-gray-700 uppercase mb-1">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                                        <input type="password" name="password_confirmation" required placeholder="Ulangi password baru" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-vokasi-primary">
                                    </div>
                                </div>
                                
                                <div class="flex justify-end pt-2">
                                    <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2.5 px-6 rounded-xl transition-colors shadow-sm text-xs">
                                        <i class="fas fa-lock mr-1.5"></i> Perbarui Password
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

            </div>

            <!-- FOOTER -->
            <footer class="mt-8 py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
                Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
            </footer>

        </main>
@endsection