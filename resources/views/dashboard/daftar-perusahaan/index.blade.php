@extends('layouts.dashboard')

@section('content')
    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50" 
         x-data="{ openModal: false, isEdit: false, editData: {}, activeUrl: '' }">

        <!-- CONTENT -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative custom-scrollbar">
            
            <div class="max-w-7xl mx-auto w-full flex-1 space-y-6">
                
                <!-- Page Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Perusahaan Mitra Magang</h2>
                        <p class="text-sm text-gray-500 mt-1">Kelola data instansi/perusahaan mitra penyedia lowongan magang Vokasi UNHAS.</p>
                    </div>
                    <div class="flex gap-2">
                        <!-- TRIGGER MODAL TAMBAH -->
                        <button @click="isEdit = false; editData = {}; openModal = true" class="bg-vokasi-primary hover:bg-vokasi-dark text-white text-sm font-semibold py-2 px-4 rounded-lg transition-colors shadow-sm flex items-center">
                            <i class="fas fa-building-circle-check mr-2"></i> Tambah Perusahaan Baru
                        </button>
                    </div>
                </div>

                <!-- NOTIFIKASI SUKSES / ERROR -->
                @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900"><i class="fas fa-times"></i></button>
                </div>
                @endif

                @if($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm">
                    <p class="font-bold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal menyimpan data:</p>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- SUMMARY CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-building text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Total Mitra</p>
                            <p class="text-xl font-bold text-gray-800 leading-none mt-1">{{ $totalMitra }} Perusahaan</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-briefcase text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Lowongan Aktif</p>
                            <p class="text-xl font-bold text-green-600 leading-none mt-1">-- Program</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-handshake text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Mitra MoU / MoA</p>
                            <p class="text-xl font-bold text-purple-600 leading-none mt-1">{{ $totalMoU }} Resmi</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-user-tie text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Supervisor Terdaftar</p>
                            <p class="text-xl font-bold text-orange-600 leading-none mt-1">-- Akun</p>
                        </div>
                    </div>
                </div>

                <!-- TABLE SECTION -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    
                    <!-- Table Toolbar / Filter -->
                    <form action="{{ route('dashboard-daftar-lowongan-daftar-perusahaan') }}" method="GET" class="p-5 border-b border-gray-100 flex flex-col lg:flex-row justify-between lg:items-center gap-4 bg-gray-50/50">
                        <div class="relative w-full lg:w-96">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama perusahaan, sektor, atau alamat..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary">
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            <select name="sektor" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option value="semua" {{ request('sektor') == 'semua' ? 'selected' : '' }}>Semua Sektor Industri</option>
                                <option value="Teknologi Informasi" {{ request('sektor') == 'Teknologi Informasi' ? 'selected' : '' }}>Teknologi Informasi</option>
                                <option value="Pertanian" {{ request('sektor') == 'Pertanian' ? 'selected' : '' }}>Pertanian / Agrikultur</option>
                                <option value="Pemerintahan" {{ request('sektor') == 'Pemerintahan' ? 'selected' : '' }}>Pemerintahan</option>
                                <option value="Kesehatan" {{ request('sektor') == 'Kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                            </select>

                            <select name="status" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}>Semua Status Kerjasama</option>
                                <option value="MoU Resmi" {{ request('status') == 'MoU Resmi' ? 'selected' : '' }}>MoU Resmi</option>
                                <option value="Mitra Reguler" {{ request('status') == 'Mitra Reguler' ? 'selected' : '' }}>Mitra Reguler</option>
                                <option value="Mandiri Partner" {{ request('status') == 'Mandiri Partner' ? 'selected' : '' }}>Mandiri Partner</option>
                            </select>
                        </div>
                    </form>

                    <!-- Responsive Table -->
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[1100px]">
                            <thead>
                                <tr class="bg-gray-100/60 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <th class="p-4 w-12 text-center">No</th>
                                    <th class="p-4 w-64">Nama Perusahaan</th>
                                    <th class="p-4 w-40">Sektor Industri</th>
                                    <th class="p-4 min-w-[200px]">Kota / Alamat & Koordinat</th>
                                    <th class="p-4 w-32 text-center">Email HRD</th>
                                    <th class="p-4 w-32 text-center">Status Kerjasama</th>
                                    <th class="p-4 w-36 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                
                                @forelse($perusahaans as $index => $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-center text-gray-500 font-medium">{{ $perusahaans->firstItem() + $index }}</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-teal-50 border border-teal-100 flex items-center justify-center text-vokasi-primary shrink-0 font-bold">
                                                {{ $item->inisial }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800">{{ $item->nama_perusahaan }}</p>
                                                <p class="text-xs text-gray-500">{{ $item->website ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-semibold rounded">
                                            {{ $item->sektor_industri }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-medium text-gray-800 line-clamp-1">{{ $item->alamat }}</p>
                                        <p class="text-[10px] text-gray-400 font-mono">
                                            <i class="fas fa-map-marker-alt text-red-500 mr-1"></i> 
                                            {{ $item->latitude ?? '-' }}, {{ $item->longitude ?? '-' }}
                                        </p>
                                    </td>
                                    <td class="p-4 text-center text-xs font-semibold text-gray-600">
                                        {{ $item->email_hrd }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 text-[10px] font-bold rounded-full border
                                            {{ $item->status_kerjasama == 'MoU Resmi' ? 'bg-purple-100 text-purple-700 border-purple-200' : '' }}
                                            {{ $item->status_kerjasama == 'Mitra Reguler' ? 'bg-teal-100 text-vokasi-dark border-teal-200' : '' }}
                                            {{ $item->status_kerjasama == 'Mandiri Partner' ? 'bg-amber-100 text-amber-700 border-amber-200' : '' }}">
                                            {{ $item->status_kerjasama }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- EDIT BUTTON -->
                                            <button @click="isEdit = true; editData = {{ json_encode($item) }}; activeUrl = '{{ route('dashboard-daftar-lowongan-daftar-perusahaan-update', $item->id) }}'; openModal = true" 
                                                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold py-1.5 px-2.5 rounded transition-colors" title="Edit Data">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <!-- DELETE BUTTON -->
                                            <form action="{{ route('dashboard-daftar-lowongan-daftar-perusahaan-destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus perusahaan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold py-1.5 px-2.5 rounded transition-colors border border-red-200" title="Hapus Data">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-gray-400">
                                        <i class="fas fa-building text-3xl mb-2 block"></i> Belum ada data perusahaan mitra.
                                    </td>
                                </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Controls -->
                    <div class="p-4 border-t border-gray-100 bg-white">
                        {{ $perusahaans->links() }}
                    </div>

                </div>

            </div>

            <!-- FOOTER -->
            <footer class="mt-8 py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
                Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
            </footer>

        </main>

        <!-- ========================================== -->
        <!-- MODAL POPUP: TAMBAH / EDIT PERUSAHAAN MITRA -->
        <!-- ========================================== -->
        <div x-show="openModal" 
             x-cloak 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            
            <div @click.away="openModal = false" class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-2xl overflow-hidden">
                
                <!-- Modal Header -->
                <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-building-circle-check text-lg"></i>
                        <h3 class="font-bold text-lg" x-text="isEdit ? 'Edit Data Perusahaan Mitra' : 'Tambah Perusahaan Mitra Baru'"></h3>
                    </div>
                    <button @click="openModal = false" class="text-white/80 hover:text-white hover:bg-black/10 p-1.5 rounded-lg transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Modal Body / Form -->
                <form :action="isEdit ? activeUrl : '{{ route('dashboard-daftar-lowongan-daftar-perusahaan-store') }}'" method="POST" class="p-6 space-y-4">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <!-- Nama Perusahaan -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Instansi / Perusahaan <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_perusahaan" :value="isEdit ? editData.nama_perusahaan : ''" placeholder="Contoh: PT. Inovasi Teknologi Nusantara" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                        </div>

                        <!-- Sektor Industri -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sektor / Bidang Industri <span class="text-red-500">*</span></label>
                            <select name="sektor_industri" :value="isEdit ? editData.sektor_industri : ''" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                                <option value="" disabled selected>Pilih Bidang</option>
                                <option value="Teknologi Informasi">Teknologi Informasi / Software</option>
                                <option value="Pertanian">Pertanian / Agrikultur</option>
                                <option value="Pemerintahan">Instansi Pemerintah</option>
                                <option value="Kesehatan">Kesehatan / Rumah Sakit</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <!-- Status Kerjasama -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Kerjasama</label>
                            <select name="status_kerjasama" :value="isEdit ? editData.status_kerjasama : 'Mitra Reguler'" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors">
                                <option value="MoU Resmi">MoU / MoA Resmi UNHAS</option>
                                <option value="Mitra Reguler">Mitra Reguler Prodi</option>
                                <option value="Mandiri Partner">Pengajuan Mandiri Mahasiswa</option>
                            </select>
                        </div>

                        <!-- Website -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Website Perusahaan</label>
                            <input type="url" name="website" :value="isEdit ? editData.website : ''" placeholder="https://www.company.com" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors">
                        </div>

                        <!-- Email Kontak / HR -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Resmi / HRD <span class="text-red-500">*</span></label>
                            <input type="email" name="email_hrd" :value="isEdit ? editData.email_hrd : ''" placeholder="hrd@company.com" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                        </div>

                        <!-- Alamat Lengkap -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap Perusahaan <span class="text-red-500">*</span></label>
                            <textarea name="alamat" x-text="isEdit ? editData.alamat : ''" rows="2" placeholder="Jl. Perintis Kemerdekaan KM..." class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors resize-none" required></textarea>
                        </div>

                        <!-- Titik Koordinat Geofencing -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Latitude (GPS)</label>
                            <input type="text" name="latitude" :value="isEdit ? editData.latitude : ''" placeholder="-5.1322..." class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Longitude (GPS)</label>
                            <input type="text" name="longitude" :value="isEdit ? editData.longitude : ''" placeholder="119.4255..." class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors">
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-6">
                        <button type="button" @click="openModal = false" class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 font-medium text-sm transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-lg font-bold text-sm transition-colors shadow-sm flex items-center">
                            <i class="fas fa-save mr-2"></i> <span x-text="isEdit ? 'Perbarui Data' : 'Simpan Perusahaan'"></span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
@endsection