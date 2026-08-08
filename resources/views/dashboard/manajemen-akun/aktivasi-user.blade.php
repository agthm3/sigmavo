@extends('layouts.dashboard')

@section('content')
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 lg:p-8 custom-scrollbar" x-data="{ openModal: false, selectedRole: 'mahasiswa' }">
    
    <!-- HEADER HALAMAN & TOMBOL TAMBAH -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Aktivasi & Kontrol Akses User</h1>
            <p class="text-sm text-gray-500 mt-1">
                @if($currentUser->hasRole('admin_prodi'))
                    Menampilkan daftar pengguna untuk <strong>{{ $currentUser->adminProdiProfile?->prodi?->nama_prodi }}</strong>
                @else
                    Kelola status aktif akun pengguna (Mahasiswa, Dosen, SPV Mitra, Admin Prodi) seluruh Fakultas Vokasi.
                @endif
            </p>
        </div>
        
        <div class="flex items-center gap-3">
            @if($currentUser->hasAnyRole(['admin', 'superadmin', 'admin_prodi']))
            <button @click="openModal = true" class="px-4 py-2.5 bg-vokasi-primary hover:bg-vokasi-dark text-white font-semibold text-sm rounded-xl transition shadow-sm flex items-center gap-2">
                <i class="fas fa-user-plus"></i> Tambah User Baru
            </button>
            @endif
        </div>
    </div>

    <!-- NOTIFIKASI ERROR VALIDASI -->
    @if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-sm">
        <p class="font-bold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal menambahkan user baru:</p>
        <ul class="list-disc list-inside space-y-1 text-xs">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- NOTIFIKASI SUKSES / ERROR -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between text-sm">
        <div class="flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900"><i class="fas fa-times"></i></button>
    </div>
    @endif

    <!-- FILTER BAR -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-4 mb-6">
        <form action="{{ route('dashboard-manajemen-aktivasi-user') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                @if(!$currentUser->hasRole('admin_prodi'))
                <select name="prodi_id" onchange="this.form.submit()" class="w-full sm:w-auto px-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20">
                    <option value="semua" {{ request('prodi_id') == 'semua' ? 'selected' : '' }}>Semua Program Studi</option>
                    @foreach($prodis as $p)
                        <option value="{{ $p->id }}" {{ request('prodi_id') == $p->id ? 'selected' : '' }}>{{ $p->nama_prodi }}</option>
                    @endforeach
                </select>
                @endif

                <select name="role" onchange="this.form.submit()" class="w-full sm:w-auto px-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20">
                    <option value="semua" {{ request('role') == 'semua' ? 'selected' : '' }}>Semua Role</option>
                    <option value="mahasiswa" {{ request('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="dosen" {{ request('role') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                    <option value="spv" {{ request('role') == 'spv' ? 'selected' : '' }}>SPV Mitra Lapangan</option>
                    <option value="admin_prodi" {{ request('role') == 'admin_prodi' ? 'selected' : '' }}>Admin Prodi</option>
                </select>

                <select name="status" onchange="this.form.submit()" class="w-full sm:w-auto px-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20">
                    <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}>Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Non-Aktif / Pending</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                </select>
            </div>

            <div class="relative w-full md:w-72">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." class="w-full pl-10 pr-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20 transition-colors">
                <i class="fas fa-search absolute left-3.5 top-3 text-gray-400 text-xs"></i>
            </div>
        </form>
    </div>

    <!-- TABEL USER -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-800">Daftar Pengguna Registrasi</h2>
            <span class="text-xs font-semibold text-gray-500">Total: {{ $users->total() }} Pengguna</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-[11px] font-bold tracking-wider">
                        <th class="p-4 pl-6">Pengguna</th>
                        <th class="p-4">Role / Peran</th>
                        <th class="p-4">Program Studi / Instansi</th>
                        <th class="p-4">Status Akun</th>
                        <th class="p-4 text-right pr-6">Aksi Aktivasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-4 pl-6">
                            <div class="flex items-center space-x-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=37A7AC&color=fff" alt="Avatar" class="w-9 h-9 rounded-full border border-gray-200">
                                <div>
                                    <p class="font-bold text-gray-800">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            @foreach($user->roles as $role)
                                <span class="inline-block px-2.5 py-1 text-xs font-bold rounded-lg uppercase tracking-wider
                                    {{ $role->name == 'admin' ? 'bg-purple-100 text-purple-700' : '' }}
                                    {{ $role->name == 'admin_prodi' ? 'bg-indigo-100 text-indigo-700' : '' }}
                                    {{ $role->name == 'dosen' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $role->name == 'mahasiswa' ? 'bg-teal-100 text-vokasi-dark' : '' }}
                                    {{ $role->name == 'spv' ? 'bg-amber-100 text-amber-800 border border-amber-200' : '' }}">
                                    {{ str_replace('_', ' ', $role->name) }}
                                </span>
                            @endforeach
                        </td>
                        <td class="p-4 text-xs font-medium text-gray-600">
                            @if($user->mahasiswaProfile)
                                <span class="font-bold text-gray-800">{{ $user->mahasiswaProfile->prodi?->nama_prodi ?? '-' }}</span><br>
                                <span class="text-gray-400">NIM: {{ $user->mahasiswaProfile->nim }}</span>
                            @elseif($user->dosenProfile)
                                <span class="font-bold text-gray-800">{{ $user->dosenProfile->prodi?->nama_prodi ?? '-' }}</span><br>
                                <span class="text-gray-400">NIP: {{ $user->dosenProfile->nip_nidn }}</span>
                            @elseif($user->spvProfile)
                                <span class="font-bold text-amber-700">{{ $user->spvProfile->perusahaan?->nama_perusahaan ?? '-' }}</span><br>
                                <span class="text-gray-500">Prodi: {{ $user->spvProfile->prodi?->nama_prodi ?? '-' }}</span>
                            @elseif($user->adminProdiProfile)
                                <span class="font-bold text-indigo-600">{{ $user->adminProdiProfile->prodi?->nama_prodi ?? '-' }}</span>
                            @else
                                <span class="text-gray-400">Fakultas Vokasi</span>
                            @endif
                        </td>
                        <td class="p-4">
                            @if($user->is_active)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span> Non-Aktif
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-right pr-6">
                            <form action="{{ route('dashboard-manajemen-aktivasi-toggle', $user->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PATCH')
                                @if($user->is_active)
                                    <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 font-semibold text-xs rounded-lg border border-red-200">
                                        Nonaktifkan
                                    </button>
                                @else
                                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-lg shadow-sm">
                                        Aktifkan
                                    </button>
                                @endif
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-400">
                            Tidak ada data pengguna ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $users->links() }}
        </div>
    </div>

    <!-- MODAL POPUP: FORM TAMBAH USER BARU -->
    <div x-show="openModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="openModal = false" class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            
            <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-user-plus text-vokasi-primary mr-2"></i> Tambah User Baru</h3>
                <button @click="openModal = false" class="text-gray-400 hover:text-gray-600 text-lg"><i class="fas fa-times"></i></button>
            </div>

            <form action="{{ route('dashboard-manajemen-aktivasi-store') }}" method="POST" class="p-6 space-y-4">
                @csrf

                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Lengkap & Gelar <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Budi Santoso, S.T. (Supervisor)" class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none">
                </div>

                <!-- Email & Password -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email Resmi <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required placeholder="user@perusahaan.com" class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Password Awal <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required placeholder="Min. 8 Karakter" class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none">
                    </div>
                </div>

                <!-- Role Selection -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Peran / Role <span class="text-red-500">*</span></label>
                    <select name="role" x-model="selectedRole" required class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none">
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="dosen">Dosen Pembimbing</option>
                        <option value="spv">SPV (Supervisor Mitra Lapangan)</option>
                        @if(!$currentUser->hasRole('admin_prodi'))
                            <option value="admin_prodi">Admin Studi / Prodi</option>
                        @endif
                    </select>
                </div>

                <!-- Program Studi Terkait -->
                @if(!$currentUser->hasRole('admin_prodi'))
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Program Studi <span class="text-red-500">*</span></label>
                    <select name="prodi_id" required class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none">
                        <option value="" disabled selected>-- Pilih Program Studi --</option>
                        @foreach($prodis as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Perusahaan (Khusus Role SPV) -->
                <div x-show="selectedRole === 'spv'" class="pt-1 border-t border-gray-100">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Perusahaan / Mitra Penempatan <span class="text-red-500">*</span></label>
                    <select name="perusahaan_id" class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none">
                        <option value="" disabled selected>-- Pilih Perusahaan Mitra --</option>
                        @foreach($perusahaans as $pt)
                            <option value="{{ $pt->id }}">{{ $pt->nama_perusahaan }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- FIELD DINAMIS LAINNYA -->
                <div x-show="selectedRole === 'mahasiswa'" class="grid grid-cols-2 gap-3 pt-1 border-t border-gray-100">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">NIM Mahasiswa <span class="text-red-500">*</span></label>
                        <input type="text" name="nim" placeholder="H071231012" class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Angkatan</label>
                        <input type="text" name="angkatan" value="{{ date('Y') }}" class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none">
                    </div>
                </div>

                <div x-show="selectedRole === 'dosen' || selectedRole === 'admin_prodi'" class="pt-1 border-t border-gray-100">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">NIP / NIDN Dosen <span class="text-red-500">*</span></label>
                    <input type="text" name="nip_nidn" placeholder="198501012010121001" class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none">
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="openModal = false" class="px-4 py-2 bg-gray-100 text-gray-600 font-semibold text-xs rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white font-semibold text-xs rounded-xl shadow-sm">Simpan & Aktifkan</button>
                </div>

            </form>
        </div>
    </div>

</main>
@endsection