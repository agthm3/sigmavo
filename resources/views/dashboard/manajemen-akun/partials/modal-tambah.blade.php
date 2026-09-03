<div x-show="openModal" x-cloak class="fixed inset-0 z-[60] overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div @click.away="openModal = false" class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden border border-gray-100 flex flex-col max-h-[90vh]">
        <div class="p-6 border-b border-gray-100 flex justify-between bg-gray-50/50 shrink-0">
            <h3 class="font-bold text-gray-800"><i class="fas fa-user-plus text-vokasi-primary mr-2"></i> Tambah User Manual</h3>
            <button type="button" @click="openModal = false" class="text-gray-400"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('dashboard-manajemen-aktivasi-store') }}" method="POST" class="p-6 space-y-4 text-xs overflow-y-auto custom-scrollbar flex-1">
            @csrf
            <div>
                <label class="block font-bold text-gray-700 uppercase mb-1">Nama Lengkap & Gelar <span class="text-red-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Budi Santoso, S.T." class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-vokasi-primary outline-none">
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" required placeholder="email@contoh.com" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-vokasi-primary outline-none">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required placeholder="Min. 8 Karakter" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-vokasi-primary outline-none">
                </div>
            </div>

            <!-- SEARCHABLE DROPDOWN ROLE -->
            <div class="relative z-[45]">
                <label class="block font-bold text-gray-700 uppercase mb-1">Role / Peran <span class="text-red-500">*</span></label>
                <div x-data="{
                        search: '', open: false,
                        options: [
                            { id: 'mahasiswa', text: 'Mahasiswa' },
                            { id: 'dosen', text: 'Dosen Pembimbing' },
                            { id: 'spv', text: 'SPV Mitra Lapangan' }
                            @if(!$currentUser->hasRole('admin_prodi'))
                            ,{ id: 'admin_prodi', text: 'Admin Prodi' }
                            @endif
                        ],
                        get filtered() {
                            if (this.search === '') return this.options;
                            return this.options.filter(i => i.text.toLowerCase().includes(this.search.toLowerCase()));
                        },
                        get selectedText() {
                            let st = this.options.find(i => i.id == selectedRole);
                            return st ? st.text : '-- Pilih Role --';
                        }
                    }" @click.away="open = false" class="relative">
                    
                    <input type="hidden" name="role" x-model="selectedRole">
                    <div @click="open = !open" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl flex justify-between items-center cursor-pointer">
                        <span x-text="selectedText" class="font-medium text-gray-700"></span> <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                    
                    <div x-show="open" x-cloak class="absolute w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg flex flex-col overflow-hidden">
                        <div class="p-2 border-b"><input type="text" x-model="search" placeholder="Cari role..." class="w-full px-3 py-2 border rounded-lg focus:outline-none" @click.stop></div>
                        <ul class="max-h-40 overflow-y-auto p-1 custom-scrollbar">
                            <template x-for="opt in filtered" :key="opt.id">
                                <li @click="selectedRole = opt.id; open = false; search = ''" class="px-3 py-2 hover:bg-teal-50 cursor-pointer rounded-lg text-gray-700 font-medium" x-text="opt.text"></li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- SEARCHABLE DROPDOWN PRODI -->
            @if(!$currentUser->hasRole('admin_prodi'))
            <div x-show="selectedRole !== 'spv'" class="relative z-40 pt-1 border-t border-gray-100">
                <label class="block font-bold text-gray-700 uppercase mb-1">Program Studi <span class="text-red-500">*</span></label>
                <div x-data="{
                        formProdiId: '', search: '', open: false,
                        options: [ @foreach($prodis as $p) { id: '{{ $p->id }}', text: '{{ addslashes($p->nama_prodi) }}' }, @endforeach ],
                        get filtered() {
                            if (this.search === '') return this.options;
                            return this.options.filter(i => i.text.toLowerCase().includes(this.search.toLowerCase()));
                        },
                        get selectedText() {
                            let st = this.options.find(i => i.id == this.formProdiId);
                            return st ? st.text : '-- Pilih Program Studi --';
                        }
                    }" @click.away="open = false" class="relative">
                    <input type="hidden" name="prodi_id" x-model="formProdiId" :required="selectedRole !== 'spv'">
                    <div @click="open = !open" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl flex justify-between items-center cursor-pointer">
                        <span x-text="selectedText" class="font-medium" :class="formProdiId ? 'text-gray-700' : 'text-gray-400'"></span> <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                    <div x-show="open" x-cloak class="absolute w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg flex flex-col overflow-hidden">
                        <div class="p-2 border-b"><input type="text" x-model="search" placeholder="Cari program studi..." class="w-full px-3 py-2 border rounded-lg focus:outline-none" @click.stop></div>
                        <ul class="max-h-40 overflow-y-auto p-1 custom-scrollbar">
                            <template x-for="opt in filtered" :key="opt.id">
                                <li @click="formProdiId = opt.id; open = false; search = ''" class="px-3 py-2 hover:bg-teal-50 cursor-pointer rounded-lg text-gray-700 font-medium" x-text="opt.text"></li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <!-- SEARCHABLE DROPDOWN PERUSAHAAN (Khusus SPV) -->
            <div x-show="selectedRole === 'spv'" class="relative z-30 pt-1 border-t border-gray-100">
                <label class="block font-bold text-gray-700 uppercase mb-1">Perusahaan Mitra <span class="text-red-500">*</span></label>
                <div x-data="{
                        formPerusahaanId: '', search: '', open: false,
                        options: [ @foreach($perusahaans as $pt) { id: '{{ $pt->id }}', text: '{{ addslashes($pt->nama_perusahaan) }}' }, @endforeach ],
                        get filtered() {
                            if (this.search === '') return this.options.slice(0, 50); // Tampilkan 50 data awal untuk optimasi
                            return this.options.filter(i => i.text.toLowerCase().includes(this.search.toLowerCase())).slice(0, 50);
                        },
                        get selectedText() {
                            let st = this.options.find(i => i.id == this.formPerusahaanId);
                            return st ? st.text : '-- Pilih Perusahaan Mitra --';
                        }
                    }" @click.away="open = false" class="relative">
                    <input type="hidden" name="perusahaan_id" x-model="formPerusahaanId" :required="selectedRole === 'spv'">
                    <div @click="open = !open" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl flex justify-between items-center cursor-pointer">
                        <span x-text="selectedText" class="font-medium" :class="formPerusahaanId ? 'text-gray-700' : 'text-gray-400'"></span> <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                    <div x-show="open" x-cloak class="absolute w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg flex flex-col overflow-hidden">
                        <div class="p-2 border-b"><input type="text" x-model="search" placeholder="Cari perusahaan..." class="w-full px-3 py-2 border rounded-lg focus:outline-none" @click.stop></div>
                        <ul class="max-h-40 overflow-y-auto p-1 custom-scrollbar">
                            <template x-for="opt in filtered" :key="opt.id">
                                <li @click="formPerusahaanId = opt.id; open = false; search = ''" class="px-3 py-2 hover:bg-teal-50 cursor-pointer rounded-lg text-gray-700 font-medium" x-text="opt.text"></li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Form Dinamis -->
            <div x-show="selectedRole === 'mahasiswa'" class="grid grid-cols-2 gap-3 pt-1 border-t border-gray-100">
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">NIM</label>
                    <input type="text" name="nim" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl bg-gray-50 focus:ring-vokasi-primary outline-none">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Angkatan</label>
                    <input type="text" name="angkatan" value="{{ date('Y') }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl bg-gray-50 focus:ring-vokasi-primary outline-none">
                </div>
            </div>

            <div x-show="selectedRole === 'dosen' || selectedRole === 'admin_prodi'">
                <label class="block font-bold text-gray-700 uppercase mb-1">NIP / NIDN</label>
                <input type="text" name="nip_nidn" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl bg-gray-50 focus:ring-vokasi-primary outline-none">
            </div>

            <div x-show="selectedRole === 'spv'">
                <label class="block font-bold text-gray-700 uppercase mb-1">Jabatan SPV</label>
                <input type="text" name="jabatan" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl bg-gray-50 focus:ring-vokasi-primary outline-none">
            </div>

            <div>
                <label class="block font-bold text-gray-700 uppercase mb-1">No HP / WhatsApp (Opsional)</label>
                <input type="text" name="no_hp" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl bg-gray-50 focus:ring-vokasi-primary outline-none">
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 shrink-0">
                <button type="button" @click="openModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition">Batal</button>
                <button type="submit" class="px-5 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white font-bold rounded-xl shadow-sm transition">Simpan User</button>
            </div>
        </form>
    </div>
</div>