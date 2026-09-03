<div x-show="openProfileModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div @click.away="openProfileModal = false" class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden border border-gray-100 flex flex-col max-h-[90vh]" x-if="activeUser">
        
        <!-- HEADER BACKGROUND -->
        <div class="relative bg-vokasi-primary p-6 pb-12 flex justify-between items-start text-white shrink-0">
            <h3 class="font-bold text-base"><i class="fas fa-id-badge mr-2"></i> Detail Lengkap Pengguna</h3>
            <button type="button" @click="openProfileModal = false" class="text-white/80 hover:text-white transition"><i class="fas fa-times text-lg"></i></button>
        </div>

        <!-- FOTO PROFIL & STATUS -->
        <div class="px-6 relative -mt-10 mb-4 text-center shrink-0">
            <img :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent(activeUser?.name || 'User') + '&background=f8f9fa&color=37A7AC&size=128'" 
                 class="w-20 h-20 rounded-full border-4 border-white shadow-md mx-auto bg-white">
            <h4 class="font-bold text-lg text-gray-800 mt-2" x-text="activeUser?.name"></h4>
            <p class="text-xs text-vokasi-primary font-bold bg-teal-50 border border-teal-100 inline-block px-3 py-0.5 rounded-full mt-1" x-text="activeUser?.roleStr"></p>
        </div>

        <div class="px-6 pb-6 overflow-y-auto custom-scrollbar flex-1 space-y-4 text-xs">
            
            <!-- GRID INFORMASI UTAMA -->
            <div class="grid grid-cols-2 gap-3 p-4 bg-gray-50 border border-gray-100 rounded-2xl">
                <!-- Kolom Penuh: Email -->
                <div class="flex flex-col border-b border-gray-200/60 pb-2 col-span-2">
                    <span class="text-[10px] uppercase font-bold text-gray-400 mb-0.5"><i class="fas fa-envelope text-vokasi-primary mr-1"></i> Email Resmi</span>
                    <span class="font-semibold text-gray-800 text-[13px]" x-text="activeUser?.email"></span>
                </div>
                
                <!-- Kolom Penuh: No HP -->
                <div class="flex flex-col border-b border-gray-200/60 pb-2 col-span-2">
                    <span class="text-[10px] uppercase font-bold text-gray-400 mb-0.5"><i class="fas fa-phone-alt text-emerald-500 mr-1"></i> Nomor WhatsApp / HP</span>
                    <span class="font-semibold text-gray-800 text-xs" x-text="activeUser?.no_hp || 'Belum diatur'"></span>
                </div>

                <!-- Setengah Kolom: Instansi / Prodi -->
                <div class="flex flex-col border-b border-gray-200/60 pb-2 col-span-2">
                    <span class="text-[10px] uppercase font-bold text-gray-400 mb-0.5"><i class="fas fa-building text-blue-500 mr-1"></i> Instansi / Prodi</span>
                    <span class="font-semibold text-gray-800 text-xs" x-text="activeUser?.instansi"></span>
                </div>

                <!-- Setengah Kolom: NIM / NIP -->
                <div class="flex flex-col">
                    <span class="text-[10px] uppercase font-bold text-gray-400 mb-0.5"><i class="fas fa-id-card text-purple-500 mr-1"></i> Identitas (NIM/NIP)</span>
                    <span class="font-mono font-semibold text-gray-800 text-xs" x-text="activeUser?.nim || activeUser?.nip_nidn || '-'"></span>
                </div>

                <!-- Dinamis: Angkatan atau Jabatan -->
                <template x-if="activeUser?.rawRole === 'mahasiswa'">
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase font-bold text-gray-400 mb-0.5">Tahun Angkatan</span>
                        <span class="font-semibold text-gray-800 text-xs" x-text="activeUser?.angkatan || '-'"></span>
                    </div>
                </template>

                <template x-if="activeUser?.rawRole === 'spv'">
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase font-bold text-gray-400 mb-0.5">Jabatan</span>
                        <span class="font-semibold text-gray-800 text-xs" x-text="activeUser?.jabatan || '-'"></span>
                    </div>
                </template>
            </div>

            <!-- TABEL RIWAYAT MAGANG KHUSUS MAHASISWA -->
            <template x-if="activeUser?.rawRole === 'mahasiswa' && activeUser?.riwayat_magang?.length > 0">
                <div class="p-4 bg-white border border-gray-200 rounded-2xl">
                    <h5 class="font-bold text-gray-800 text-xs uppercase mb-3"><i class="fas fa-history text-vokasi-primary mr-1.5"></i> Riwayat Pengajuan Magang</h5>
                    <div class="space-y-3">
                        <template x-for="magang in activeUser.riwayat_magang">
                            <div class="p-3 bg-gray-50 border border-gray-100 rounded-xl relative">
                                <span class="absolute top-3 right-3 px-2 py-0.5 rounded text-[9px] font-bold uppercase" 
                                      :class="{ 'bg-emerald-100 text-emerald-700': magang.status == 'diterima', 'bg-amber-100 text-amber-700': magang.status == 'menunggu', 'bg-red-100 text-red-700': magang.status == 'ditolak' }" 
                                      x-text="magang.status"></span>
                                <p class="font-bold text-gray-800 pr-16 text-sm" x-text="magang.posisi"></p>
                                <p class="text-[11px] text-purple-600 font-bold mb-2"><i class="fas fa-building mr-1"></i> <span x-text="magang.instansi"></span></p>
                                <div class="grid grid-cols-2 gap-2 text-[10px] text-gray-500 bg-white p-2 rounded-lg border border-gray-100">
                                    <p><i class="fas fa-user-tie text-blue-400 mr-1"></i> Dosen: <br><strong class="text-gray-700" x-text="magang.dosen"></strong></p>
                                    <p><i class="fas fa-user-shield text-amber-400 mr-1"></i> SPV: <br><strong class="text-gray-700" x-text="magang.spv"></strong></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
            
            <template x-if="activeUser?.rawRole === 'mahasiswa' && activeUser?.riwayat_magang?.length === 0">
                <div class="p-4 bg-gray-50 border border-gray-100 rounded-2xl text-center">
                    <p class="text-xs text-gray-400 font-medium"><i class="fas fa-folder-open text-2xl mb-1 block"></i> Belum ada riwayat pengajuan magang.</p>
                </div>
            </template>

            <!-- Reset Password -->
            <div class="p-4 bg-amber-50/70 border border-amber-200 rounded-2xl" x-data="{ showPass: false }">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs font-bold text-amber-900"><i class="fas fa-key text-amber-600 mr-1"></i> Informasi Password:</span>
                    <button @click="openResetForm = !openResetForm" class="text-[11px] px-2 py-1 bg-amber-600 text-white font-bold rounded-lg hover:bg-amber-700">
                        <i class="fas fa-sync-alt mr-1"></i> Reset Password
                    </button>
                </div>
                
                <div class="bg-white px-3 py-2 border border-amber-200 rounded-xl font-mono text-gray-800 flex justify-between items-center" x-show="!openResetForm">
                    <span x-text="showPass ? activeUser?.tempPassword : '••••••••••••'"></span>
                    <i class="fas cursor-pointer text-gray-400" :class="showPass ? 'fa-eye-slash' : 'fa-eye'" @click="showPass = !showPass"></i>
                </div>
                
                <form :action="activeUser?.resetUrl" method="POST" x-show="openResetForm" class="mt-2 flex gap-2">
                    @csrf @method('PATCH')
                    <input type="text" name="new_password" required placeholder="Ketik Password Baru..." class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-vokasi-primary outline-none">
                    <button type="submit" class="bg-amber-600 text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-amber-700">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>