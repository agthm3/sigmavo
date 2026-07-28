@extends('layouts.dashboard')

@section('content')
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col custom-scrollbar"
              x-data="{ openIzinModal: false }">
            <div class="flex-1 max-w-7xl mx-auto w-full">
                
                <!-- NOTIFIKASI SUKSES / ERROR -->
                @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900"><i class="fas fa-times"></i></button>
                </div>
                @endif

                @if($activeMagang)
                    @php
                        $isMandiri = $activeMagang->jalur_magang === 'mandiri';
                        $namaInstansi = $isMandiri ? $activeMagang->nama_instansi_mandiri : ($activeMagang->lowongan->perusahaan->nama_perusahaan ?? '-');
                        $posisi = $isMandiri ? ($activeMagang->divisi_mandiri ?? 'Pengajuan Mandiri') : ($activeMagang->lowongan->judul_posisi ?? '-');
                        $alamat = $isMandiri ? '-' : ($activeMagang->lowongan->perusahaan->alamat ?? '-');
                        $deskripsi = $isMandiri ? 'Magang Mandiri Mahasiswa Vokasi UNHAS.' : ($activeMagang->lowongan->deskripsi ?? '-');
                    @endphp

                    <!-- PROGRESS & STATUS CARD -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                        <div class="flex flex-col md:flex-row justify-between md:items-center mb-6 gap-4">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800">{{ $posisi }}</h3>
                                <div class="flex items-center mt-2 space-x-3">
                                    <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full flex items-center border border-green-200">
                                        <span class="w-2 h-2 rounded-full bg-green-500 mr-2 animate-pulse"></span> Sedang Berjalan
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        <i class="fas fa-calendar-alt mr-1"></i> 
                                        @if($activeMagang->tgl_mulai_magang && $activeMagang->tgl_selesai_magang)
                                            {{ \Carbon\Carbon::parse($activeMagang->tgl_mulai_magang)->format('d M Y') }} - {{ \Carbon\Carbon::parse($activeMagang->tgl_selesai_magang)->format('d M Y') }}
                                        @else
                                            Periode Berjalan
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('dashboard-mahasiswa-absensi') }}" class="bg-vokasi-primary hover:bg-vokasi-dark text-white text-sm font-semibold py-2 px-4 rounded-lg transition-colors shadow-sm flex items-center">
                                    <i class="fas fa-camera mr-2"></i> Absen Hari Ini
                                </a>
                                <a href="{{ route('dashboard-mahasiswa-logbook') }}" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-semibold py-2 px-4 rounded-lg transition-colors flex items-center">
                                    <i class="fas fa-pen mr-2"></i> Isi Logbook
                                </a>
                            </div>
                        </div>

                        <!-- 900 Hours Progress Bar -->
                        <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                            <div class="flex justify-between items-end mb-2">
                                <div>
                                    <p class="text-sm font-semibold text-gray-700">Progres Pemenuhan Jam Magang</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Sisa jam akan berkurang otomatis saat presensi & logbook diverifikasi.</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-2xl font-bold text-vokasi-primary">{{ $jamTerisi }}</span>
                                    <span class="text-gray-500 text-sm"> / {{ $targetJam }} Jam</span>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 mb-2 overflow-hidden">
                                <div class="bg-vokasi-primary h-3 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 font-medium">
                                <span>0 Jam</span>
                                <span class="text-orange-600 font-semibold"><i class="fas fa-info-circle mr-1"></i>Sisa: {{ $sisaJam }} Jam</span>
                                <span>{{ $targetJam }} Jam</span>
                            </div>
                        </div>
                    </div>

                    <!-- DETAIL CARDS GRID -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                        
                        <!-- Informasi Perusahaan -->
                        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h4 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2 flex items-center">
                                <i class="fas fa-building text-vokasi-primary mr-2"></i> Informasi Penempatan
                            </h4>
                            
                            <div class="flex flex-col md:flex-row gap-6">
                                <div class="w-full md:w-1/3 shrink-0">
                                    <div class="aspect-square bg-teal-50 rounded-xl border border-teal-100 flex items-center justify-center p-4">
                                        <div class="text-center">
                                            <i class="fas fa-robot text-5xl text-vokasi-primary mb-2"></i>
                                            <p class="font-bold text-gray-800 text-sm line-clamp-2">{{ $namaInstansi }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-full md:w-2/3 space-y-4">
                                    <div>
                                        <p class="text-xs text-gray-400 uppercase font-bold">Nama Instansi / Perusahaan</p>
                                        <p class="font-bold text-gray-800 text-lg">{{ $namaInstansi }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 uppercase font-bold">Posisi / Jabatan</p>
                                        <p class="font-medium text-vokasi-primary">{{ $posisi }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 uppercase font-bold">Lokasi Penempatan</p>
                                        <p class="font-medium text-gray-700 text-xs">{{ $alamat }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 uppercase font-bold">Deskripsi Tugas</p>
                                        <p class="text-xs text-gray-600 mt-1 leading-relaxed whitespace-pre-line">{{ $deskripsi }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Pembimbing -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col justify-between">
                            <div>
                                <h4 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2 flex items-center">
                                    <i class="fas fa-users text-vokasi-primary mr-2"></i> Pihak Terkait
                                </h4>
                                
                                <div class="space-y-6">
                                    <!-- Dosen Pendamping -->
                                    <div>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 block">Dosen Pendamping (Fakultas)</span>
                                        @if($activeMagang->dosen)
                                            <div class="flex items-center">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($activeMagang->dosen->name) }}&background=f3f4f6&color=37A7AC" alt="Dosen" class="w-10 h-10 rounded-full mr-3 border border-gray-200">
                                                <div>
                                                    <p class="font-bold text-gray-800 text-sm">{{ $activeMagang->dosen->name }}</p>
                                                    <p class="text-xs text-gray-500">NIP: {{ $activeMagang->dosen->dosenProfile->nip_nidn ?? '-' }}</p>
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-xs text-gray-400 italic"><i class="fas fa-info-circle mr-1"></i> Belum Ditetapkan</p>
                                        @endif
                                    </div>

                                    <hr class="border-gray-100">

                                    <!-- Supervisor Lapangan -->
                                    <div>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 block">Supervisor Lapangan (Mitra)</span>
                                        <div class="flex items-center">
                                            <img src="https://ui-avatars.com/api/?name=Supervisor+Lapangan&background=f3f4f6&color=6b7280" alt="Supervisor" class="w-10 h-10 rounded-full mr-3 border border-gray-200">
                                            <div>
                                                <p class="font-bold text-gray-800 text-sm">Supervisor Mitra</p>
                                                <p class="text-xs text-gray-500">{{ $namaInstansi }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick Action Ajukan Izin -->
                            <div class="mt-6 pt-4 border-t border-gray-100">
                                <button type="button" @click="openIzinModal = true" class="w-full bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold py-2.5 rounded-xl transition-colors border border-red-200 flex items-center justify-center">
                                    <i class="fas fa-notes-medical mr-2"></i> Ajukan Izin / Sakit
                                </button>
                            </div>
                        </div>

                    </div>
                @else
                    <!-- TAMPILAN JIKA BELUM ADA PROGRAM MAGANG AKTIF -->
                    <div class="bg-white p-12 text-center rounded-2xl border border-gray-200 shadow-sm max-w-2xl mx-auto my-8">
                        <div class="w-16 h-16 bg-teal-50 text-vokasi-primary rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Program Magang Aktif</h3>
                        <p class="text-sm text-gray-500 mb-6 leading-relaxed">
                            Anda saat ini belum terdaftar dalam program magang aktif. Silakan ajukan lamaran magang melalui katalog lowongan atau ajukan magang mandiri.
                        </p>
                        <div class="flex justify-center gap-3">
                            <a href="{{ route('dashboard-mahasiswa-daftar-lowongan') }}" class="px-5 py-2.5 bg-vokasi-primary hover:bg-vokasi-dark text-white font-bold text-xs rounded-xl shadow-sm transition-colors">
                                <i class="fas fa-search mr-1.5"></i> Cari Lowongan
                            </a>
                            <a href="{{ route('dashboard-mahasiswa-ajukan-mandiri') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition-colors">
                                Ajukan Mandiri
                            </a>
                        </div>
                    </div>
                @endif

            </div>

            <!-- FOOTER -->
            <footer class="mt-auto py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
                Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
            </footer>

            <!-- ========================================== -->
            <!-- MODAL POPUP: FORM AJUKAN IZIN / SAKIT -->
            <!-- ========================================== -->
            <div x-show="openIzinModal" 
                 x-cloak 
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                
                <div @click.away="openIzinModal = false" class="bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-lg overflow-hidden">
                    
                    <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-notes-medical text-lg"></i>
                            <h3 class="font-bold text-lg">Form Permohonan Izin / Sakit</h3>
                        </div>
                        <button @click="openIzinModal = false" class="text-white/80 hover:text-white p-1 rounded-lg">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <form action="{{ route('dashboard-mahasiswa-program-magang-izin') }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Jenis Permohonan <span class="text-red-500">*</span></label>
                            <select name="jenis_izin" required class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-primary">
                                <option value="sakit">Sakit / Surat Dokter</option>
                                <option value="izin">Izin Kepentingan Mendesak</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Dari Tanggal <span class="text-red-500">*</span></label>
                                <input type="date" name="tgl_mulai" required class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-primary">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Sampai Tanggal <span class="text-red-500">*</span></label>
                                <input type="date" name="tgl_selesai" required class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-primary">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Alasan / Keterangan <span class="text-red-500">*</span></label>
                            <textarea name="alasan" rows="3" required placeholder="Jelaskan alasan permohonan izin Anda..." class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-primary resize-none"></textarea>
                        </div>

                        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                            <button type="button" @click="openIzinModal = false" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-medium text-xs">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-xl font-bold text-xs shadow-sm">
                                Kirim Permohonan
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </main>
@endsection