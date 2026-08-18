@extends('layouts.dashboard')

@section('content')
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 lg:p-8 custom-scrollbar" 
      x-data="{ 
          openModal: false, 
          selectedRole: 'mahasiswa', 
          openProfileModal: false, 
          openEditModal: false,
          editUser: {},
          editRole: 'mahasiswa',
          activeUser: null, 
          openResetForm: false,
          showEditUserModal(userObj) {
              this.editUser = userObj;
              this.editRole = userObj.rawRole;
              this.openEditModal = true;
          }
      }">
    
    <!-- HEADER HALAMAN & TOMBOL AKSI -->
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
        
        <div class="flex flex-wrap items-center gap-2">
            @if($currentUser->hasAnyRole(['admin', 'superadmin', 'admin_prodi']))
            
            <!-- Tombol Group Mass Import Excel -->
            <div x-data="{ openMenu: false }" class="relative">
                <button @click="openMenu = !openMenu" @click.away="openMenu = false" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition">
                    <i class="fas fa-file-excel"></i> Mass Import (Excel) <i class="fas fa-chevron-down text-[10px] ml-1"></i>
                </button>

                <div x-show="openMenu" x-cloak class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden z-50">
                    <a href="{{ route('dashboard-manajemen-aktivasi-template') }}" class="block px-4 py-3 text-xs text-gray-700 hover:bg-gray-50 border-b border-gray-100 font-medium">
                        <i class="fas fa-download text-emerald-600 w-4"></i> 1. Download Template Excel
                    </a>
                    <button type="button" @click="openMenu = false; $dispatch('open-import-modal')" class="w-full text-left px-4 py-3 text-xs text-gray-700 hover:bg-gray-50 font-medium">
                        <i class="fas fa-upload text-blue-600 w-4"></i> 2. Upload Data Excel
                    </button>
                </div>
            </div>

            <!-- Tombol Tambah User Manual -->
            <button @click="openModal = true" class="px-4 py-2.5 bg-vokasi-primary hover:bg-vokasi-dark text-white font-semibold text-xs rounded-xl transition shadow-sm flex items-center gap-2">
                <i class="fas fa-user-plus"></i> Tambah Manual
            </button>
            @endif
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between text-sm shadow-sm">
        <div class="flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900"><i class="fas fa-times"></i></button>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl flex items-center justify-between text-sm shadow-sm">
        <div class="flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-red-600 text-lg"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900"><i class="fas fa-times"></i></button>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-xs shadow-sm">
        <p class="font-bold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Terjadi Kesalahan:</p>
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
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
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-right pr-6">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($users as $user)
                    
                    @php
                        $identitasStr = '-';
                        $instansiStr = 'Fakultas Vokasi UNHAS';
                        $rawRole = $user->roles->first()?->name ?? 'mahasiswa';
                        $prodiIdVal = null;
                        $perusahaanIdVal = null;
                        $nimVal = '';
                        $nipVal = '';
                        $angkatanVal = '';
                        $jabatanVal = '';
                        $noHpVal = '';

                        if($user->mahasiswaProfile) {
                            $identitasStr = 'NIM: ' . $user->mahasiswaProfile->nim;
                            $instansiStr = $user->mahasiswaProfile->masterProdi?->nama_prodi ?? ($user->mahasiswaProfile->prodi?->nama_prodi ?? '-');
                            $prodiIdVal = $user->mahasiswaProfile->prodi_id;
                            $nimVal = $user->mahasiswaProfile->nim;
                            $angkatanVal = $user->mahasiswaProfile->angkatan;
                            $noHpVal = $user->mahasiswaProfile->no_hp;
                        } elseif($user->dosenProfile) {
                            $identitasStr = 'NIP/NIDN: ' . $user->dosenProfile->nip_nidn;
                            $instansiStr = $user->dosenProfile->prodi?->nama_prodi ?? '-';
                            $prodiIdVal = $user->dosenProfile->prodi_id;
                            $nipVal = $user->dosenProfile->nip_nidn;
                            $noHpVal = $user->dosenProfile->no_hp;
                        } elseif($user->spvProfile) {
                            $identitasStr = 'Jabatan: ' . ($user->spvProfile->jabatan ?? '-');
                            $instansiStr = $user->spvProfile->perusahaan?->nama_perusahaan ?? '-';
                            $prodiIdVal = $user->spvProfile->prodi_id;
                            $perusahaanIdVal = $user->spvProfile->perusahaan_id;
                            $jabatanVal = $user->spvProfile->jabatan;
                            $noHpVal = $user->spvProfile->no_hp;
                        } elseif($user->adminProdiProfile) {
                            $identitasStr = 'NIP/NIDN: ' . $user->adminProdiProfile->nip_nidn;
                            $instansiStr = $user->adminProdiProfile->prodi?->nama_prodi ?? '-';
                            $prodiIdVal = $user->adminProdiProfile->prodi_id;
                            $nipVal = $user->adminProdiProfile->nip_nidn;
                        }

                        $roleNames = [];
                        foreach($user->roles as $r) {
                            $roleNames[] = strtoupper(str_replace('_', ' ', $r->name));
                        }
                        $roleStr = implode(', ', $roleNames);

                        $userObjectJson = [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'rawRole' => $rawRole,
                            'prodi_id' => $prodiIdVal,
                            'perusahaan_id' => $perusahaanIdVal,
                            'nim' => $nimVal,
                            'nip_nidn' => $nipVal,
                            'angkatan' => $angkatanVal,
                            'jabatan' => $jabatanVal,
                            'no_hp' => $noHpVal,
                            'updateUrl' => route('dashboard-manajemen-aktivasi-update-profile', $user->id)
                        ];
                    @endphp

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
                                <span class="inline-block px-2.5 py-1 text-[10px] font-bold rounded-lg uppercase tracking-wider
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
                            <span class="font-bold {{ $user->spvProfile ? 'text-amber-700' : ($user->adminProdiProfile ? 'text-indigo-600' : 'text-gray-800') }}">
                                {{ $instansiStr }}
                            </span><br>
                            <span class="text-gray-400">{{ $identitasStr }}</span>
                        </td>
                        <td class="p-4 text-center">
                            @if($user->is_active)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span> Non-Aktif
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-right pr-6 whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                
                                <!-- Tombol EDIT PROFIL (BARU) -->
                                <button type="button" title="Edit Profil Pengguna" @click="showEditUserModal(@js($userObjectJson))" class="px-2.5 py-1.5 bg-gray-100 hover:bg-vokasi-primary hover:text-white text-gray-700 font-bold text-xs rounded-xl border border-gray-200 transition shadow-sm flex items-center gap-1">
                                    <i class="fas fa-edit"></i> Edit
                                </button>

                                <!-- Tombol VIEW PROFIL & RESET PASSWORD -->
                                <button type="button" title="Lihat Profil & Kelola Password" @click="
                                    activeUser = {
                                        id: {{ $user->id }},
                                        name: '{{ addslashes($user->name) }}',
                                        email: '{{ addslashes($user->email) }}',
                                        role: '{{ addslashes($roleStr) }}',
                                        identitas: '{{ addslashes($identitasStr) }}',
                                        instansi: '{{ addslashes($instansiStr) }}',
                                        tempPassword: '{{ addslashes($user->temp_password ?? 'Tidak Tersedia (User Mengubah Sendiri)') }}',
                                        status: {{ $user->is_active ? 'true' : 'false' }},
                                        created_at: '{{ $user->created_at->format('d M Y, H:i') }}',
                                        resetUrl: '{{ route('dashboard-manajemen-aktivasi-reset-password', $user->id) }}'
                                    };
                                    openResetForm = false;
                                    openProfileModal = true;
                                " class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold text-xs rounded-xl border border-blue-200 transition shadow-sm flex items-center gap-1">
                                    <i class="fas fa-eye"></i> Profil & Key
                                </button>

                                <!-- Tombol TOGGLE AKTIVASI -->
                                <form action="{{ route('dashboard-manajemen-aktivasi-toggle', $user->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    @if($user->is_active)
                                        <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs rounded-xl border border-red-200 transition" title="Nonaktifkan User">
                                            Nonaktifkan
                                        </button>
                                    @else
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition" title="Aktifkan User">
                                            Aktifkan
                                        </button>
                                    @endif
                                </form>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-400">
                            <i class="fas fa-users-slash text-3xl mb-2 block"></i> Tidak ada data pengguna ditemukan.
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

    <!-- ========================================================================= -->
    <!-- MODAL POPUP: EDIT PROFIL PENGGUNA (BARU) -->
    <!-- ========================================================================= -->
    <div x-show="openEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="openEditModal = false" class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden border border-gray-100 flex flex-col max-h-[90vh]">
            
            <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-vokasi-primary text-white shrink-0">
                <h3 class="text-base font-bold flex items-center gap-2">
                    <i class="fas fa-user-edit"></i> Edit Profil Pengguna
                </h3>
                <button type="button" @click="openEditModal = false" class="text-white/80 hover:text-white text-lg"><i class="fas fa-times"></i></button>
            </div>

            <form :action="editUser.updateUrl" method="POST" class="p-6 space-y-4 overflow-y-auto custom-scrollbar flex-1 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Nama Lengkap & Gelar <span class="text-red-500">*</span></label>
                    <input type="text" name="name" :value="editUser.name" required placeholder="Nama Lengkap" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Email Resmi <span class="text-red-500">*</span></label>
                    <input type="email" name="email" :value="editUser.email" required placeholder="Email Resmi" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Peran / Role <span class="text-red-500">*</span></label>
                    <select name="role" x-model="editRole" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none font-medium">
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="dosen">Dosen Pembimbing</option>
                        <option value="spv">SPV (Supervisor Mitra Lapangan)</option>
                        @if(!$currentUser->hasRole('admin_prodi'))
                            <option value="admin_prodi">Admin Prodi</option>
                            <option value="admin">Admin</option>
                        @endif
                    </select>
                </div>

                <!-- PILIH PROGRAM STUDI: Tampil untuk role mahasiswa, dosen, admin_prodi, spv -->
                @if(!$currentUser->hasRole('admin_prodi'))
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Program Studi Terdaftar</label>
                    <select name="prodi_id" :value="editUser.prodi_id" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none font-medium">
                        <option value="">-- Pilih Program Studi --</option>
                        @foreach($prodis as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- KHUSUS SPV: PERUSAHAAN & JABATAN -->
                <div x-show="editRole === 'spv'" class="space-y-3 pt-1 border-t border-gray-100">
                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Perusahaan / Instansi Mitra</label>
                        <select name="perusahaan_id" :value="editUser.perusahaan_id" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none font-medium">
                            <option value="">-- Pilih Perusahaan Mitra --</option>
                            @foreach($perusahaans as $pt)
                                <option value="{{ $pt->id }}">{{ $pt->nama_perusahaan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Jabatan di Perusahaan</label>
                        <input type="text" name="jabatan" :value="editUser.jabatan" placeholder="Supervisor Lapangan / Manajer HR" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none">
                    </div>
                </div>

                <!-- KHUSUS MAHASISWA: NIM & ANGKATAN -->
                <div x-show="editRole === 'mahasiswa'" class="grid grid-cols-2 gap-3 pt-1 border-t border-gray-100">
                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">NIM Mahasiswa</label>
                        <input type="text" name="nim" :value="editUser.nim" placeholder="H071231012" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Angkatan</label>
                        <input type="text" name="angkatan" :value="editUser.angkatan" placeholder="2023" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none">
                    </div>
                </div>

                <!-- KHUSUS DOSEN & ADMIN PRODI: NIP / NIDN -->
                <div x-show="editRole === 'dosen' || editRole === 'admin_prodi'" class="pt-1 border-t border-gray-100">
                    <label class="block font-bold text-gray-700 uppercase mb-1">NIP / NIDN</label>
                    <input type="text" name="nip_nidn" :value="editUser.nip_nidn" placeholder="198501012010121001" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none">
                </div>

                <!-- NOMOR WHATSAPP / TELEPON -->
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Nomor WhatsApp / HP (Opsional)</label>
                    <input type="text" name="no_hp" :value="editUser.no_hp" placeholder="081234567890" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none">
                </div>

                <!-- Footer Buttons -->
                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 shrink-0">
                    <button type="button" @click="openEditModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-xs rounded-xl transition">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- MODAL POPUP 1: FORM TAMBAH USER MANUAL -->
    <div x-show="openModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="openModal = false" class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            
            <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h3 class="text-base font-bold text-gray-800"><i class="fas fa-user-plus text-vokasi-primary mr-2"></i> Tambah User Baru</h3>
                <button type="button" @click="openModal = false" class="text-gray-400 hover:text-gray-600 text-lg"><i class="fas fa-times"></i></button>
            </div>

            <form action="{{ route('dashboard-manajemen-aktivasi-store') }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf

                @if($currentUser->hasRole('admin_prodi'))
                <div class="p-3 bg-teal-50 border border-teal-200 rounded-xl flex items-center gap-2.5 text-xs text-vokasi-dark">
                    <i class="fas fa-building text-vokasi-primary text-base shrink-0"></i>
                    <div>
                        <p class="font-bold">Unit Program Studi Otomatis:</p>
                        <p class="font-medium text-gray-700">
                            User baru akan otomatis terdaftar di <strong>{{ $currentUser->adminProdiProfile?->prodi?->nama_prodi ?? 'Program Studi Anda' }}</strong>.
                        </p>
                    </div>
                </div>
                @endif

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Nama Lengkap & Gelar <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Budi Santoso, S.T." class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Email Resmi <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required placeholder="user@vokasi.unhas.ac.id" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none">
                    </div>
                    
                    <div x-data="{ showPassword: false }">
                        <label class="block font-bold text-gray-700 uppercase mb-1">Password Awal <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" name="password" required placeholder="Min. 8 Karakter" class="w-full px-3.5 py-2.5 pr-10 text-xs bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-vokasi-primary outline-none">
                            <button type="button" @click="showPassword = !showPassword" tabindex="-1" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-vokasi-primary">
                                <i class="fas" :class="showPassword ? 'fa-eye-slash text-vokasi-primary' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Peran / Role <span class="text-red-500">*</span></label>
                    <select name="role" x-model="selectedRole" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none font-medium">
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="dosen">Dosen Pembimbing</option>
                        <option value="spv">SPV (Supervisor Mitra Lapangan)</option>
                        @if(!$currentUser->hasRole('admin_prodi'))
                            <option value="admin_prodi">Admin Prodi</option>
                        @endif
                    </select>
                </div>

                @if(!$currentUser->hasRole('admin_prodi'))
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Program Studi <span class="text-red-500">*</span></label>
                    <select name="prodi_id" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none font-medium">
                        <option value="" disabled selected>-- Pilih Program Studi --</option>
                        @foreach($prodis as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div x-show="selectedRole === 'spv'" class="pt-1 border-t border-gray-100">
                    <label class="block font-bold text-gray-700 uppercase mb-1">Perusahaan / Mitra Penempatan <span class="text-red-500">*</span></label>
                    <select name="perusahaan_id" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none font-medium">
                        <option value="" disabled selected>-- Pilih Perusahaan Mitra --</option>
                        @foreach($perusahaans as $pt)
                            <option value="{{ $pt->id }}">{{ $pt->nama_perusahaan }}</option>
                        @endforeach
                    </select>
                </div>

                <div x-show="selectedRole === 'mahasiswa'" class="grid grid-cols-2 gap-3 pt-1 border-t border-gray-100">
                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">NIM Mahasiswa <span class="text-red-500">*</span></label>
                        <input type="text" name="nim" placeholder="H071231012" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Angkatan</label>
                        <input type="text" name="angkatan" value="{{ date('Y') }}" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none">
                    </div>
                </div>

                <div x-show="selectedRole === 'dosen' || selectedRole === 'admin_prodi'" class="pt-1 border-t border-gray-100">
                    <label class="block font-bold text-gray-700 uppercase mb-1">NIP / NIDN <span class="text-red-500">*</span></label>
                    <input type="text" name="nip_nidn" placeholder="198501012010121001" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Nomor WhatsApp / HP</label>
                    <input type="text" name="no_hp" placeholder="081234567890" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-vokasi-primary outline-none">
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                    <button type="button" @click="openModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-xs rounded-xl transition">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white font-bold text-xs rounded-xl shadow-sm transition">Simpan & Aktifkan</button>
                </div>

            </form>
        </div>
    </div>

    <!-- MODAL POPUP 2: UPLOAD EXCEL IMPORT -->
    <div x-data="{ openImportModal: false }" 
         @open-import-modal.window="openImportModal = true"
         x-show="openImportModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div @click.away="openImportModal = false" class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <div class="bg-emerald-600 px-6 py-4 text-white flex justify-between items-center">
                <h3 class="font-bold text-base"><i class="fas fa-file-excel mr-2"></i> Upload Data User (Excel)</h3>
                <button type="button" @click="openImportModal = false" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
            </div>

            <form action="{{ route('dashboard-manajemen-aktivasi-import-preview') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 text-xs">
                @csrf
                <div class="p-3 bg-emerald-50 border border-emerald-100 rounded-xl text-xs text-emerald-800 space-y-1">
                    <p class="font-bold"><i class="fas fa-info-circle mr-1"></i> Informasi Petunjuk:</p>
                    <p>Unggah berkas <strong>Template Excel (.xlsx)</strong> yang telah Anda unduh dan isi data penggunanya secara lengkap.</p>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-2">Unggah File Excel <span class="text-red-500">*</span></label>
                    <input type="file" name="file_excel" required accept=".xlsx, .xls" class="w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                    <button type="button" @click="openImportModal = false" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 bg-white font-bold text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs shadow-sm flex items-center gap-2">
                        <i class="fas fa-eye"></i> Tampilkan Preview
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL POPUP 3: DETAIL PROFIL USER & RESET PASSWORD -->
    <div x-show="openProfileModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="openProfileModal = false" class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden border border-gray-100" x-if="activeUser">
            
            <div class="relative bg-vokasi-primary p-6 pb-12 flex justify-between items-start text-white">
                <h3 class="font-bold text-base"><i class="fas fa-id-badge mr-2"></i> Detail Pengguna</h3>
                <button type="button" @click="openProfileModal = false" class="text-white/80 hover:text-white transition"><i class="fas fa-times text-lg"></i></button>
            </div>

            <div class="px-6 relative -mt-10 mb-4 text-center">
                <img :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent(activeUser?.name || 'User') + '&background=f8f9fa&color=37A7AC&size=128'" 
                     alt="Avatar" 
                     class="w-20 h-20 rounded-full border-4 border-white shadow-md mx-auto bg-white">
                <h4 class="font-bold text-lg text-gray-800 mt-2 leading-tight" x-text="activeUser?.name"></h4>
                <p class="text-xs text-vokasi-primary font-semibold mt-0.5" x-text="activeUser?.role"></p>
                
                <div class="mt-2">
                    <template x-if="activeUser?.status">
                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <i class="fas fa-check-circle mr-1"></i> AKUN AKTIF
                        </span>
                    </template>
                    <template x-if="!activeUser?.status">
                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                            <i class="fas fa-exclamation-triangle mr-1"></i> NON-AKTIF (DITANGGUHKAN)
                        </span>
                    </template>
                </div>
            </div>

            <div class="px-6 pb-8 space-y-4 text-xs">
                <div class="p-4 bg-gray-50 border border-gray-100 rounded-2xl space-y-3">
                    <div class="flex flex-col border-b border-gray-200/60 pb-2">
                        <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Email Resmi</span>
                        <span class="font-medium text-gray-800 text-xs" x-text="activeUser?.email"></span>
                    </div>
                    <div class="flex flex-col border-b border-gray-200/60 pb-2">
                        <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Program Studi / Instansi Mitra</span>
                        <span class="font-medium text-gray-800 text-xs" x-text="activeUser?.instansi"></span>
                    </div>
                    <div class="flex flex-col border-b border-gray-200/60 pb-2">
                        <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Identitas Akademik / Jabatan</span>
                        <span class="font-mono font-medium text-gray-800 text-xs" x-text="activeUser?.identitas"></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Waktu Registrasi Akun</span>
                        <span class="font-medium text-gray-600 text-xs" x-text="activeUser?.created_at"></span>
                    </div>
                </div>

                <div class="p-4 bg-amber-50/70 border border-amber-200 rounded-2xl space-y-3" x-data="{ showRecentPass: false }">
                    <div class="flex items-center justify-between border-b border-amber-200/60 pb-2">
                        <span class="text-xs font-bold text-amber-900 flex items-center gap-1.5">
                            <i class="fas fa-key text-amber-600"></i> Password Terakhir / Awal:
                        </span>
                        <button type="button" @click="openResetForm = !openResetForm" class="text-xs font-bold text-vokasi-primary hover:underline">
                            <span x-text="openResetForm ? 'Batal' : 'Reset Password Baru'"></span>
                        </button>
                    </div>

                    <div class="flex items-center justify-between bg-white px-3 py-2 rounded-xl border border-amber-200 text-xs" x-show="!openResetForm">
                        <span class="font-mono font-bold text-gray-800" x-text="showRecentPass ? activeUser?.tempPassword : '••••••••••••'"></span>
                        <button type="button" @click="showRecentPass = !showRecentPass" class="text-gray-400 hover:text-vokasi-primary transition-colors focus:outline-none ml-2">
                            <i class="fas" :class="showRecentPass ? 'fa-eye-slash text-vokasi-primary' : 'fa-eye'"></i>
                        </button>
                    </div>

                    <form :action="activeUser?.resetUrl" method="POST" x-show="openResetForm" x-cloak class="space-y-3 pt-2" x-data="{ showNewPass: false }">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">Ketik Password Baru <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input :type="showNewPass ? 'text' : 'password'" name="new_password" required placeholder="Min. 8 Karakter" class="w-full px-3.5 py-2 pr-10 text-xs bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-vokasi-primary outline-none">
                                <button type="button" @click="showNewPass = !showNewPass" tabindex="-1" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-vokasi-primary">
                                    <i class="fas" :class="showNewPass ? 'fa-eye-slash text-vokasi-primary' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5">
                                <i class="fas fa-save"></i> Simpan Password Baru
                            </button>
                        </div>
                    </form>
                </div>

                <div class="text-center pt-2">
                    <button type="button" @click="openProfileModal = false" class="w-full px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition">
                        Tutup Profil
                    </button>
                </div>
            </div>

        </div>
    </div>

</main>
@endsection