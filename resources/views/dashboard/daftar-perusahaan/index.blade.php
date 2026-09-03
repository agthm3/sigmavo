@extends('layouts.dashboard')

@section('content')
    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50" 
         x-data="perusahaanComponent()">

        <!-- CONTENT -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative custom-scrollbar">
            
            <div class="max-w-7xl mx-auto w-full flex-1 space-y-6">
                <!-- Page Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Perusahaan Mitra Magang</h2>
                        <p class="text-sm text-gray-500 mt-1">Kelola dan pastikan data instansi/perusahaan mitra bebas dari duplikasi.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button @click="mergeModal = true" class="bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 text-sm font-semibold py-2 px-4 rounded-lg transition-colors shadow-sm flex items-center">
                            <i class="fas fa-object-group mr-2"></i> Gabungkan Data (Merge)
                        </button>
                        <button @click="isEdit = false; editData = {}; openModal = true" class="bg-vokasi-primary hover:bg-vokasi-dark text-white text-sm font-semibold py-2 px-4 rounded-lg transition-colors shadow-sm flex items-center">
                            <i class="fas fa-building-circle-check mr-2"></i> Tambah Baru
                        </button>
                    </div>
                </div>

                <!-- NOTIFICATIONS -->
                @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2"><i class="fas fa-check-circle text-emerald-600 text-lg"></i><span>{{ session('success') }}</span></div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900"><i class="fas fa-times"></i></button>
                </div>
                @endif
                @if(session('error'))
                <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2"><i class="fas fa-exclamation-triangle text-red-600 text-lg"></i><span>{{ session('error') }}</span></div>
                    <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900"><i class="fas fa-times"></i></button>
                </div>
                @endif

                <!-- SUMMARY CARDS & TABLE (Sama seperti sebelumnya) -->
                <!-- [Data Table Disingkat Demi Kejelasan Code] -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <form action="{{ route('dashboard-daftar-lowongan-daftar-perusahaan') }}" method="GET" class="p-5 border-b border-gray-100 flex flex-col lg:flex-row justify-between lg:items-center gap-4 bg-gray-50/50">
                        <div class="relative w-full lg:w-96">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama perusahaan..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:border-vokasi-primary">
                        </div>
                    </form>

                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[1000px]">
                            <thead>
                                <tr class="bg-gray-100/60 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <th class="p-4 w-12 text-center">No</th>
                                    <th class="p-4 w-64">Nama Perusahaan</th>
                                    <th class="p-4 min-w-[200px]">Sektor & Alamat</th>
                                    <th class="p-4 w-32 text-center">Status</th>
                                    <th class="p-4 w-32 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                @forelse($perusahaans as $index => $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-center text-gray-500 font-medium">{{ $perusahaans->firstItem() + $index }}</td>
                                    <td class="p-4">
                                        <p class="font-bold text-gray-800">{{ $item->nama_perusahaan }}</p>
                                        <p class="text-[11px] text-gray-500">{{ $item->email_hrd }}</p>
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center px-2 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-bold rounded border border-blue-100 mb-1">{{ $item->sektor_industri }}</span>
                                        <p class="font-medium text-gray-600 text-xs line-clamp-1">{{ $item->alamat }}</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 text-[10px] font-bold rounded-full border {{ $item->status_kerjasama == 'MoU Resmi' ? 'bg-purple-100 text-purple-700 border-purple-200' : 'bg-gray-100 text-gray-600 border-gray-200' }}">{{ $item->status_kerjasama }}</span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button @click="isEdit = true; editData = {{ json_encode($item) }}; activeUrl = '{{ route('dashboard-daftar-lowongan-daftar-perusahaan-update', $item->id) }}'; openModal = true" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold py-1.5 px-2.5 rounded transition-colors"><i class="fas fa-edit"></i></button>
                                        <form action="{{ route('dashboard-daftar-lowongan-daftar-perusahaan-destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold py-1.5 px-2.5 rounded transition-colors border border-red-200"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="p-8 text-center text-gray-400">Belum ada data perusahaan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-gray-100 bg-white">{{ $perusahaans->links() }}</div>
                </div>
            </div>
        </main>

        <!-- ========================================== -->
        <!-- MODAL 1: TAMBAH / EDIT PERUSAHAAN (Disingkat) -->
        <div x-show="openModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div @click.away="openModal = false" class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-2xl overflow-hidden">
                <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-building-circle-check text-lg"></i>
                        <h3 class="font-bold text-lg" x-text="isEdit ? 'Edit Data Perusahaan Mitra' : 'Tambah Perusahaan Mitra Baru'"></h3>
                    </div>
                    <button @click="openModal = false" class="text-white/80 hover:text-white"><i class="fas fa-times text-lg"></i></button>
                </div>
                <form :action="isEdit ? activeUrl : '{{ route('dashboard-daftar-lowongan-daftar-perusahaan-store') }}'" method="POST" class="p-6 space-y-4">
                    @csrf
                    <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Instansi <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_perusahaan" :value="isEdit ? editData.nama_perusahaan : ''" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Sektor <span class="text-red-500">*</span></label>
                            <select name="sektor_industri" :value="isEdit ? editData.sektor_industri : ''" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm" required>
                                <option value="Teknologi Informasi">Teknologi Informasi</option>
                                <option value="Pertanian">Pertanian</option>
                                <option value="Pemerintahan">Pemerintahan</option>
                                <option value="Kesehatan">Kesehatan</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Status Kerjasama</label>
                            <select name="status_kerjasama" :value="isEdit ? editData.status_kerjasama : 'Mitra Reguler'" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm">
                                <option value="MoU Resmi">MoU / MoA Resmi UNHAS</option>
                                <option value="Mitra Reguler">Mitra Reguler</option>
                                <option value="Mandiri Partner">Mandiri Partner</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Website</label>
                            <input type="url" name="website" :value="isEdit ? editData.website : ''" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Email HRD <span class="text-red-500">*</span></label>
                            <input type="email" name="email_hrd" :value="isEdit ? editData.email_hrd : ''" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Alamat <span class="text-red-500">*</span></label>
                            <textarea name="alamat" x-text="isEdit ? editData.alamat : ''" rows="2" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm" required></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Latitude</label>
                            <input type="text" name="latitude" :value="isEdit ? editData.latitude : ''" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Longitude</label>
                            <input type="text" name="longitude" :value="isEdit ? editData.longitude : ''" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm">
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button" @click="openModal = false" class="px-4 py-2 border rounded-lg bg-white font-medium text-sm">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-vokasi-primary text-white rounded-lg font-bold text-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL 2: GABUNGKAN / MERGE DATA -->
        <!-- ========================================== -->
        <div x-show="mergeModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 flex items-center justify-center p-4">
            <div @click.away="mergeModal = false" class="bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-5xl overflow-hidden flex flex-col max-h-[95vh]">
                
                <div class="bg-purple-600 px-6 py-4 text-white flex justify-between items-center shrink-0">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-object-group text-lg"></i>
                        <h3 class="font-bold text-base">Gabungkan (Merge) Instansi Duplikat</h3>
                    </div>
                    <button @click="mergeModal = false" class="text-white/80 hover:text-white p-1 rounded-lg"><i class="fas fa-times text-lg"></i></button>
                </div>

                <div class="flex flex-col md:flex-row flex-1 overflow-hidden bg-gray-50">
                    
                    <!-- KIRI: PILIHAN TARGET & SOURCE -->
                    <div class="w-full md:w-1/2 p-6 overflow-y-auto border-r border-gray-200 space-y-6">
                        
                        <!-- 1. Perusahaan Utama (SEARCHABLE DROPDOWN) -->
                        <div class="relative">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-2">1. Instansi Master (Target) <span class="text-red-500">*</span></label>
                            <p class="text-[11px] text-gray-500 mb-2">Instansi ini akan dipertahankan namanya.</p>
                            
                            <!-- Search Input -->
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i class="fas fa-search text-gray-400 text-xs"></i>
                                </div>
                                <input type="text" x-model="searchTargetName" @focus="openTargetDropdown = true" @input="openTargetDropdown = true" placeholder="Cari & pilih instansi master..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-xl text-xs font-semibold focus:outline-none focus:border-purple-500">
                            </div>

                            <!-- Dropdown Box -->
                            <div x-show="openTargetDropdown" @click.away="openTargetDropdown = false" class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-52 overflow-y-auto">
                                <template x-for="p in allPerusahaan.filter(p => p.nama.toLowerCase().includes(searchTargetName.toLowerCase()))" :key="p.id">
                                    <div @click="selectTarget(p)" class="px-4 py-2.5 text-xs cursor-pointer border-b border-gray-100 hover:bg-purple-50 hover:text-purple-700 font-semibold text-gray-700 transition-colors">
                                        <i class="fas fa-building mr-1.5 text-purple-400"></i> <span x-text="p.nama"></span>
                                    </div>
                                </template>
                                <template x-if="allPerusahaan.filter(p => p.nama.toLowerCase().includes(searchTargetName.toLowerCase())).length === 0">
                                    <div class="px-4 py-3 text-xs text-gray-400 italic text-center">Tidak ditemukan.</div>
                                </template>
                            </div>
                        </div>

                        <!-- 2. Perusahaan Duplikat (Sumber) -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-2">2. Pilih Instansi Duplikat (Disedot & Dihapus) <span class="text-red-500">*</span></label>
                            <p class="text-[11px] text-gray-500 mb-2">Pilih satu atau lebih instansi ganda. Datanya akan dipindahkan ke Target.</p>
                            
                            <div class="bg-white border border-gray-300 rounded-xl overflow-hidden shadow-sm">
                                <div class="p-2 border-b border-gray-200 bg-gray-50 relative">
                                    <i class="fas fa-search absolute left-4 top-3 text-gray-400 text-[10px]"></i>
                                    <input type="text" x-model="searchSource" placeholder="Cari nama duplikat..." class="w-full pl-7 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:border-purple-300">
                                </div>
                                <div class="max-h-48 overflow-y-auto p-2 space-y-1 custom-scrollbar">
                                    <template x-for="p in filteredSources" :key="p.id">
                                        <label class="flex items-center p-2 rounded-lg hover:bg-purple-50 cursor-pointer border border-transparent hover:border-purple-200 transition-colors"
                                               :class="mergeSources.includes(p.id) ? 'bg-purple-50 border-purple-200' : ''">
                                            <input type="checkbox" :value="p.id" @change="toggleSource(p.id)" :checked="mergeSources.includes(p.id)" class="w-4 h-4 text-purple-600 rounded border-gray-300 focus:ring-purple-500">
                                            <span class="ml-2 text-xs font-semibold text-gray-700" x-text="p.nama"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- KANAN: PREVIEW KONFIRMASI -->
                    <div class="w-full md:w-1/2 p-6 bg-white overflow-y-auto flex flex-col justify-between">
                        
                        <div class="space-y-4">
                            <!-- Info SPV Master Target (Fitur Baru) -->
                            <template x-if="mergeTarget && !isLoadingPreview">
                                <div class="bg-purple-50 border border-purple-200 p-3 rounded-xl">
                                    <h5 class="text-[10px] font-bold text-purple-500 uppercase mb-1">Identitas SPV Target (Master)</h5>
                                    <template x-if="previewData?.target_spv?.length > 0">
                                        <div>
                                            <template x-for="spv in previewData.target_spv">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <div class="w-6 h-6 bg-purple-200 text-purple-700 rounded-full flex justify-center items-center text-[10px]"><i class="fas fa-user-tie"></i></div>
                                                    <div>
                                                        <p class="text-xs font-bold text-purple-900 leading-tight" x-text="spv.name"></p>
                                                        <p class="text-[10px] text-purple-600" x-text="spv.email"></p>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="previewData?.target_spv?.length === 0">
                                        <p class="text-xs text-purple-700 italic"><i class="fas fa-info-circle mr-1"></i> Perusahaan Master belum memiliki SPV terdaftar.</p>
                                    </template>
                                </div>
                            </template>

                            <h4 class="font-bold text-sm text-gray-800 border-b border-gray-100 pb-2">
                                <i class="fas fa-satellite-dish text-purple-500 mr-1.5"></i> Data yang Akan Dipindahkan
                            </h4>

                            <!-- State Kosong / Loading -->
                            <template x-if="mergeSources.length === 0 && !isLoadingPreview">
                                <div class="text-center p-6 text-gray-400">
                                    <i class="fas fa-boxes text-3xl mb-2 opacity-50"></i>
                                    <p class="text-xs">Pilih minimal 1 instansi duplikat untuk melihat apa saja data yang akan dipindahkan.</p>
                                </div>
                            </template>
                            <template x-if="isLoadingPreview">
                                <div class="text-center p-6 text-purple-500">
                                    <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                                    <p class="text-xs font-bold">Menarik relasi data...</p>
                                </div>
                            </template>

                            <!-- State Data Ditemukan (Clickable Badge) -->
                            <template x-if="mergeSources.length > 0 && !isLoadingPreview && previewData">
                                <div class="space-y-3">
                                    <!-- SPV Box -->
                                    <button type="button" @click="showDetail('spv')" class="w-full bg-blue-50 hover:bg-blue-100 border border-blue-200 p-3 rounded-xl flex items-center justify-between transition text-left">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-user-tie text-blue-500 text-lg"></i>
                                            <div>
                                                <span class="text-xs font-bold text-blue-900 block">Akun SPV (Duplikat)</span>
                                                <span class="text-[10px] text-blue-600">Klik untuk melihat siapa saja</span>
                                            </div>
                                        </div>
                                        <span class="font-bold text-lg text-blue-700" x-text="previewData.sources.spvs.length + ' SPV'"></span>
                                    </button>

                                    <!-- Mahasiswa Box -->
                                    <button type="button" @click="showDetail('pendaftar')" class="w-full bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 p-3 rounded-xl flex items-center justify-between transition text-left">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-users text-emerald-500 text-lg"></i>
                                            <div>
                                                <span class="text-xs font-bold text-emerald-900 block">Mahasiswa & Logbook</span>
                                                <span class="text-[10px] text-emerald-600">Klik untuk melihat daftar mahasiswa</span>
                                            </div>
                                        </div>
                                        <span class="font-bold text-lg text-emerald-700" x-text="previewData.sources.pendaftars.length + ' Anak'"></span>
                                    </button>

                                    <!-- Lowongan Box -->
                                    <button type="button" @click="showDetail('lowongan')" class="w-full bg-amber-50 hover:bg-amber-100 border border-amber-200 p-3 rounded-xl flex items-center justify-between transition text-left">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-briefcase text-amber-500 text-lg"></i>
                                            <span class="text-xs font-bold text-amber-900">Lowongan / Divisi Posisi</span>
                                        </div>
                                        <span class="font-bold text-lg text-amber-700" x-text="previewData.sources.lowongans.length + ' Divisi'"></span>
                                    </button>

                                    <div class="mt-2 p-2.5 bg-red-50 border border-red-200 rounded-lg text-[10px] text-red-800 leading-relaxed font-medium flex gap-2">
                                        <i class="fas fa-exclamation-triangle mt-0.5"></i>
                                        <p>Instansi duplikat (sumber) akan <strong>DIHAPUS PERMANEN</strong> setelah datanya pindah.</p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="pt-4 mt-6 border-t border-gray-100 shrink-0">
                            <form action="{{ route('dashboard-daftar-lowongan-daftar-perusahaan-merge') }}" method="POST">
                                @csrf
                                <input type="hidden" name="target_id" :value="mergeTarget">
                                <template x-for="sid in mergeSources" :key="sid">
                                    <input type="hidden" name="source_ids[]" :value="sid">
                                </template>

                                <button type="submit" 
                                        :disabled="!mergeTarget || mergeSources.length === 0 || isLoadingPreview" 
                                        :class="(!mergeTarget || mergeSources.length === 0 || isLoadingPreview) ? 'bg-gray-300 cursor-not-allowed opacity-70 text-gray-500' : 'bg-purple-600 hover:bg-purple-700 text-white shadow-md'"
                                        class="w-full py-3 rounded-xl font-bold text-xs transition-colors flex items-center justify-center gap-2"
                                        onclick="return confirm('Apakah Anda 100% yakin ingin menggabungkan dan menghapus perusahaan duplikat ini?')">
                                    <i class="fas fa-code-merge text-lg"></i> Eksekusi Proses Merge Data
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SUB-MODAL 3: DETAIL RINCIAN SEBELUM MERGE -->
        <!-- ========================================== -->
        <div x-show="detailModal" x-cloak class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] z-50 flex items-center justify-center p-4">
            <div @click.away="detailModal = false" class="bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-lg overflow-hidden flex flex-col max-h-[80vh]">
                
                <div class="bg-gray-800 px-5 py-3 text-white flex justify-between items-center shrink-0">
                    <h3 class="font-bold text-sm" x-text="detailTitle"></h3>
                    <button @click="detailModal = false" class="text-white/70 hover:text-white"><i class="fas fa-times text-lg"></i></button>
                </div>

                <div class="p-5 overflow-y-auto custom-scrollbar flex-1">
                    
                    <!-- View Data SPV -->
                    <template x-if="detailType === 'spv'">
                        <div class="space-y-3">
                            <template x-for="item in previewData?.sources?.spvs" :key="item.email">
                                <div class="bg-blue-50 border border-blue-100 p-3 rounded-xl">
                                    <p class="font-bold text-blue-900 text-xs" x-text="item.name"></p>
                                    <p class="text-[11px] text-blue-600" x-text="item.email"></p>
                                    <p class="text-[10px] text-gray-500 mt-1"><i class="fas fa-building mr-1"></i> Asal: <span x-text="item.asal"></span></p>
                                </div>
                            </template>
                            <template x-if="previewData?.sources?.spvs.length === 0">
                                <p class="text-xs text-center text-gray-500">Tidak ada SPV di perusahaan duplikat.</p>
                            </template>
                        </div>
                    </template>

                    <!-- View Data Mahasiswa -->
                    <template x-if="detailType === 'pendaftar'">
                        <div class="space-y-3">
                            <template x-for="item in previewData?.sources?.pendaftars" :key="item.nim">
                                <div class="bg-emerald-50 border border-emerald-100 p-3 rounded-xl flex justify-between items-center">
                                    <div>
                                        <p class="font-bold text-emerald-900 text-xs" x-text="item.name"></p>
                                        <p class="text-[11px] text-emerald-700">NIM: <span x-text="item.nim || '-'"></span></p>
                                        <p class="text-[10px] text-gray-500 mt-1"><i class="fas fa-building mr-1"></i> Asal: <span x-text="item.asal"></span></p>
                                    </div>
                                </div>
                            </template>
                            <template x-if="previewData?.sources?.pendaftars.length === 0">
                                <p class="text-xs text-center text-gray-500">Tidak ada Mahasiswa di perusahaan duplikat.</p>
                            </template>
                        </div>
                    </template>

                    <!-- View Data Lowongan -->
                    <template x-if="detailType === 'lowongan'">
                        <div class="space-y-3">
                            <template x-for="item in previewData?.sources?.lowongans" :key="item.judul_posisi + item.asal">
                                <div class="bg-amber-50 border border-amber-100 p-3 rounded-xl">
                                    <p class="font-bold text-amber-900 text-xs" x-text="item.judul_posisi"></p>
                                    <p class="text-[10px] text-gray-500 mt-1"><i class="fas fa-building mr-1"></i> Asal: <span x-text="item.asal"></span></p>
                                </div>
                            </template>
                        </div>
                    </template>

                </div>
                
                <div class="p-3 border-t bg-gray-50 text-right shrink-0">
                    <button @click="detailModal = false" class="px-4 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-bold rounded-lg">Tutup</button>
                </div>
            </div>
        </div>

    </div>

    <!-- SCRIPT ALPINE COMPONENT -->
    <script>
        function perusahaanComponent() {
            const rawPerusahaan = @json($allPerusahaan->map(function($p) { return ['id' => $p->id, 'nama' => $p->nama_perusahaan]; }));

            return {
                openModal: false,
                isEdit: false,
                editData: {},
                activeUrl: '',

                // State Modal Merge & Dropdown Search
                mergeModal: false,
                openTargetDropdown: false,
                searchTargetName: '',
                mergeTarget: '',
                mergeSources: [],
                searchSource: '',
                
                // State Data & Detail Modal
                previewData: null,
                isLoadingPreview: false,
                allPerusahaan: rawPerusahaan,
                
                detailModal: false,
                detailType: '',
                detailTitle: '',

                // Fungsi untuk Memilih Target dari Dropdown
                selectTarget(perusahaan) {
                    this.mergeTarget = perusahaan.id;
                    this.searchTargetName = perusahaan.nama;
                    this.openTargetDropdown = false;
                    this.fetchPreview(); // Langsung fetch agar data SPV Master muncul
                },

                // Filter Source (Tidak menampilkan target)
                get filteredSources() {
                    return this.allPerusahaan.filter(p => {
                        const notTarget = p.id != this.mergeTarget;
                        const matchSearch = p.nama.toLowerCase().includes(this.searchSource.toLowerCase());
                        return notTarget && matchSearch;
                    });
                },

                // Centang Data Source
                toggleSource(id) {
                    const index = this.mergeSources.indexOf(id);
                    if (index > -1) {
                        this.mergeSources.splice(index, 1);
                    } else {
                        this.mergeSources.push(id);
                    }
                    this.fetchPreview();
                },

                // Ambil Data Preview Detail
                fetchPreview() {
                    this.isLoadingPreview = true;
                    
                    fetch("{{ route('dashboard-daftar-lowongan-daftar-perusahaan-preview') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ 
                            target_id: this.mergeTarget,
                            source_ids: this.mergeSources 
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.previewData = data;
                        this.isLoadingPreview = false;
                    })
                    .catch(err => {
                        console.error('Error fetching preview:', err);
                        this.isLoadingPreview = false;
                    });
                },

                // Fungsi Munculkan Sub-Modal Detail
                showDetail(type) {
                    this.detailType = type;
                    if(type === 'spv') this.detailTitle = 'Rincian Akun SPV yang akan dipindah';
                    if(type === 'pendaftar') this.detailTitle = 'Rincian Mahasiswa yang akan dipindah';
                    if(type === 'lowongan') this.detailTitle = 'Rincian Posisi/Lowongan yang akan dipindah';
                    this.detailModal = true;
                }
            }
        }
    </script>
@endsection