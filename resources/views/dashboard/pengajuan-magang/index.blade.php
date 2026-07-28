@extends('layouts.dashboard')

@section('content')

    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50"
         x-data="{ openModal: false, activeData: null, activeUrl: '', autoNomor: '' }">

        <!-- CONTENT -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative custom-scrollbar">
            
            <div class="max-w-7xl mx-auto w-full flex-1 space-y-6">
                
                <!-- Page Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Pengajuan Magang & Penerbitan Surat</h2>
                        <p class="text-sm text-gray-500 mt-1">Verifikasi permohonan pengajuan magang dan cetak Surat Pengantar/Tugas Pengabdian.</p>
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
                    
                    <!-- Table Toolbar / Filter -->
                    <form action="{{ route('dashboard-daftar-lowongan-pengajuan-magang') }}" method="GET" class="p-5 border-b border-gray-100 flex flex-col lg:flex-row justify-between lg:items-center gap-4 bg-gray-50/50">
                        <div class="relative w-full lg:w-96">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama mahasiswa, NIM, atau perusahaan..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary">
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            <select name="jalur" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option value="semua" {{ request('jalur') == 'semua' ? 'selected' : '' }}>Semua Jalur</option>
                                <option value="reguler" {{ request('jalur') == 'reguler' ? 'selected' : '' }}>Lowongan Reguler</option>
                                <option value="mandiri" {{ request('jalur') == 'mandiri' ? 'selected' : '' }}>Magang Mandiri</option>
                            </select>

                            <select name="status_surat" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm">
                                <option value="semua" {{ request('status_surat') == 'semua' ? 'selected' : '' }}>Semua Status Surat</option>
                                <option value="menunggu" {{ request('status_surat') == 'menunggu' ? 'selected' : '' }}>Menunggu Penerbitan</option>
                                <option value="terbit" {{ request('status_surat') == 'terbit' ? 'selected' : '' }}>Terbit (Siap Ambil)</option>
                                <option value="ditolak" {{ request('status_surat') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                    </form>

                    <!-- Responsive Table -->
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[1100px]">
                            <thead>
                                <tr class="bg-gray-100/60 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <th class="p-4 w-12 text-center">No</th>
                                    <th class="p-4 w-60">Mahasiswa Pemohon</th>
                                    <th class="p-4 w-32 text-center">Jalur Magang</th>
                                    <th class="p-4 min-w-[220px]">Instansi / Perusahaan Tujuan</th>
                                    <th class="p-4 w-36">Tgl Pengajuan</th>
                                    <th class="p-4 w-36 text-center">Status Surat</th>
                                    <th class="p-4 w-40 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                
                                @forelse($pendaftarans as $index => $item)
                                <tr class="hover:bg-gray-50 transition-colors {{ $item->status_surat == 'menunggu' ? 'bg-yellow-50/20' : '' }}">
                                    <td class="p-4 text-center text-gray-500 font-medium">{{ $pendaftarans->firstItem() + $index }}</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($item->mahasiswa->name ?? 'Mhs') }}&background=f3f4f6&color=37A7AC" alt="Avatar" class="w-9 h-9 rounded-full border border-gray-200">
                                            <div>
                                                <p class="font-bold text-gray-800">{{ $item->mahasiswa->name ?? '-' }}</p>
                                                <p class="text-xs text-gray-500">{{ $item->mahasiswa->mahasiswaProfile->nim ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 text-[10px] font-bold rounded-full border
                                            {{ $item->jalur_magang == 'mandiri' ? 'bg-purple-100 text-purple-700 border-purple-200' : 'bg-blue-100 text-blue-700 border-blue-200' }}">
                                            {{ strtoupper($item->jalur_magang ?? 'REGULER') }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        @if($item->jalur_magang == 'mandiri')
                                            <p class="font-bold text-gray-800">{{ $item->nama_instansi_mandiri ?? '-' }}</p>
                                            <p class="text-xs text-gray-500">{{ $item->divisi_mandiri ?? 'Pengajuan Mandiri' }}</p>
                                        @else
                                            <p class="font-bold text-gray-800">{{ $item->lowongan->perusahaan->nama_perusahaan ?? '-' }}</p>
                                            <p class="text-xs text-gray-500">{{ $item->lowongan->judul_posisi ?? '-' }}</p>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        <p class="font-medium text-gray-700">{{ $item->created_at->format('d M Y') }}</p>
                                        <p class="text-xs text-gray-400">{{ $item->created_at->format('H:i') }} WITA</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($item->status_surat == 'terbit')
                                            <span class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full border border-green-200">
                                                <i class="fas fa-check-circle mr-1"></i> Surat Terbit
                                            </span>
                                        @elseif($item->status_surat == 'ditolak')
                                            <span class="inline-flex items-center px-2.5 py-1 bg-red-100 text-red-700 text-[10px] font-bold rounded-full border border-red-200">
                                                <i class="fas fa-times-circle mr-1"></i> Ditolak
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 bg-yellow-100 text-yellow-700 text-[10px] font-bold rounded-full border border-yellow-200">
                                                <i class="fas fa-spinner fa-spin mr-1"></i> Perlu Surat
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($item->status_surat == 'terbit')
                                            <div class="flex items-center justify-center gap-1.5">
                                                <button @click="activeData = {{ json_encode($item) }}; activeUrl = '{{ route('dashboard-daftar-lowongan-pengajuan-magang-terbit', $item->id) }}'; openModal = true" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold py-1.5 px-3 rounded transition-colors" title="Edit Surat">
                                                    <i class="fas fa-edit mr-1"></i> Edit
                                                </button>
                                            </div>
                                        @else
                                            <button @click="activeData = {{ json_encode($item) }}; activeUrl = '{{ route('dashboard-daftar-lowongan-pengajuan-magang-terbit', $item->id) }}'; autoNomor = '{{ rand(1000, 9999) }}/UN4.15/TU.02/{{ date('Y') }}'; openModal = true" 
                                                    class="bg-vokasi-primary hover:bg-vokasi-dark text-white text-xs font-bold py-1.5 px-3 rounded shadow-sm transition-colors flex items-center justify-center mx-auto">
                                                <i class="fas fa-file-export mr-1.5"></i> Terbit Surat
                                            </button>
                                        @endif
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
                    
                    <!-- Pagination Controls -->
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

        <!-- ========================================== -->
        <!-- MODAL POPUP: TERBITKAN SURAT PENGANTAR -->
        <!-- ========================================== -->
        <div x-show="openModal" 
             x-cloak 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            
            <div @click.away="openModal = false" class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-2xl overflow-hidden" x-if="activeData">
                
                <!-- Modal Header -->
                <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-file-signature text-lg"></i>
                        <h3 class="font-bold text-lg">Penerbitan Surat Pengantar Magang</h3>
                    </div>
                    <button @click="openModal = false" class="text-white/80 hover:text-white hover:bg-black/10 p-1.5 rounded-lg transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Modal Form -->
                <form :action="activeUrl" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-xs text-blue-800 flex items-center gap-3">
                        <i class="fas fa-circle-info text-blue-500 text-base shrink-0"></i>
                        <p>Sistem menyimpan nomor surat resmi dan memperbarui periode pelaksanaan magang mahasiswa.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <!-- Pemohon -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Mahasiswa</label>
                            <input type="text" :value="activeData?.mahasiswa?.name + ' (' + (activeData?.mahasiswa?.mahasiswa_profile?.nim || '-') + ')'" readonly class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-sm text-gray-600 font-medium cursor-not-allowed">
                        </div>

                        <!-- Perusahaan Tujuan -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Instansi / Perusahaan Tujuan</label>
                            <input type="text" :value="activeData?.jalur_magang === 'mandiri' ? activeData?.nama_instansi_mandiri : activeData?.lowongan?.perusahaan?.nama_perusahaan" readonly class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-sm text-gray-600 font-medium cursor-not-allowed">
                        </div>

                        <!-- Nomor Surat -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Surat Pengantar <span class="text-red-500">*</span></label>
                            <input type="text" name="nomor_surat" :value="activeData?.nomor_surat || autoNomor" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                        </div>

                        <!-- Perihal -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Perihal Surat <span class="text-red-500">*</span></label>
                            <input type="text" name="perihal_surat" :value="activeData?.perihal_surat || 'Permohonan Pelaksanaan Magang Industri Mahasiswa Vokasi'" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                        </div>

                        <!-- Tanggal Mulai -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Magang <span class="text-red-500">*</span></label>
                            <input type="date" name="tgl_mulai_magang" :value="activeData?.tgl_mulai_magang ? activeData?.tgl_mulai_magang.split('T')[0] : '2026-08-01'" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                        </div>

                        <!-- Tanggal Selesai -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai Magang <span class="text-red-500">*</span></label>
                            <input type="date" name="tgl_selesai_magang" :value="activeData?.tgl_selesai_magang ? activeData?.tgl_selesai_magang.split('T')[0] : '2027-01-31'" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-vokasi-light focus:border-vokasi-primary transition-colors" required>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-6">
                        <button type="button" @click="openModal = false" class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 font-medium text-sm transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-lg font-bold text-sm transition-colors shadow-sm flex items-center">
                            <i class="fas fa-save mr-2"></i> Terbitkan Surat
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
@endsection