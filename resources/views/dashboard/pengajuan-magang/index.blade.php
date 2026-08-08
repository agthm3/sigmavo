@extends('layouts.dashboard')

@section('content')

    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50"
         x-data="{ openModal: false, openVerifikasiModal: false, activeData: null, activeUrl: '', autoNomor: '' }">

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative custom-scrollbar">
            
            <div class="max-w-7xl mx-auto w-full flex-1 space-y-6">
                
                <!-- Page Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Pengajuan Magang & Penerbitan Surat</h2>
                        <p class="text-sm text-gray-500 mt-1">Verifikasi permohonan pengajuan magang, terbitkan Surat Pengantar, dan validasi Surat Balasan Perusahaan.</p>
                    </div>
                </div>

                <!-- NOTIFIKASI SUKSES / ERROR -->
                @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center justify-between text-sm shadow-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900"><i class="fas fa-times"></i></button>
                </div>
                @endif

                @if($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm shadow-sm">
                    <p class="font-bold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Terjadi kesalahan:</p>
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
                            <i class="fas fa-paper-plane text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Total Pengajuan</p>
                            <p class="text-xl font-bold text-gray-800 leading-none mt-1">{{ $totalPengajuan }} Berkas</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-yellow-50 text-yellow-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-clock text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Perlu Surat Pengantar</p>
                            <p class="text-xl font-bold text-yellow-600 leading-none mt-1">{{ $totalPerluSurat }} Pengajuan</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-file-circle-check text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Surat Diterbitkan</p>
                            <p class="text-xl font-bold text-green-600 leading-none mt-1">{{ $totalSuratTerbit }} Surat</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-route text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Pengajuan Mandiri</p>
                            <p class="text-xl font-bold text-purple-600 leading-none mt-1">{{ $totalMandiri }} Berkas</p>
                        </div>
                    </div>
                </div>

                <!-- TABLE SECTION -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    
                    <!-- Form Filter Toolbar -->
                    <form action="{{ route('dashboard-daftar-lowongan-pengajuan-magang') }}" method="GET" class="p-5 border-b border-gray-100 flex flex-col lg:flex-row justify-between lg:items-center gap-4 bg-gray-50/50">
                        <div class="relative w-full lg:w-96">
                            <i class="fas fa-search absolute left-3.5 top-3 text-gray-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama mahasiswa, NIM, atau perusahaan..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-primary">
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            <select name="jalur" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-xs rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option value="semua" {{ request('jalur') == 'semua' ? 'selected' : '' }}>Semua Jalur</option>
                                <option value="reguler" {{ request('jalur') == 'reguler' ? 'selected' : '' }}>Lowongan Reguler</option>
                                <option value="mandiri" {{ request('jalur') == 'mandiri' ? 'selected' : '' }}>Magang Mandiri</option>
                            </select>

                            <select name="status_surat" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-xs rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option value="semua" {{ request('status_surat') == 'semua' ? 'selected' : '' }}>Semua Status Surat</option>
                                <option value="menunggu" {{ request('status_surat') == 'menunggu' ? 'selected' : '' }}>Menunggu Penerbitan</option>
                                <option value="terbit" {{ request('status_surat') == 'terbit' ? 'selected' : '' }}>Terbit (Siap Ambil)</option>
                            </select>
                        </div>
                    </form>

                    <!-- Tabel Data Pengajuan Magang -->
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[1100px]">
                            <thead>
                                <tr class="bg-gray-100/60 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <th class="p-4 w-12 text-center">No</th>
                                    <th class="p-4 w-56">Mahasiswa Pemohon</th>
                                    <th class="p-4 w-44">Instansi Tujuan</th>
                                    <th class="p-4 w-32 text-center">Surat Pengantar</th>
                                    <th class="p-4 w-40 text-center">Surat Balasan (Mhs)</th>
                                    <th class="p-4 w-32 text-center">Status Akhir</th>
                                    <th class="p-4 w-44 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                
                                @forelse($pendaftarans as $index => $item)
                                <tr class="hover:bg-gray-50 transition-colors {{ $item->status_surat == 'menunggu' ? 'bg-yellow-50/20' : '' }}">
                                    <td class="p-4 text-center text-gray-500 font-medium">{{ $pendaftarans->firstItem() + $index }}</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($item->user?->name ?? 'Mhs') }}&background=f3f4f6&color=37A7AC" alt="Avatar" class="w-9 h-9 rounded-full border border-gray-200">
                                            <div>
                                                <p class="font-bold text-gray-800 text-xs">{{ $item->user?->name ?? 'User Terhapus' }}</p>
                                                <p class="text-[11px] text-gray-500">{{ $item->user?->mahasiswaProfile?->nim ?? '-' }}</p>
                                                <p class="text-[10px] font-semibold text-vokasi-primary mt-0.5">{{ $item->user?->mahasiswaProfile?->prodi?->nama_prodi ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        @if($item->jalur_magang == 'mandiri')
                                            <p class="font-bold text-gray-800 text-xs">{{ $item->nama_instansi_mandiri ?? '-' }}</p>
                                            <p class="text-[10px] text-purple-600 font-semibold">Magang Mandiri</p>
                                        @else
                                            <p class="font-bold text-gray-800 text-xs">{{ $item->lowongan?->perusahaan?->nama_perusahaan ?? '-' }}</p>
                                            <p class="text-[10px] text-blue-600 font-semibold">{{ $item->lowongan?->judul_posisi ?? 'Lowongan Reguler' }}</p>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($item->status_surat == 'terbit')
                                            <span class="inline-flex items-center px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded-md border border-green-200">
                                                <i class="fas fa-check-circle mr-1"></i> Terbit
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 bg-yellow-100 text-yellow-700 text-[10px] font-bold rounded-md border border-yellow-200">
                                                <i class="fas fa-clock mr-1"></i> Belum
                                            </span>
                                        @endif
                                    </td>
                                    
                                    <!-- KOLOM TERBARU: FILE SURAT BALASAN PERUSAHAAN -->
                                    <td class="p-4 text-center">
                                        @if(!empty($item->surat_balasan))
                                            <a href="{{ asset('storage/' . $item->surat_balasan) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 bg-purple-100 hover:bg-purple-200 text-purple-800 text-xs font-bold rounded-xl border border-purple-300 shadow-sm transition-colors">
                                                <i class="fas fa-file-pdf text-purple-600"></i> Lihat PDF
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Belum Diunggah</span>
                                        @endif
                                    </td>

                                    <td class="p-4 text-center">
                                        @if($item->status_seleksi == 'diterima')
                                            <span class="inline-flex items-center px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full border border-emerald-200">
                                                <i class="fas fa-check-circle mr-1"></i> Diterima
                                            </span>
                                        @elseif($item->status_seleksi == 'ditolak')
                                            <span class="inline-flex items-center px-2.5 py-1 bg-red-100 text-red-700 text-[10px] font-bold rounded-full border border-red-200">
                                                <i class="fas fa-times-circle mr-1"></i> Ditolak
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 bg-yellow-100 text-yellow-700 text-[10px] font-bold rounded-full border border-yellow-200">
                                                <i class="fas fa-spinner fa-spin mr-1"></i> Proses
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Tombol Terbit / Edit Surat Pengantar -->
                                            <button type="button" 
                                                    @click="
                                                        activeData = {{ json_encode($item) }}; 
                                                        activeUrl = '{{ route('dashboard-daftar-lowongan-pengajuan-magang-terbit', $item->id) }}'; 
                                                        autoNomor = '{{ rand(1000, 9999) }}/UN4.15/TU.02/{{ date('Y') }}'; 
                                                        openModal = true;
                                                    " 
                                                    class="bg-vokasi-primary hover:bg-vokasi-dark text-white text-[11px] font-bold py-1.5 px-2.5 rounded-lg shadow-sm transition-colors flex items-center gap-1" 
                                                    title="Terbit / Edit Surat Pengantar">
                                                <i class="fas fa-envelope-open-text"></i> Surat
                                            </button>

                                            <!-- Tombol Verifikasi Balasan Perusahaan -->
                                            @if($item->status_surat == 'terbit')
                                            <button type="button" 
                                                    @click="
                                                        activeData = {{ json_encode($item) }}; 
                                                        activeUrl = '{{ route('dashboard-daftar-lowongan-pengajuan-magang-verifikasi-balasan', $item->id) }}'; 
                                                        openVerifikasiModal = true;
                                                    " 
                                                    class="bg-purple-600 hover:bg-purple-700 text-white text-[11px] font-bold py-1.5 px-2.5 rounded-lg shadow-sm transition-colors flex items-center gap-1" 
                                                    title="Verifikasi Keputusan Akhir">
                                                <i class="fas fa-user-check"></i> Verifikasi
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-gray-400">
                                        <i class="fas fa-paper-plane text-3xl mb-2 block"></i> Belum ada permohonan pengajuan magang.
                                    </td>
                                </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                    
                    <div class="p-4 border-t border-gray-100 bg-white">
                        {{ $pendaftarans->links() }}
                    </div>

                </div>

            </div>

            <!-- FOOTER -->
            <footer class="mt-8 py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
                Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
            </footer>

        </main>

        <!-- MODAL POPUP 1: TERBITKAN SURAT PENGANTAR -->
        <div x-show="openModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div @click.away="openModal = false" class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-2xl overflow-hidden" x-if="activeData">
                <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-file-signature text-lg"></i>
                        <h3 class="font-bold text-lg">Penerbitan Surat Pengantar Magang</h3>
                    </div>
                    <button type="button" @click="openModal = false" class="text-white/80 hover:text-white p-1.5 rounded-lg"><i class="fas fa-times text-lg"></i></button>
                </div>

                <form :action="activeUrl" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Mahasiswa</label>
                            <input type="text" :value="(activeData?.user?.name || 'Mahasiswa') + ' (' + (activeData?.user?.mahasiswa_profile?.nim || '-') + ')'" readonly class="w-full px-3 py-2 bg-gray-100 border rounded-lg text-sm text-gray-600 font-medium cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Instansi / Perusahaan Tujuan</label>
                            <input type="text" :value="activeData?.jalur_magang === 'mandiri' ? activeData?.nama_instansi_mandiri : activeData?.lowongan?.perusahaan?.nama_perusahaan" readonly class="w-full px-3 py-2 bg-gray-100 border rounded-lg text-sm text-gray-600 font-medium cursor-not-allowed">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Surat Pengantar <span class="text-red-500">*</span></label>
                            <input type="text" name="nomor_surat" :value="activeData?.nomor_surat || autoNomor" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm font-mono focus:ring-vokasi-primary" required>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Perihal Surat <span class="text-red-500">*</span></label>
                            <input type="text" name="perihal_surat" :value="activeData?.perihal_surat || 'Permohonan Pelaksanaan Magang Industri Mahasiswa Vokasi'" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-vokasi-primary" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Magang <span class="text-red-500">*</span></label>
                            <input type="date" name="tgl_mulai_magang" :value="activeData?.tgl_mulai_magang ? activeData?.tgl_mulai_magang.split('T')[0] : '2026-08-01'" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-vokasi-primary" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai Magang <span class="text-red-500">*</span></label>
                            <input type="date" name="tgl_selesai_magang" :value="activeData?.tgl_selesai_magang ? activeData?.tgl_selesai_magang.split('T')[0] : '2027-01-31'" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-vokasi-primary" required>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-6">
                        <button type="button" @click="openModal = false" class="px-5 py-2 border rounded-lg text-gray-700 bg-white font-medium text-sm">Batal</button>
                        <button type="submit" class="px-6 py-2 bg-vokasi-primary text-white rounded-lg font-bold text-sm shadow-sm flex items-center">
                            <i class="fas fa-save mr-2"></i> Terbitkan Surat
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL POPUP 2: VERIFIKASI KEPUTUSAN BALASAN PERUSAHAAN -->
        <div x-show="openVerifikasiModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div @click.away="openVerifikasiModal = false" class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-lg overflow-hidden" x-if="activeData">
                <div class="bg-purple-600 px-6 py-4 text-white flex justify-between items-center">
                    <h3 class="font-bold text-base"><i class="fas fa-user-check mr-2"></i> Verifikasi Hasil Balasan Perusahaan</h3>
                    <button type="button" @click="openVerifikasiModal = false" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
                </div>

                <form :action="activeUrl" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Mahasiswa Pemohon</p>
                        <p class="text-sm font-bold text-gray-800" x-text="activeData?.user?.name || '-'"></p>
                    </div>

                    <!-- PRATINJAU BERKAS SURAT BALASAN MAHASISWA DI DALAM MODAL -->
                    <div class="p-3.5 bg-purple-50 border border-purple-200 rounded-xl text-xs text-purple-900 flex justify-between items-center">
                        <div>
                            <p class="font-bold text-purple-950">Berkas Surat Balasan Mahasiswa:</p>
                            <p class="text-[11px] text-purple-700" x-text="activeData?.surat_balasan ? 'File sudah diunggah oleh mahasiswa.' : 'Mahasiswa belum mengunggah file.'"></p>
                        </div>
                        <template x-if="activeData?.surat_balasan">
                            <a :href="'/storage/' + activeData?.surat_balasan" target="_blank" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-bold text-xs shadow-sm flex items-center gap-1">
                                <i class="fas fa-file-pdf"></i> Buka File
                            </a>
                        </template>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Status Keputusan Akhir <span class="text-red-500">*</span></label>
                        <select name="status_seleksi" :value="activeData?.status_seleksi" class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:ring-vokasi-primary focus:outline-none" required>
                            <option value="diterima">DITERIMA (Mahasiswa Resmi Magang)</option>
                            <option value="ditolak">DITOLAK (Perusahaan Menolak)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Catatan Tambahan (Opsional)</label>
                        <textarea name="catatan_seleksi" :value="activeData?.catatan_seleksi" rows="3" placeholder="Contoh: Diterima di divisi IT Support..." class="w-full px-3.5 py-2 text-xs bg-gray-50 border border-gray-300 rounded-xl focus:ring-vokasi-primary focus:outline-none resize-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                        <button type="button" @click="openVerifikasiModal = false" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 bg-white font-bold text-xs">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold text-xs shadow-sm flex items-center gap-2">
                            <i class="fas fa-save"></i> Simpan Status
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection