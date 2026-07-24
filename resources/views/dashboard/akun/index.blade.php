@extends('layouts.dashboard')


@section('content')
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col">
            
            <div class="flex-1">
                
                <!-- Page Header -->
                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">Profil & Pengaturan Akun</h3>
                    <p class="text-sm text-gray-500 mt-1">Kelola informasi pribadi dan pengaturan keamanan akun Anda.</p>
                </div>

                <div class="flex flex-col lg:flex-row gap-6">
                    
                    <!-- LEFT COLUMN: PROFILE CARD -->
                    <div class="w-full lg:w-1/3">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                            <!-- Cover Image -->
                            <div class="h-24 bg-vokasi-primary"></div>
                            
                            <div class="px-6 pb-6 relative">
                                <!-- Avatar -->
                                <div class="flex justify-center -mt-12 mb-4">
                                    <div class="relative">
                                        <img src="https://ui-avatars.com/api/?name=Mahasiswa+Vokasi&background=ffffff&color=37A7AC&size=128" alt="Profile" class="w-24 h-24 rounded-full border-4 border-white shadow-md bg-white">
                                        <button class="absolute bottom-0 right-0 bg-gray-800 text-white p-1.5 rounded-full hover:bg-vokasi-primary transition-colors text-xs" title="Ganti Foto">
                                            <i class="fas fa-camera"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Profile Info -->
                                <div class="text-center mb-6">
                                    <h4 class="text-xl font-bold text-gray-800">Fadehl Thristansyah</h4>
                                    <p class="text-gray-500 text-sm font-medium">H071231012</p>
                                    <div class="mt-2 inline-block px-3 py-1 bg-[#e6f4f5] text-vokasi-dark text-xs font-semibold rounded-full">
                                        Mahasiswa Aktif
                                    </div>
                                </div>

                                <!-- Details -->
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between border-b border-gray-100 pb-2">
                                        <span class="text-gray-500">Program Studi</span>
                                        <span class="font-medium text-gray-800 text-right">D4 Teknologi Produksi Pertanian</span>
                                    </div>
                                    <div class="flex justify-between border-b border-gray-100 pb-2">
                                        <span class="text-gray-500">Fakultas</span>
                                        <span class="font-medium text-gray-800 text-right">Vokasi</span>
                                    </div>
                                    <div class="flex justify-between border-b border-gray-100 pb-2">
                                        <span class="text-gray-500">Tahun Masuk</span>
                                        <span class="font-medium text-gray-800">2023</span>
                                    </div>
                                    <div class="flex justify-between pt-1">
                                        <span class="text-gray-500">Status Magang</span>
                                        <span class="font-bold text-yellow-600"><i class="fas fa-clock mr-1"></i> Belum Magang</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: FORM EDIT PROFILE -->
                    <div class="w-full lg:w-2/3 space-y-6">
                        
                        <!-- Form Informasi Pribadi -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                            <h4 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Informasi Pribadi</h4>
                            
                            <form action="#" method="POST" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Read-only inputs (dari SIAKAD) -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                        <input type="text" value="Fadehl Thristansyah" readonly class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-500 cursor-not-allowed focus:outline-none">
                                        <p class="text-[10px] text-gray-400 mt-1">*Nama sesuai SIAKAD</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">NIM</label>
                                        <input type="text" value="H071231012" readonly class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-500 cursor-not-allowed focus:outline-none">
                                    </div>

                                    <!-- Editable inputs -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                        <input type="email" value="fadehl@student.unhas.ac.id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp <span class="text-red-500">*</span></label>
                                        <input type="text" value="081234567890" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Domisili <span class="text-red-500">*</span></label>
                                    <textarea rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors resize-none">Jl. Perintis Kemerdekaan KM. 10, Tamalanrea, Makassar</textarea>
                                </div>

                                <div class="flex justify-end pt-2">
                                    <button type="button" class="bg-vokasi-primary hover:bg-vokasi-dark text-white font-medium py-2 px-6 rounded-lg transition-colors shadow-sm">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Form Ubah Password -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                            <h4 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Ubah Password</h4>
                            
                            <form action="#" method="POST" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                                        <input type="password" placeholder="Masukkan password lama" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                                        <input type="password" placeholder="Minimal 8 karakter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                                        <input type="password" placeholder="Ulangi password baru" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors">
                                    </div>
                                </div>
                                
                                <div class="flex justify-end pt-2">
                                    <button type="button" class="bg-gray-800 hover:bg-gray-900 text-white font-medium py-2 px-6 rounded-lg transition-colors shadow-sm">
                                        Perbarui Password
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