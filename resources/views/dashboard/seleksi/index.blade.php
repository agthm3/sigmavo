@extends('layouts.dashboard')

@section('content')
    <!-- Tambahkan 'formStatus' di x-data untuk mendeteksi perubahan dropdown secara realtime -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50" 
         x-data="{ openModal: false, activeData: null, activeUrl: '', formStatus: '' }">

        <!-- CONTENT -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative custom-scrollbar">
            
            <div class="max-w-7xl mx-auto w-full flex-1 space-y-6">
                
                <!-- Page Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Seleksi & Penempatan Pelamar</h2>
                        <p class="text-sm text-gray-500 mt-1">Tinjau berkas pendaftaran mahasiswa (Reguler & Mandiri), tetapkan status seleksi, dan alokasikan Dosen Pendamping.</p>
                    </div>
                </div>

                <!-- NOTIFIKASI SUKSES / ERROR (Pesan Sukses Bisa Menampilkan Password Default) -->
                @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-start gap-3 text-sm shadow-sm">
                    <i class="fas fa-check-circle text-emerald-600 text-lg mt-0.5"></i>
                    <div class="leading-relaxed">
                        {{ session('success') }}
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 ml-auto"><i class="fas fa-times"></i></button>
                </div>
                @endif

                <!-- SUMMARY CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-user-group text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Total Pelamar</p>
                            <p class="text-xl font-bold text-gray-800 leading-none mt-1">{{ $totalPelamar }} Orang</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-yellow-50 text-yellow-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-hourglass-start text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Belum Diseleksi</p>
                            <p class="text-xl font-bold text-yellow-600 leading-none mt-1">{{ $totalMenunggu }} Pelamar</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-user-check text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Diterima Magang</p>
                            <p class="text-xl font-bold text-green-600 leading-none mt-1">{{ $totalDiterima }} Orang</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-user-xmark text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Ditolak / Gugur</p>
                            <p class="text-xl font-bold text-red-600 leading-none mt-1">{{ $totalDitolak }} Orang</p>
                        </div>
                    </div>
                </div>

                <!-- TABLE SECTION -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden">
                    
                    <!-- Table Toolbar / Filter -->
                    <form action="{{ route('dashboard-daftar-lowongan-seleksi') }}" method="GET" class="p-5 border-b border-gray-100 flex flex-col lg:flex-row justify-between lg:items-center gap-4 bg-gray-50/50">
                        <div class="relative w-full lg:w-96">
                            <i class="fas fa-search absolute left-3.5 top-3 text-gray-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama mahasiswa, NIM, atau posisi..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-primary/20">
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            <select name="lowongan_id" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-xs rounded-xl focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm font-semibold">
                                <option value="semua" {{ request('lowongan_id') == 'semua' ? 'selected' : '' }}>Semua Posisi Lowongan</option>
                                @foreach($lowongans as $l)
                                    <option value="{{ $l->id }}" {{ request('lowongan_id') == $l->id ? 'selected' : '' }}>{{ $l->judul_posisi }} ({{ $l->perusahaan->nama_perusahaan ?? '' }})</option>
                                @endforeach
                            </select>

                            <select name="status" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-xs rounded-xl focus:ring-vokasi-primary outline-none px-3 py-2 shadow-sm font-semibold">
                                <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}>Semua Status</option>
                                <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu Seleksi</option>
                                <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                    </form>

                    <!-- Responsive Table -->
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[1100px]">
                            <thead>
                                <tr class="bg-gray-100/60 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <th class="p-4 w-12 text-center">No</th>
                                    <th class="p-4 w-60">Mahasiswa Pelamar</th>
                                    <th class="p-4 w-56">Posisi & Perusahaan</th>
                                    <th class="p-4 w-32 text-center">Jalur Magang</th>
                                    <th class="p-4 w-48">Dosen Pendamping</th>
                                    <th class="p-4 w-32 text-center">Status Seleksi</th>
                                    <th class="p-4 w-36 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100 text-gray-700">
                                
                                @forelse($pendaftarans as $index => $item)
                                <tr class="hover:bg-gray-50 transition-colors {{ $item->status_seleksi == 'menunggu' ? 'bg-yellow-50/20' : '' }}">
                                    <td class="p-4 text-center text-gray-500 font-medium text-xs">{{ $pendaftarans->firstItem() + $index }}</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($item->user->name ?? 'Mhs') }}&background=f3f4f6&color=37A7AC" alt="Avatar" class="w-9 h-9 rounded-full border border-gray-200 shrink-0">
                                            <div>
                                                <p class="font-bold text-gray-800 text-xs">{{ $item->user->name ?? '-' }}</p>
                                                <p class="text-[11px] text-gray-500">NIM: {{ $item->user->mahasiswaProfile->nim ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        @if($item->jalur_magang === 'mandiri' || !empty($item->nama_instansi_mandiri))
                                            <p class="font-bold text-gray-800 text-xs">{{ $item->divisi_mandiri ?? 'Pengajuan Mandiri' }}</p>
                                            <p class="text-xs text-purple-600 font-bold"><i class="fas fa-building mr-1"></i> {{ $item->nama_instansi_mandiri }}</p>
                                        @else
                                            <p class="font-bold text-gray-800 text-xs">{{ $item->lowongan->judul_posisi ?? '-' }}</p>
                                            <p class="text-xs text-vokasi-primary font-medium">{{ $item->lowongan->perusahaan->nama_perusahaan ?? '-' }}</p>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($item->jalur_magang === 'mandiri' || !empty($item->nama_instansi_mandiri))
                                            <span class="inline-flex items-center px-2.5 py-1 bg-purple-50 text-purple-700 text-[10px] font-bold rounded-full border border-purple-200">
                                                <i class="fas fa-user-gear mr-1"></i> Mandiri
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 bg-blue-50 text-blue-700 text-[10px] font-bold rounded-full border border-blue-200">
                                                <i class="fas fa-handshake mr-1"></i> Reguler Mitra
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        @if($item->dosen)
                                            <p class="font-bold text-gray-800 text-xs">{{ $item->dosen->name }}</p>
                                            <p class="text-[10px] text-emerald-600 font-semibold"><i class="fas fa-check-circle"></i> Assigned</p>
                                        @else
                                            <span class="text-xs text-gray-400 italic"><i class="fas fa-user-plus mr-1"></i> Belum Ditetapkan</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($item->status_seleksi == 'diterima')
                                            <span class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-full border border-emerald-200">
                                                <i class="fas fa-check-circle mr-1"></i> Diterima
                                            </span>
                                        @elseif($item->status_seleksi == 'ditolak')
                                            <span class="inline-flex items-center px-2.5 py-1 bg-red-50 text-red-700 text-[10px] font-bold rounded-full border border-red-200">
                                                <i class="fas fa-times-circle mr-1"></i> Ditolak
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 bg-amber-50 text-amber-700 text-[10px] font-bold rounded-full border border-amber-200">
                                                <i class="fas fa-spinner fa-spin mr-1"></i> Menunggu
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        <!-- Update state Alpine.js formStatus saat tombol diklik -->
                                        <button type="button" @click="activeData = {{ json_encode($item) }}; formStatus = '{{ $item->status_seleksi }}'; activeUrl = '{{ route('dashboard-daftar-lowongan-seleksi-update', $item->id) }}'; openModal = true" 
                                                class="bg-vokasi-primary hover:bg-vokasi-dark text-white text-xs font-bold py-1.5 px-3 rounded-lg shadow-sm transition-colors flex items-center justify-center mx-auto">
                                            <i class="fas fa-file-signature mr-1.5"></i> Tinjau Berkas
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-gray-400">
                                        <i class="fas fa-user-xmark text-3xl mb-2 block"></i> Belum ada data pelamar magang.
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
        </main>

        <!-- ========================================== -->
        <!-- MODAL POPUP: TINJAU BERKAS & SELEKSI PELAMAR -->
        <!-- ========================================== -->
        <div x-show="openModal" 
             x-cloak 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            
            <div @click.away="openModal = false" class="bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-3xl overflow-hidden max-h-[92vh] flex flex-col" x-if="activeData">
                
                <!-- Modal Header -->
                <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center shrink-0">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-user-check text-lg"></i>
                        <h3 class="font-bold text-base">Tinjau Berkas & Keputusan Seleksi</h3>
                    </div>
                    <button type="button" @click="openModal = false" class="text-white/80 hover:text-white hover:bg-black/10 p-1.5 rounded-lg transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="p-6 space-y-5 overflow-y-auto custom-scrollbar flex-1 text-xs">
                                        
                    <!-- Profil Pelamar Summary -->
                    <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-200">
                        <img :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent(activeData?.user?.name || 'Mhs') + '&background=37A7AC&color=fff'" alt="Pelamar" class="w-14 h-14 rounded-full border-2 border-white shadow-sm shrink-0">
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-bold text-base text-gray-800" x-text="activeData?.user?.name"></h4>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        NIM: <span class="font-mono font-bold text-gray-700" x-text="activeData?.user?.mahasiswa_profile?.nim || '-'"></span> • 
                                        <span x-text="activeData?.user?.mahasiswa_profile?.prodi?.nama_prodi || '-'"></span>
                                    </p>
                                </div>
                                <template x-if="activeData?.jalur_magang === 'mandiri' || activeData?.nama_instansi_mandiri">
                                    <span class="px-3 py-1 bg-purple-100 text-purple-700 text-[10px] font-bold rounded-full border border-purple-200">
                                        <i class="fas fa-user-gear mr-1"></i> Pengajuan Mandiri
                                    </span>
                                </template>
                                <template x-if="activeData?.jalur_magang !== 'mandiri' && !activeData?.nama_instansi_mandiri">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-[10px] font-bold rounded-full border border-blue-200">
                                        <i class="fas fa-handshake mr-1"></i> Magang Reguler Mitra
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- TAMPILAN DETAIL PENGAJUAN MANDIRI -->
                    <template x-if="activeData?.jalur_magang === 'mandiri' || activeData?.nama_instansi_mandiri">
                        <div class="space-y-4">
                            <!-- Detail Instansi Mandiri -->
                            <div class="p-4 bg-purple-50/60 border border-purple-200 rounded-2xl space-y-3">
                                <h5 class="font-bold text-purple-900 text-xs uppercase tracking-wider flex items-center">
                                    <i class="fas fa-building text-purple-600 mr-2"></i> Detail Instansi & Posisi Mandiri
                                </h5>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-gray-500">Nama Instansi/Perusahaan:</p>
                                        <p class="font-bold text-gray-800 text-sm" x-text="activeData?.nama_instansi_mandiri"></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Posisi / Departemen:</p>
                                        <p class="font-bold text-vokasi-primary text-sm" x-text="activeData?.divisi_mandiri"></p>
                                    </div>
                                    <template x-if="activeData?.tgl_mulai_magang && activeData?.tgl_selesai_magang">
                                        <div class="md:col-span-2">
                                            <p class="text-gray-500">Periode Rencana Magang:</p>
                                            <p class="font-bold text-gray-800" x-text="activeData?.tgl_mulai_magang + ' s.d ' + activeData?.tgl_selesai_magang"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Catatan Jobdesc & Supervisor jika tersimpan -->
                            <template x-if="activeData?.catatan_seleksi && activeData?.catatan_seleksi.includes('Supervisor')">
                                <div class="p-4 bg-gray-50 border border-gray-200 rounded-2xl space-y-1">
                                    <h5 class="font-bold text-gray-800 text-xs uppercase mb-1"><i class="fas fa-user-tie text-vokasi-primary mr-1.5"></i> Ringkasan Supervisor & Jobdesc Mandiri:</h5>
                                    <p class="text-gray-700 leading-relaxed font-medium whitespace-pre-line" x-text="activeData?.catatan_seleksi"></p>
                                </div>
                            </template>

                            <!-- Download Surat Balasan Mandiri -->
                            <template x-if="activeData?.surat_balasan">
                                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-between">
                                    <div>
                                        <p class="font-bold text-emerald-900">Surat Balasan / Bukti Diterima Magang</p>
                                        <p class="text-[11px] text-emerald-700 mt-0.5">Lampiran resmi dari instansi mitra.</p>
                                    </div>
                                    <a :href="'{{ asset('storage') }}/' + activeData?.surat_balasan" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition shadow-sm flex items-center gap-1.5 shrink-0">
                                        <i class="fas fa-download"></i> Unduh Surat
                                    </a>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- TAMPILAN DETAIL PENGAJUAN REGULER MITRA -->
                    <template x-if="activeData?.jalur_magang !== 'mandiri' && !activeData?.nama_instansi_mandiri">
                        <div class="p-4 bg-blue-50/60 border border-blue-200 rounded-2xl space-y-2">
                            <h5 class="font-bold text-blue-900 text-xs uppercase tracking-wider flex items-center">
                                <i class="fas fa-briefcase text-blue-600 mr-2"></i> Lowongan Mitra Terdaftar
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                <div>
                                    <p class="text-gray-500">Posisi Lowongan:</p>
                                    <p class="font-bold text-gray-800" x-text="activeData?.lowongan?.judul_posisi"></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Perusahaan Mitra:</p>
                                    <p class="font-bold text-vokasi-primary" x-text="activeData?.lowongan?.perusahaan?.nama_perusahaan"></p>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- BERKAS CV & TRANSKRIP (BERLAKU UNTUK SEMUA) -->
                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-2xl space-y-3">
                        <h5 class="font-bold text-gray-800 text-xs uppercase tracking-wider flex items-center">
                            <i class="fas fa-folder-open text-vokasi-primary mr-2"></i> Berkas Pendukung Mahasiswa
                        </h5>
                        <div class="flex flex-wrap gap-3">
                            <template x-if="activeData?.file_cv">
                                <a :href="'{{ asset('storage') }}/' + activeData?.file_cv" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-white border border-gray-300 hover:border-vokasi-primary text-gray-700 hover:text-vokasi-primary font-bold rounded-xl transition shadow-sm">
                                    <i class="fas fa-file-pdf text-red-500 mr-2"></i> Curiculum Vitae (CV)
                                </a>
                            </template>
                            <template x-if="!activeData?.file_cv">
                                <span class="text-gray-400 italic">CV belum diunggah</span>
                            </template>

                            <template x-if="activeData?.file_transkrip">
                                <a :href="'{{ asset('storage') }}/' + activeData?.file_transkrip" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-white border border-gray-300 hover:border-vokasi-primary text-gray-700 hover:text-vokasi-primary font-bold rounded-xl transition shadow-sm">
                                    <i class="fas fa-file-pdf text-red-500 mr-2"></i> Transkrip Nilai
                                </a>
                            </template>
                        </div>
                    </div>

                    <!-- Form Keputusan Seleksi -->
                    <form :action="activeUrl" method="POST" class="pt-2">
                        @csrf
                        @method('PUT')

                        <h5 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-2 flex items-center mb-4">
                            <i class="fas fa-user-gear text-vokasi-primary mr-2"></i> Form Keputusan & Penugasan
                        </h5>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Keputusan Status (Dilengkapi x-model) -->
                            <div>
                                <label class="block font-bold text-gray-700 uppercase mb-1">Keputusan Seleksi <span class="text-red-500">*</span></label>
                                <select name="status_seleksi" x-model="formStatus" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-primary" required>
                                    <option value="menunggu">Menunggu Seleksi</option>
                                    <option value="diterima">Diterima Magang</option>
                                    <option value="ditolak">Ditolak / Tidak Lolos</option>
                                    <option value="wawancara">Panggil Wawancara</option>
                                </select>
                            </div>

                            <!-- Assign Dosen Pendamping -->
                            <div>
                                <label class="block font-bold text-gray-700 uppercase mb-1">Assign Dosen Pendamping</label>
                                <select name="dosen_id" :value="activeData?.dosen_id" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-primary">
                                    <option value="">-- Pilih Dosen Pendamping --</option>
                                    @foreach($dosens as $d)
                                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Catatan Seleksi -->
                            <div class="md:col-span-2">
                                <label class="block font-bold text-gray-700 uppercase mb-1">Catatan Tambahan / Alasan Penolakan</label>
                                <!-- Kita hide form ini jika mandiri agar tidak menimpa ringkasan SPV, atau beri peringatan -->
                                <template x-if="activeData?.jalur_magang === 'mandiri'">
                                    <p class="text-[11px] text-amber-600 font-bold mb-1"><i class="fas fa-exclamation-triangle"></i> Hati-hati mengubah catatan ini karena berisi data SPV.</p>
                                </template>
                                <textarea name="catatan_seleksi" :value="activeData?.catatan_seleksi" rows="3" placeholder="Tuliskan catatan khusus untuk mahasiswa..." class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-primary resize-none"></textarea>
                            </div>
                        </div>

                        <!-- WARNING AUTO-CREATE SPV (Muncul Secara Realtime) -->
                        <template x-if="(activeData?.jalur_magang === 'mandiri' || activeData?.nama_instansi_mandiri) && formStatus === 'diterima' && activeData?.status_seleksi !== 'diterima'">
                            <div class="mt-4 p-4 bg-purple-50 border border-purple-200 rounded-xl flex items-start gap-3 shadow-sm">
                                <i class="fas fa-user-shield text-purple-600 text-xl mt-0.5"></i>
                                <div>
                                    <h4 class="font-bold text-xs text-purple-900 uppercase">Pembuatan Akun Supervisor Otomatis</h4>
                                    <p class="text-[11px] text-purple-800 mt-1 leading-relaxed">
                                        Menyimpan keputusan ini akan <strong>secara otomatis membuatkan akun SPV</strong> menggunakan email yang diinput mahasiswa. 
                                        Password default akan di-generate dan muncul di notifikasi sukses setelah Anda klik Simpan.
                                    </p>
                                </div>
                            </div>
                        </template>

                        <!-- Modal Footer -->
                        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-6 shrink-0">
                            <button type="button" @click="openModal = false" class="px-5 py-2 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-bold text-xs transition-colors">
                                Batal
                            </button>
                            <button type="submit" class="px-6 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-xl font-bold text-xs transition-colors shadow-sm flex items-center">
                                <i class="fas fa-check mr-2"></i> Simpan Keputusan
                            </button>
                        </div>
                    </form>

                </div>

            </div>
        </div>

    </div>
@endsection