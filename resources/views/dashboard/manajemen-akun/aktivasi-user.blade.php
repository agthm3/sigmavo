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
          openImportModal: false,
          showEditUserModal(userObj) {
              this.editUser = userObj;
              this.editRole = userObj.rawRole;
              this.openEditModal = true;
          }
      }" @open-import-modal.window="openImportModal = true">
    
    <!-- HEADER HALAMAN & TOMBOL AKSI -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Aktivasi & Kontrol Akses User</h1>
            <p class="text-sm text-gray-500 mt-1">
                @if($currentUser->hasRole('admin_prodi'))
                    Menampilkan daftar pengguna untuk <strong>{{ $currentUser->adminProdiProfile?->prodi?->nama_prodi }}</strong>
                @else
                    Kelola status aktif akun pengguna seluruh Fakultas Vokasi.
                @endif
            </p>
        </div>
        
        <div class="flex flex-wrap items-center gap-2">
            @if($currentUser->hasAnyRole(['admin', 'superadmin', 'admin_prodi']))
            <div x-data="{ openMenu: false }" class="relative">
                <button @click="openMenu = !openMenu" @click.away="openMenu = false" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition">
                    <i class="fas fa-file-excel"></i> Mass Import (Excel) <i class="fas fa-chevron-down text-[10px] ml-1"></i>
                </button>
                <div x-show="openMenu" x-cloak class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden z-50">
                    <a href="{{ route('dashboard-manajemen-aktivasi-template') }}" class="block px-4 py-3 text-xs text-gray-700 hover:bg-gray-50 border-b border-gray-100 font-medium">
                        <i class="fas fa-download text-emerald-600 w-4"></i> 1. Download Template
                    </a>
                    <button type="button" @click="openMenu = false; $dispatch('open-import-modal')" class="w-full text-left px-4 py-3 text-xs text-gray-700 hover:bg-gray-50 font-medium">
                        <i class="fas fa-upload text-blue-600 w-4"></i> 2. Upload Data Excel
                    </button>
                </div>
            </div>

            <button @click="openModal = true" class="px-4 py-2.5 bg-vokasi-primary hover:bg-vokasi-dark text-white font-semibold text-xs rounded-xl transition shadow-sm flex items-center gap-2">
                <i class="fas fa-user-plus"></i> Tambah Manual
            </button>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between text-sm shadow-sm">
        <div class="flex items-center gap-2"><i class="fas fa-check-circle text-emerald-600 text-lg"></i><span>{{ session('success') }}</span></div>
        <button onclick="this.parentElement.remove()" class="text-emerald-600"><i class="fas fa-times"></i></button>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl flex items-center justify-between text-sm shadow-sm">
        <div class="flex items-center gap-2"><i class="fas fa-exclamation-circle text-red-600 text-lg"></i><span>{{ session('error') }}</span></div>
        <button onclick="this.parentElement.remove()" class="text-red-600"><i class="fas fa-times"></i></button>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-4 mb-6">
        <form action="{{ route('dashboard-manajemen-aktivasi-user') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                @if(!$currentUser->hasRole('admin_prodi'))
                <select name="prodi_id" onchange="this.form.submit()" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20">
                    <option value="semua">Semua Program Studi</option>
                    @foreach($prodis as $p)
                        <option value="{{ $p->id }}" {{ request('prodi_id') == $p->id ? 'selected' : '' }}>{{ $p->nama_prodi }}</option>
                    @endforeach
                </select>
                @endif
                <select name="role" onchange="this.form.submit()" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20">
                    <option value="semua" {{ request('role') == 'semua' ? 'selected' : '' }}>Semua Role</option>
                    <option value="mahasiswa" {{ request('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="dosen" {{ request('role') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                    <option value="spv" {{ request('role') == 'spv' ? 'selected' : '' }}>SPV Mitra</option>
                </select>
                <select name="status" onchange="this.form.submit()" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20">
                    <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}>Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Non-Aktif</option>
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
                        $identitasStr = '-'; $instansiStr = 'Fakultas Vokasi UNHAS';
                        $rawRole = $user->roles->first()?->name ?? 'mahasiswa';
                        $prodiIdVal = null; $perusahaanIdVal = null; $nimVal = ''; $nipVal = ''; 
                        $angkatanVal = ''; $jabatanVal = ''; $noHpVal = ''; // Variabel Tambahan

                        if($user->mahasiswaProfile) {
                            $identitasStr = 'NIM: ' . $user->mahasiswaProfile->nim;
                            $instansiStr = $user->mahasiswaProfile->masterProdi?->nama_prodi ?? ($user->mahasiswaProfile->prodi?->nama_prodi ?? '-');
                            $prodiIdVal = $user->mahasiswaProfile->prodi_id; $nimVal = $user->mahasiswaProfile->nim;
                            $angkatanVal = $user->mahasiswaProfile->angkatan; $noHpVal = $user->mahasiswaProfile->no_hp;
                        } elseif($user->dosenProfile) {
                            $identitasStr = 'NIP/NIDN: ' . $user->dosenProfile->nip_nidn;
                            $instansiStr = $user->dosenProfile->prodi?->nama_prodi ?? '-';
                            $prodiIdVal = $user->dosenProfile->prodi_id; $nipVal = $user->dosenProfile->nip_nidn;
                            $noHpVal = $user->dosenProfile->no_hp;
                        } elseif($user->spvProfile) {
                            $identitasStr = 'Jabatan: ' . ($user->spvProfile->jabatan ?? '-');
                            $instansiStr = $user->spvProfile->perusahaan?->nama_perusahaan ?? '-';
                            $perusahaanIdVal = $user->spvProfile->perusahaan_id;
                            $jabatanVal = $user->spvProfile->jabatan; $noHpVal = $user->spvProfile->no_hp;
                        } elseif($user->adminProdiProfile) {
                            $identitasStr = 'NIP/NIDN: ' . $user->adminProdiProfile->nip_nidn;
                            $instansiStr = $user->adminProdiProfile->prodi?->nama_prodi ?? '-';
                            $prodiIdVal = $user->adminProdiProfile->prodi_id; $nipVal = $user->adminProdiProfile->nip_nidn;
                        }

                        // JSON Object untuk Alpine.js (Diupdate lengkap)
                        $userObjectJson = [
                            'id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'rawRole' => $rawRole,
                            'prodi_id' => $prodiIdVal, 'perusahaan_id' => $perusahaanIdVal, 
                            'nim' => $nimVal, 'nip_nidn' => $nipVal, 'angkatan' => $angkatanVal, 'jabatan' => $jabatanVal, 'no_hp' => $noHpVal,
                            'roleStr' => strtoupper(str_replace('_', ' ', $rawRole)),
                            'identitas' => $identitasStr, 'instansi' => $instansiStr,
                            'tempPassword' => $user->temp_password ?? 'Sudah Diubah (Gunakan Reset)', 'status' => $user->is_active,
                            'created_at' => $user->created_at->format('d M Y, H:i'),
                            'resetUrl' => route('dashboard-manajemen-aktivasi-reset-password', $user->id),
                            'updateUrl' => route('dashboard-manajemen-aktivasi-update-profile', $user->id),
                            'riwayat_magang' => $user->riwayat_magang ?? []
                        ];
                    @endphp

                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-4 pl-6">
                            <div class="flex items-center space-x-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=37A7AC&color=fff" class="w-9 h-9 rounded-full border border-gray-200">
                                <div>
                                    <p class="font-bold text-gray-800">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            <span class="inline-block px-2.5 py-1 text-[10px] font-bold rounded-lg uppercase tracking-wider bg-gray-100 text-gray-800">
                                {{ str_replace('_', ' ', $rawRole) }}
                            </span>
                        </td>
                        <td class="p-4 text-xs font-medium text-gray-600">
                            <span class="font-bold text-vokasi-dark">{{ $instansiStr }}</span><br>
                            <span class="text-gray-400">{{ $identitasStr }}</span>
                        </td>
                        <td class="p-4 text-center">
                            @if($user->is_active)
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="p-4 text-right pr-6 whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <button type="button" @click="showEditUserModal(@js($userObjectJson))" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl border border-gray-200 transition"><i class="fas fa-edit"></i> Edit</button>
                                <button type="button" @click="activeUser = @js($userObjectJson); openProfileModal = true; openResetForm = false;" class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold text-xs rounded-xl border border-blue-200 transition"><i class="fas fa-eye"></i> Detail</button>
                                
                                <form action="{{ route('dashboard-manajemen-aktivasi-toggle', $user->id) }}" method="POST" class="inline-block">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 {{ $user->is_active ? 'bg-red-50 text-red-600 border-red-200' : 'bg-emerald-600 text-white' }} font-bold text-xs rounded-xl border transition">
                                        {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="p-8 text-center text-gray-400">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">{{ $users->links() }}</div>
    </div>

    @include('dashboard.manajemen-akun.partials.modal-edit')
    @include('dashboard.manajemen-akun.partials.modal-tambah')
    @include('dashboard.manajemen-akun.partials.modal-import')
    @include('dashboard.manajemen-akun.partials.modal-detail')

</main>
@endsection