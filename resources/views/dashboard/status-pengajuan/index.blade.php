@extends('layouts.dashboard')

@section('content')
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative custom-scrollbar">
            
            <div class="max-w-5xl mx-auto w-full flex-1">
                
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Status Pengajuan Magang</h2>
                        <p class="text-sm text-gray-500 mt-1">Pantau perkembangan seleksi dan verifikasi pengajuan magang Anda.</p>
                    </div>
                    
                    <!-- Filter Form -->
                    <form action="{{ route('dashboard-mahasiswa-status-pengajuan') }}" method="GET">
                        <select name="status" onchange="this.form.submit()" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-vokasi-primary focus:border-vokasi-primary block p-2 outline-none shadow-sm">
                            <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}>Semua Status</option>
                            <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Sedang Diproses (Menunggu)</option>
                            <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </form>
                </div>

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

                @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-circle text-red-600 text-lg"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900"><i class="fas fa-times"></i></button>
                </div>
                @endif

                <div class="space-y-6">
                    
                    @forelse($pendaftarans as $item)
                        @php
                            $isMandiri = $item->jalur_magang === 'mandiri';
                            $namaPerusahaan = $isMandiri ? $item->nama_instansi_mandiri : ($item->lowongan->perusahaan->nama_perusahaan ?? '-');
                            $judulPosisi = $isMandiri ? ($item->divisi_mandiri ?? 'Pengajuan Mandiri') : ($item->lowongan->judul_posisi ?? '-');
                        @endphp

                        <!-- KARTU PENGAJUAN -->
                        <div class="bg-white rounded-xl shadow-sm border transition-all overflow-hidden
                            {{ $item->status_seleksi == 'diterima' ? 'border-green-200' : '' }}
                            {{ $item->status_seleksi == 'ditolak' ? 'border-red-200 bg-red-50/10' : '' }}
                            {{ $item->status_seleksi == 'menunggu' ? 'border-gray-200 hover:border-vokasi-primary' : '' }}">
                            
                            <div class="p-5 lg:p-6">
                                <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-6">
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 rounded-lg flex items-center justify-center font-bold text-xl shrink-0
                                            {{ $item->status_seleksi == 'diterima' ? 'bg-green-50 text-green-600 border border-green-200' : '' }}
                                            {{ $item->status_seleksi == 'ditolak' ? 'bg-red-50 text-red-500 border border-red-200' : '' }}
                                            {{ $item->status_seleksi == 'menunggu' ? 'bg-orange-50 text-orange-500 border border-orange-100' : '' }}">
                                            <i class="{{ $item->status_seleksi == 'diterima' ? 'fas fa-check-circle' : ($item->status_seleksi == 'ditolak' ? 'fas fa-times-circle' : 'fas fa-building') }}"></i>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <h3 class="font-bold text-lg text-gray-800 {{ $item->status_seleksi == 'ditolak' ? 'line-through text-gray-500' : '' }}">
                                                    {{ $namaPerusahaan }}
                                                </h3>
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded border
                                                    {{ $isMandiri ? 'bg-purple-100 text-purple-700 border-purple-200' : 'bg-blue-100 text-blue-700 border-blue-200' }}">
                                                    {{ strtoupper($item->jalur_magang ?? 'REGULER') }}
                                                </span>
                                            </div>
                                            <p class="text-vokasi-primary font-medium text-sm">{{ $judulPosisi }}</p>
                                            <p class="text-xs text-gray-500 mt-1"><i class="far fa-clock mr-1"></i> Diajukan: {{ $item->created_at->format('d F Y') }}</p>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        @if($item->status_seleksi == 'diterima')
                                            <span class="inline-block bg-green-100 text-green-700 text-xs font-bold px-3 py-1.5 rounded-full border border-green-200 shadow-sm">
                                                <i class="fas fa-check-circle mr-1"></i> Diterima Magang
                                            </span>
                                        @elseif($item->status_seleksi == 'ditolak')
                                            <span class="inline-block bg-red-100 text-red-700 text-xs font-bold px-3 py-1.5 rounded-full border border-red-200">
                                                <i class="fas fa-times-circle mr-1"></i> Tidak Lolos Seleksi
                                            </span>
                                        @else
                                            <span class="inline-block bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1.5 rounded-full border border-yellow-200">
                                                <i class="fas fa-spinner fa-spin mr-1"></i> Menunggu Verifikasi
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Progress Timeline (Hanya untuk yang sedang aktif/diproses atau diterima) -->
                                @if($item->status_seleksi !== 'ditolak')
                                <div class="relative pt-2">
                                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                        <div class="w-full border-t-2 {{ $item->status_seleksi == 'diterima' ? 'border-green-400' : 'border-gray-200' }}"></div>
                                    </div>
                                    <div class="relative flex justify-between">
                                        <!-- Step 1: Diajukan -->
                                        <div class="flex flex-col items-center">
                                            <div class="h-8 w-8 rounded-full bg-vokasi-primary text-white flex items-center justify-center ring-4 ring-white z-10">
                                                <i class="fas fa-check text-sm"></i>
                                            </div>
                                            <span class="text-xs font-bold text-gray-800 mt-2">Diajukan</span>
                                        </div>

                                        <!-- Step 2: Verifikasi Admin/Dosen -->
                                        <div class="flex flex-col items-center">
                                            <div class="h-8 w-8 rounded-full {{ $item->status_seleksi == 'diterima' ? 'bg-vokasi-primary text-white' : 'bg-yellow-400 text-white animate-pulse' }} flex items-center justify-center ring-4 ring-white z-10">
                                                <i class="fas fa-search text-sm"></i>
                                            </div>
                                            <span class="text-xs font-bold {{ $item->status_seleksi == 'diterima' ? 'text-gray-800' : 'text-yellow-600' }} mt-2">Verifikasi Berkasi</span>
                                        </div>

                                        <!-- Step 3: Surat Pengantar -->
                                        <div class="flex flex-col items-center">
                                            <div class="h-8 w-8 rounded-full {{ $item->status_surat == 'terbit' ? 'bg-vokasi-primary text-white' : 'bg-gray-200 text-gray-400' }} flex items-center justify-center ring-4 ring-white z-10">
                                                <i class="fas fa-envelope-open-text text-sm"></i>
                                            </div>
                                            <span class="text-xs font-medium {{ $item->status_surat == 'terbit' ? 'text-gray-800 font-bold' : 'text-gray-400' }} mt-2">Surat Pengantar</span>
                                        </div>

                                        <!-- Step 4: Keputusan Akhir -->
                                        <div class="flex flex-col items-center">
                                            <div class="h-8 w-8 rounded-full {{ $item->status_seleksi == 'diterima' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400' }} flex items-center justify-center ring-4 ring-white z-10">
                                                <i class="fas fa-flag-checkered text-sm"></i>
                                            </div>
                                            <span class="text-xs font-medium {{ $item->status_seleksi == 'diterima' ? 'text-green-700 font-bold' : 'text-gray-400' }} mt-2">Keputusan Akhir</span>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Catatan Seleksi / Penolakan jika Ada -->
                                @if($item->catatan_seleksi)
                                <div class="mt-4 p-3 rounded-lg text-xs border
                                    {{ $item->status_seleksi == 'ditolak' ? 'bg-red-50 text-red-800 border-red-100' : 'bg-gray-50 text-gray-700 border-gray-200' }}">
                                    <strong>Catatan Pengelola:</strong> {{ $item->catatan_seleksi }}
                                </div>
                                @endif

                            </div>

                            <!-- Card Footer Action -->
                            <div class="bg-gray-50 p-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-3">
                                @if($item->status_seleksi == 'diterima')
                                    <span class="text-xs text-green-800 font-medium"><i class="fas fa-info-circle mr-1"></i> Selamat! Program magang ini telah disetujui.</span>
                                    <a href="{{ route('dashboard-mahasiswa-program-magang') }}" class="bg-vokasi-primary hover:bg-vokasi-dark text-white text-xs font-bold py-1.5 px-4 rounded-lg transition-colors shadow-sm">
                                        Buka Dashboard Magang
                                    </a>
                                @elseif($item->status_seleksi == 'menunggu')
                                    <span class="text-xs text-gray-500">Berkas sedang ditinjau oleh pengelola / prodi.</span>
                                    <form action="{{ route('dashboard-mahasiswa-status-pengajuan-cancel', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pengajuan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-700 transition-colors">
                                            Batalkan Pengajuan
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">Pengajuan ini tidak lolos seleksi. Anda dapat melamar lowongan lainnya.</span>
                                @endif
                            </div>

                        </div>
                    @empty
                        <div class="bg-white p-12 text-center rounded-2xl border border-gray-200 text-gray-400">
                            <i class="fas fa-folder-open text-4xl mb-3 block"></i>
                            <p class="font-bold text-gray-600 text-base">Belum Ada Pengajuan Magang</p>
                            <p class="text-xs mt-1">Anda belum melamar lowongan apa pun. Silakan kunjungi menu <a href="{{ route('dashboard-mahasiswa-daftar-lowongan') }}" class="text-vokasi-primary underline font-semibold">Daftar Lowongan</a> atau <a href="{{ route('dashboard-mahasiswa-ajukan-mandiri') }}" class="text-vokasi-primary underline font-semibold">Ajukan Mandiri</a>.</p>
                        </div>
                    @endforelse

                </div>
            </div>

            <!-- FOOTER -->
            <footer class="mt-8 py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
                Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
            </footer>

        </main>
@endsection