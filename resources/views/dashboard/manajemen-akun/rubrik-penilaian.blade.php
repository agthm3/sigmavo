@extends('layouts.dashboard')

@section('content')
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 lg:p-8 custom-scrollbar" 
      x-data="{ openAddModal: false, openEditModal: false, activeRubrik: {}, activeUrl: '' }">
    
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Rubrik Penilaian Global</h1>
            <p class="text-sm text-gray-500 mt-1">Atur standar komponen penilaian akhir mahasiswa magang untuk seluruh program studi.</p>
        </div>
        <button @click="openAddModal = true" class="bg-vokasi-primary hover:bg-vokasi-dark text-white text-sm font-bold py-2 px-4 rounded-xl shadow-sm transition-colors flex items-center gap-2">
            <i class="fas fa-plus"></i> Tambah Komponen
        </button>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex justify-between text-sm">
        <div class="flex items-center gap-2"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        <button onclick="this.parentElement.remove()" class="text-emerald-600"><i class="fas fa-times"></i></button>
    </div>
    @endif

    <!-- INDIKATOR TOTAL BOBOT (WAJIB 100%) -->
    <div class="mb-6 p-5 rounded-2xl border flex items-center justify-between shadow-sm
        {{ $isBobotValid ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200' }}">
        <div>
            <h3 class="font-bold {{ $isBobotValid ? 'text-emerald-800' : 'text-red-800' }} text-lg">
                <i class="fas {{ $isBobotValid ? 'fa-check-circle' : 'fa-exclamation-triangle' }} mr-2"></i>
                Total Bobot Penilaian: {{ $totalBobot }}%
            </h3>
            <p class="text-sm {{ $isBobotValid ? 'text-emerald-600' : 'text-red-600' }} mt-1 font-medium">
                {{ $isBobotValid ? 'Sistem siap digunakan. Distribusi bobot sudah ideal (100%).' : 'PERINGATAN: Total akumulasi bobot harus tepat 100%. Sistem penilaian akhir tidak akan berjalan akurat jika belum genap 100%.' }}
            </p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-[11px] font-bold tracking-wider">
                        <th class="p-4 text-center w-16">No</th>
                        <th class="p-4 w-64">Komponen Penilaian</th>
                        <th class="p-4">Indikator Penilaian</th>
                        <th class="p-4 text-center w-32">Bobot (%)</th>
                        <th class="p-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rubriks as $rubrik)
                    <tr class="hover:bg-gray-50">
                        <td class="p-4 text-center font-bold text-gray-500">{{ $rubrik->no_urut }}</td>
                        <td class="p-4 font-bold text-gray-800">{{ $rubrik->komponen }}</td>
                        <td class="p-4 text-xs text-gray-600 leading-relaxed">{{ $rubrik->indikator }}</td>
                        <td class="p-4 text-center font-extrabold text-vokasi-primary text-base">{{ floatval($rubrik->bobot) }}%</td>
                        <td class="p-4 text-center space-x-2">
                            <button @click="
                                        activeRubrik.no = '{{ $rubrik->no_urut }}';
                                        activeRubrik.komponen = '{{ addslashes($rubrik->komponen) }}';
                                        activeRubrik.indikator = '{{ addslashes($rubrik->indikator) }}';
                                        activeRubrik.bobot = '{{ floatval($rubrik->bobot) }}';
                                        activeUrl = '{{ route('dashboard-manajemen-rubrik-penilaian-update', $rubrik->id) }}';
                                        openEditModal = true;
                                    " 
                                    class="text-gray-400 hover:text-blue-500 transition-colors p-1" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('dashboard-manajemen-rubrik-penilaian-destroy', $rubrik->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus komponen penilaian ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors p-1" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-400">
                            <i class="fas fa-list-alt text-3xl mb-2 block"></i> Belum ada data Rubrik Penilaian.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL TAMBAH RUBRIK -->
    <div x-show="openAddModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.away="openAddModal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="bg-vokasi-primary p-4 text-white flex justify-between items-center">
                <h3 class="font-bold">Tambah Komponen Penilaian</h3>
                <button @click="openAddModal = false" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('dashboard-manajemen-rubrik-penilaian-store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="flex gap-4">
                    <div class="w-1/4">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">No. Urut</label>
                        <input type="number" name="no_urut" required class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:ring-vokasi-primary text-center">
                    </div>
                    <div class="w-3/4">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Komponen Penilaian</label>
                        <input type="text" name="komponen" placeholder="Contoh: Disiplin & Etika" required class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:ring-vokasi-primary">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Bobot (%)</label>
                    <input type="number" step="0.01" name="bobot" placeholder="Contoh: 15" required class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:ring-vokasi-primary">
                    <p class="text-[10px] text-gray-400 mt-1">Sisa limit bobot yang bisa diisi: <strong class="text-red-500">{{ 100 - $totalBobot }}%</strong></p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Indikator Penilaian</label>
                    <textarea name="indikator" rows="3" required placeholder="Jelaskan detail yang dinilai..." class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-300 rounded-xl resize-none focus:ring-vokasi-primary"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2 border rounded-xl text-xs font-bold">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-xl text-xs font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT RUBRIK -->
    <div x-show="openEditModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.away="openEditModal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="bg-gray-800 p-4 text-white flex justify-between items-center">
                <h3 class="font-bold">Edit Komponen Penilaian</h3>
                <button @click="openEditModal = false" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            <form :action="activeUrl" method="POST" class="p-6 space-y-4">
                @csrf @method('PUT')
                <div class="flex gap-4">
                    <div class="w-1/4">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">No. Urut</label>
                        <input type="number" name="no_urut" x-model="activeRubrik.no" required class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-300 rounded-xl text-center">
                    </div>
                    <div class="w-3/4">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Komponen Penilaian</label>
                        <input type="text" name="komponen" x-model="activeRubrik.komponen" required class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-300 rounded-xl">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Bobot (%)</label>
                    <input type="number" step="0.01" name="bobot" x-model="activeRubrik.bobot" required class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-300 rounded-xl">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Indikator Penilaian</label>
                    <textarea name="indikator" x-model="activeRubrik.indikator" rows="3" required class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-300 rounded-xl resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" @click="openEditModal = false" class="px-4 py-2 border rounded-xl text-xs font-bold">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-vokasi-primary text-white rounded-xl text-xs font-bold">Update</button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection