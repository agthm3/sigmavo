@extends('layouts.dashboard')

@section('content')
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 lg:p-8 custom-scrollbar" x-data="{ openAddModal: false, openEditModal: false, activeRole: null, activePermissions: [] }">
    
    <!-- HEADER HALAMAN -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Manajemen Jenis User & Hak Akses (Role)</h1>
            <p class="text-sm text-gray-500 mt-1">Atur jenis peran pengguna dan kelola otorisasi (*permission*) fitur di portal SIGMAVO.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <button type="button" @click="openAddModal = true" class="px-4 py-2.5 bg-vokasi-primary hover:bg-vokasi-dark text-white font-semibold text-sm rounded-xl transition shadow-sm hover:shadow-md flex items-center gap-2">
                <i class="fas fa-plus-circle"></i> Tambah Role Baru
            </button>
        </div>
    </div>

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

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl flex items-center justify-between text-sm">
        <div class="flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-red-600 text-lg"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900"><i class="fas fa-times"></i></button>
    </div>
    @endif

    <!-- GRID CARDS DAFTAR ROLE -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($roles as $role)
        @php
            $isSystemRole = in_array($role->name, ['admin', 'superadmin', 'admin_prodi', 'dosen', 'mahasiswa', 'mitra']);
        @endphp
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="px-3 py-1 text-xs font-bold uppercase rounded-lg tracking-wider
                        {{ $role->name == 'admin' ? 'bg-purple-100 text-purple-700' : '' }}
                        {{ $role->name == 'admin_prodi' ? 'bg-indigo-100 text-indigo-700' : '' }}
                        {{ $role->name == 'dosen' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $role->name == 'mahasiswa' ? 'bg-teal-100 text-vokasi-dark' : '' }}
                        {{ $role->name == 'mitra' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700' }}">
                        {{ str_replace('_', ' ', $role->name) }}
                    </span>

                    @if($isSystemRole)
                        <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded">Sistem</span>
                    @else
                        <form action="{{ route('dashboard-manajemen-jenis-role-destroy', $role->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus role ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-500 text-xs transition" title="Hapus Role">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    @endif
                </div>

                <h3 class="text-xl font-extrabold text-gray-800 tracking-tight">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</h3>
                <p class="text-xs text-gray-500 mt-1">
                    Memiliki <strong>{{ $role->users_count }}</strong> pengguna aktif terdaftar.
                </p>

                <!-- Daftar Hak Akses Terpasang -->
                <div class="mt-4 pt-4 border-t border-gray-100 space-y-1.5">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Otorisasi / Permission:</p>
                    <div class="flex flex-wrap gap-1">
                        @forelse($role->permissions as $perm)
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] font-medium rounded-md">
                                {{ $perm->name }}
                            </span>
                        @empty
                            <span class="text-xs text-gray-400 italic">Akses penuh bawaan / Belum diset.</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-400">ID Role: #{{ $role->id }}</span>
                <button type="button" 
                        @click="activeRole = {{ json_encode($role) }}; activePermissions = {{ json_encode($role->permissions->pluck('name')) }}; openEditModal = true" 
                        class="text-xs font-bold text-vokasi-primary hover:text-vokasi-dark flex items-center gap-1">
                    <i class="fas fa-cog"></i> Kelola Hak Akses
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <!-- ========================================== -->
    <!-- MODAL 1: TAMBAH ROLE BARU -->
    <!-- ========================================== -->
    <div x-show="openAddModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div @click.away="openAddModal = false" class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-plus-circle text-vokasi-primary mr-2"></i> Tambah Jenis Role Baru</h3>
                <button @click="openAddModal = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>

            <form action="{{ route('dashboard-manajemen-jenis-role-store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Role Baru <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Koordinator Lapangan" class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-vokasi-primary/20 focus:outline-none">
                    <p class="text-[11px] text-gray-400 mt-1">Nama role akan disesuaikan otomatis menjadi format kode sistem.</p>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2 bg-gray-100 text-gray-600 font-semibold text-xs rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white font-semibold text-xs rounded-xl shadow-sm">Simpan Role</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 2: KELOLA PERMISSION ROLE -->
    <!-- ========================================== -->
    <div x-show="openEditModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        
        <div @click.away="openEditModal = false" class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden border border-gray-100" x-if="activeRole">
            
            <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Kelola Hak Akses / Permission</h3>
                    <p class="text-xs text-vokasi-primary font-bold mt-0.5" x-text="'Role: ' + activeRole?.name"></p>
                </div>
                <button @click="openEditModal = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>

            <form :action="'{{ url('/manajemen-akun/jenis-role') }}/' + activeRole?.id + '/permissions'" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Pilih Akses Modul yang Diizinkan:</label>
                    <div class="space-y-2 max-h-60 overflow-y-auto custom-scrollbar p-2 bg-gray-50 rounded-xl border border-gray-200">
                        @forelse($permissions as $perm)
                        <label class="flex items-center gap-3 p-2 bg-white rounded-lg border border-gray-100 hover:bg-gray-50 cursor-pointer text-xs">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" :checked="activePermissions.includes('{{ $perm->name }}')" class="rounded border-gray-300 text-vokasi-primary focus:ring-vokasi-primary">
                            <span class="font-medium text-gray-700">{{ $perm->name }}</span>
                        </label>
                        @empty
                        <p class="text-xs text-gray-400 p-2 text-center">Belum ada daftar permission sistem. Tambahkan via Seeder / Spatie.</p>
                        @endforelse
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="openEditModal = false" class="px-4 py-2 bg-gray-100 text-gray-600 font-semibold text-xs rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white font-semibold text-xs rounded-xl shadow-sm">Simpan Perubahan</button>
                </div>
            </form>

        </div>
    </div>

</main>
@endsection