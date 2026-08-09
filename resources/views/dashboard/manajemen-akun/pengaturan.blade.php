@extends('layouts.dashboard')

@section('content')
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 lg:p-8 custom-scrollbar" 
      x-data="{ 
        openAddProdiModal: false, 
        openEditProdiModal: false, 
        activeProdi: null,
        openAddCpmkModal: false,
        openEditCpmkModal: false,
        activeCpmk: null
      }">
    
    <!-- HEADER HALAMAN -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Pengaturan Global Portal</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data master Program Studi, Capaian Pembelajaran Mata Kuliah (CPMK), dan konfigurasi umum SIGMAVO.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-teal-50 text-vokasi-primary border border-vokasi-primary/20">
                <i class="fas fa-sliders-h mr-1.5"></i> Master Data Admin
            </span>
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

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- KOLOM KIRI: MASTER PRODI & MASTER CPMK (8 Cols) -->
        <div class="lg:col-span-8 space-y-8">
            
            <!-- 1. MASTER PROGRAM STUDI -->
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Master Program Studi (Prodi)</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Daftar prodi aktif di Fakultas Vokasi Universitas Hasanuddin.</p>
                    </div>

                    @if(Auth::user()->hasAnyRole(['admin', 'superadmin']))
                    <button type="button" @click="openAddProdiModal = true" class="px-4 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white font-semibold text-xs rounded-xl transition shadow-sm flex items-center gap-2">
                        <i class="fas fa-plus"></i> Tambah Prodi
                    </button>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-[11px] font-bold tracking-wider">
                                <th class="p-4 pl-6">Kode</th>
                                <th class="p-4">Nama Program Studi</th>
                                <th class="p-4">Jenjang</th>
                                <th class="p-4">Jumlah CPMK</th>
                                <th class="p-4 text-right pr-6">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse($prodis as $prodi)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-4 pl-6 font-mono font-bold text-vokasi-primary">
                                    {{ $prodi->kode_prodi }}
                                </td>
                                <td class="p-4 font-bold text-gray-800">
                                    {{ $prodi->nama_prodi }}
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-teal-50 text-vokasi-primary border border-vokasi-primary/20">
                                        {{ $prodi->jenjang }}
                                    </span>
                                </td>
                                <td class="p-4 text-xs font-semibold text-gray-600">
                                    <i class="fas fa-bullseye text-vokasi-primary mr-1"></i> {{ $prodi->cpmks->count() }} CPMK
                                </td>
                                <td class="p-4 text-right pr-6 space-x-2">
                                    @if(Auth::user()->hasAnyRole(['admin', 'superadmin']))
                                    <button type="button" @click="activeProdi = {{ json_encode($prodi) }}; openEditProdiModal = true" class="p-1.5 text-gray-400 hover:text-vokasi-primary transition-colors" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('dashboard-manajemen-pengaturan-prodi-destroy', $prodi->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus prodi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 transition-colors" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-xs text-gray-400 italic">Khusus Admin / Superadmin</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400">
                                    <i class="fas fa-university text-3xl mb-2 block"></i> Belum ada data Program Studi.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. MASTER CPMK PER PRODI -->
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden" x-data="{ filterProdi: 'semua' }">
                <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Master CPMK (Capaian Pembelajaran)</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Daftar CPMK yang dipilih mahasiswa saat mengisikan logbook harian.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <!-- Filter Prodi -->
                        <select x-model="filterProdi" class="bg-gray-50 border border-gray-200 text-gray-700 text-xs rounded-xl p-2 outline-none">
                            <option value="semua">Semua Prodi</option>
                            @foreach($prodis as $p)
                                <option value="{{ $p->id }}">{{ $p->kode_prodi }} - {{ $p->nama_prodi }}</option>
                            @endforeach
                        </select>

                        <button type="button" @click="openAddCpmkModal = true" class="px-4 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white font-semibold text-xs rounded-xl transition shadow-sm flex items-center gap-2 shrink-0">
                            <i class="fas fa-plus"></i> Tambah CPMK
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-[11px] font-bold tracking-wider">
                                <th class="p-4 pl-6 w-32">Kode CPMK</th>
                                <th class="p-4">Deskripsi Capaian Pembelajaran</th>
                                <th class="p-4 w-48">Program Studi</th>
                                <th class="p-4 text-right pr-6 w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse($cpmks as $cpmk)
                            <tr class="hover:bg-gray-50/50 transition-colors" x-show="filterProdi === 'semua' || filterProdi == '{{ $cpmk->prodi_id }}'">
                                <td class="p-4 pl-6 font-mono font-bold text-vokasi-primary">
                                    {{ $cpmk->kode_cpmk }}
                                </td>
                                <td class="p-4 font-medium text-gray-800 text-xs leading-relaxed">
                                    {{ $cpmk->deskripsi_cpmk }}
                                </td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $cpmk->prodi->nama_prodi ?? '-' }}
                                    </span>
                                </td>
                                <td class="p-4 text-right pr-6 space-x-2">
                                    <button type="button" @click="activeCpmk = {{ json_encode($cpmk) }}; openEditCpmkModal = true" class="p-1.5 text-gray-400 hover:text-vokasi-primary transition-colors" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('dashboard-manajemen-pengaturan-cpmk-destroy', $cpmk->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus CPMK ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 transition-colors" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-gray-400">
                                    <i class="fas fa-bullseye text-3xl mb-2 block"></i> Belum ada data CPMK terdaftar.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- KOLOM KANAN: PARAMETER AKADEMIK & SISTEM (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">
            <form action="{{ route('dashboard-manajemen-pengaturan-settings-update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm space-y-4">
                    <h3 class="text-base font-bold text-gray-800 border-b border-gray-100 pb-3"><i class="fas fa-sliders-h text-vokasi-primary mr-2"></i> Parameter Akademik Global</h3>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tahun Akademik Berjalan <span class="text-red-500">*</span></label>
                        <input type="text" name="tahun_akademik" value="{{ old('tahun_akademik', $settings['tahun_akademik']) }}" required {{ !Auth::user()->hasAnyRole(['admin', 'superadmin']) ? 'disabled' : '' }} class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Standar Minimal Jam Magang <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="min_jam_magang" value="{{ old('min_jam_magang', $settings['min_jam_magang']) }}" required {{ !Auth::user()->hasAnyRole(['admin', 'superadmin']) ? 'disabled' : '' }} class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none">
                            <span class="absolute right-3 top-2.5 text-xs text-gray-400 font-semibold">Jam</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Batas Maksimal Pengajuan Lowongan <span class="text-red-500">*</span></label>
                        <input type="number" name="max_pengajuan" value="{{ old('max_pengajuan', $settings['max_pengajuan']) }}" required {{ !Auth::user()->hasAnyRole(['admin', 'superadmin']) ? 'disabled' : '' }} class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none">
                    </div>

                    @if(Auth::user()->hasAnyRole(['admin', 'superadmin']))
                    <div class="pt-2">
                        <button type="submit" class="w-full py-2.5 bg-vokasi-primary hover:bg-vokasi-dark text-white font-semibold text-xs rounded-xl transition shadow-sm">
                            <i class="fas fa-save mr-1"></i> Simpan Parameter
                        </button>
                    </div>
                    @endif
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm space-y-3">
                    <h3 class="text-base font-bold text-gray-800 border-b border-gray-100 pb-3"><i class="fas fa-building text-vokasi-primary mr-2"></i> Sekretariat Vokasi</h3>
                    
                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email Resmi</label>
                            <input type="email" name="email_resmi" value="{{ old('email_resmi', $settings['email_resmi']) }}" required {{ !Auth::user()->hasAnyRole(['admin', 'superadmin']) ? 'disabled' : '' }} class="w-full px-3 py-1.5 text-xs bg-gray-50 border border-gray-200 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Lokasi Sekretariat</label>
                            <input type="text" name="lokasi" value="{{ old('lokasi', $settings['lokasi']) }}" {{ !Auth::user()->hasAnyRole(['admin', 'superadmin']) ? 'disabled' : '' }} class="w-full px-3 py-1.5 text-xs bg-gray-50 border border-gray-200 rounded-lg">
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>

    <!-- MODAL POPUP: TAMBAH PRODI BARU (ADMIN & SUPERADMIN) -->
    @if(Auth::user()->hasAnyRole(['admin', 'superadmin']))
    <div x-show="openAddProdiModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="openAddProdiModal = false" class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-university text-vokasi-primary mr-2"></i> Tambah Program Studi Baru</h3>
                <button @click="openAddProdiModal = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>

            <form action="{{ route('dashboard-manajemen-pengaturan-prodi-store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kode Prodi <span class="text-red-500">*</span></label>
                    <input type="text" name="kode_prodi" required placeholder="Contoh: TRK, MI, AB" class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none uppercase">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Program Studi <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_prodi" required placeholder="Contoh: Teknologi Rekayasa Komputer" class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Jenjang <span class="text-red-500">*</span></label>
                    <select name="jenjang" required class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none">
                        <option value="D4">D4 (Sarjana Terapan)</option>
                        <option value="D3">D3 (Ahli Madya)</option>
                        <option value="S1">S1</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="openAddProdiModal = false" class="px-4 py-2 bg-gray-100 text-gray-600 font-semibold text-xs rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white font-semibold text-xs rounded-xl shadow-sm">Simpan Prodi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL POPUP: EDIT PRODI -->
    <div x-show="openEditProdiModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="openEditProdiModal = false" class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden border border-gray-100" x-if="activeProdi">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-800">Edit Program Studi</h3>
                <button @click="openEditProdiModal = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>

            <form :action="'{{ url('/manajemen-akun/pengaturan/prodi') }}/' + activeProdi?.id" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kode Prodi <span class="text-red-500">*</span></label>
                    <input type="text" name="kode_prodi" :value="activeProdi?.kode_prodi" required class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none uppercase">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Program Studi <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_prodi" :value="activeProdi?.nama_prodi" required class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Jenjang <span class="text-red-500">*</span></label>
                    <select name="jenjang" :value="activeProdi?.jenjang" required class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none">
                        <option value="D4">D4 (Sarjana Terapan)</option>
                        <option value="D3">D3 (Ahli Madya)</option>
                        <option value="S1">S1</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="openEditProdiModal = false" class="px-4 py-2 bg-gray-100 text-gray-600 font-semibold text-xs rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white font-semibold text-xs rounded-xl shadow-sm">Perbarui Prodi</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- MODAL POPUP: TAMBAH CPMK BARU -->
    <div x-show="openAddCpmkModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="openAddCpmkModal = false" class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-bullseye text-vokasi-primary mr-2"></i> Tambah CPMK Baru</h3>
                <button @click="openAddCpmkModal = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>

            <form action="{{ route('dashboard-manajemen-pengaturan-cpmk-store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Program Studi <span class="text-red-500">*</span></label>
                    <select name="prodi_id" required class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none">
                        <option value="" disabled selected>Pilih Program Studi</option>
                        @foreach($prodis as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_prodi }} ({{ $p->kode_prodi }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kode CPMK <span class="text-red-500">*</span></label>
                    <input type="text" name="kode_cpmk" required placeholder="Contoh: CPMK-01" class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Deskripsi CPMK <span class="text-red-500">*</span></label>
                    <textarea name="deskripsi_cpmk" rows="3" required placeholder="Jelaskan capaian pembelajaran..." class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none resize-none"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="openAddCpmkModal = false" class="px-4 py-2 bg-gray-100 text-gray-600 font-semibold text-xs rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white font-semibold text-xs rounded-xl shadow-sm">Simpan CPMK</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL POPUP: EDIT CPMK -->
    <div x-show="openEditCpmkModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="openEditCpmkModal = false" class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden border border-gray-100" x-if="activeCpmk">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-800">Edit CPMK</h3>
                <button @click="openEditCpmkModal = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>

            <form :action="'{{ url('/manajemen-akun/pengaturan/cpmk') }}/' + activeCpmk?.id" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Program Studi <span class="text-red-500">*</span></label>
                    <select name="prodi_id" :value="activeCpmk?.prodi_id" required class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none">
                        @foreach($prodis as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_prodi }} ({{ $p->kode_prodi }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kode CPMK <span class="text-red-500">*</span></label>
                    <input type="text" name="kode_cpmk" :value="activeCpmk?.kode_cpmk" required class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Deskripsi CPMK <span class="text-red-500">*</span></label>
                    <textarea name="deskripsi_cpmk" rows="3" :value="activeCpmk?.deskripsi_cpmk" required class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none resize-none"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="openEditCpmkModal = false" class="px-4 py-2 bg-gray-100 text-gray-600 font-semibold text-xs rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white font-semibold text-xs rounded-xl shadow-sm">Perbarui CPMK</button>
                </div>
            </form>
        </div>
    </div>

</main>
@endsection