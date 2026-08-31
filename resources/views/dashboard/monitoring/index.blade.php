@extends('layouts.dashboard')

@section('content')
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative custom-scrollbar">
    <div class="max-w-7xl mx-auto w-full flex-1 space-y-6">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Monitoring Sudut Pandang Role</h2>
                <p class="text-sm text-gray-500 mt-1">Pantau apa yang dilihat oleh SPV, Dosen, atau Admin Prodi di layar mereka tanpa meminta password.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden">
            
            <form action="{{ route('dashboard-manajemen-monitoring') }}" method="GET" class="p-5 border-b border-gray-100 flex flex-col md:flex-row justify-between gap-4 bg-gray-50/50">
                <div class="relative w-full md:w-80">
                    <i class="fas fa-search absolute left-3.5 top-3 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20">
                </div>
                
                <div class="flex flex-wrap items-center gap-2">
                    <select name="role" onchange="this.form.submit()" class="bg-white border border-gray-200 text-gray-700 text-xs rounded-xl focus:ring-vokasi-primary outline-none px-4 py-2.5 shadow-sm font-semibold cursor-pointer">
                        <option value="spv" {{ request('role', 'spv') == 'spv' ? 'selected' : '' }}>SPV Mitra Lapangan</option>
                        <option value="dosen" {{ request('role') == 'dosen' ? 'selected' : '' }}>Dosen Pembimbing</option>
                        <option value="admin_prodi" {{ request('role') == 'admin_prodi' ? 'selected' : '' }}>Admin Prodi</option>
                    </select>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100/60 border-b border-gray-200 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                            <th class="p-4 pl-6 w-12">No</th>
                            <th class="p-4">Identitas User</th>
                            <th class="p-4">Unit / Instansi</th>
                            <th class="p-4 text-right pr-6">Aksi Pantau (Impersonate)</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y divide-gray-100 text-gray-700">
                        @forelse($users as $index => $u)
                        @php
                            $instansi = '-';
                            if($u->hasRole('spv')) $instansi = $u->spvProfile->perusahaan->nama_perusahaan ?? '-';
                            if($u->hasRole('dosen')) $instansi = $u->dosenProfile->prodi->nama_prodi ?? '-';
                            if($u->hasRole('admin_prodi')) $instansi = $u->adminProdiProfile->prodi->nama_prodi ?? '-';
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="p-4 pl-6 font-medium">{{ $users->firstItem() + $index }}</td>
                            <td class="p-4">
                                <p class="font-bold text-gray-800 text-sm">{{ $u->name }}</p>
                                <p class="text-gray-400">{{ $u->email }}</p>
                            </td>
                            <td class="p-4 font-bold text-vokasi-primary">
                                <i class="fas fa-building mr-1"></i> {{ $instansi }}
                            </td>
                            <td class="p-4 text-right pr-6 space-x-2">
                                <a href="{{ route('dashboard-verifikasi-daftar-mahasiswa-perlu-verifikasi', ['impersonate_user_id' => $u->id]) }}" class="inline-flex items-center px-3 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 font-bold rounded-xl transition shadow-sm">
                                    <i class="fas fa-tasks mr-1.5"></i> Antrean Pending
                                </a>
                                <a href="{{ route('dashboard-verifikasi-daftar-mahasiswa-terverifikasi', ['impersonate_user_id' => $u->id]) }}" class="inline-flex items-center px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 font-bold rounded-xl transition shadow-sm">
                                    <i class="fas fa-history mr-1.5"></i> Riwayat Verifikasi
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="p-8 text-center text-gray-400">Belum ada data user.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100 bg-white">{{ $users->links() }}</div>
        </div>
    </div>
</main>
@endsection