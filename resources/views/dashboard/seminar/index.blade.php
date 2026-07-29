@extends('layouts.dashboard')

@section('content')
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-6 flex flex-col relative custom-scrollbar"
      x-data="{ openSetModal: false, activeMhs: null, activeSeminar: null, activeActionUrl: '' }">
    
    <div class="max-w-7xl mx-auto w-full flex-1">
        
        <!-- HEADER HALAMAN -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Seminar Hasil Magang</h2>
                <p class="text-sm text-gray-500 mt-1">Informasi pendaftaran, penjadwalan, dewan penguji, dan penilaian seminar hasil.</p>
            </div>

            @if(isset($user) && !$user->hasRole('mahasiswa'))
                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-teal-50 text-vokasi-primary border border-vokasi-primary/20">
                    <i class="fas fa-user-shield mr-1.5"></i> Mode Pengelola Seminar
                </span>
            @endif
        </div>

        <!-- NOTIFIKASI SUKSES / ERROR -->
        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center justify-between text-sm shadow-sm">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900"><i class="fas fa-times"></i></button>
        </div>
        @endif

        @if(isset($user) && $user->hasAnyRole(['admin', 'superadmin', 'admin_prodi']))
            <!-- ========================================================= -->
            <!-- STATE ADMIN: TABEL KELOLA JADWAL SEMINAR MAHASISWA        -->
            <!-- ========================================================= -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden space-y-4">
                
                <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex flex-col md:flex-row justify-between md:items-center gap-4">
                    <div>
                        <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-vokasi-primary"></i> Pengelolaan Jadwal & Penguji Seminar
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">Plotting tanggal ujian, ruang sidang, dan dewan penguji untuk setiap mahasiswa.</p>
                    </div>

                    <!-- Search Box -->
                    <form action="{{ route('dashboard-mahasiswa-seminar') }}" method="GET" class="flex gap-2">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / NIM..." class="pl-8 pr-3 py-1.5 bg-white border border-gray-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-vokasi-primary">
                        </div>
                        <button type="submit" class="px-3 py-1.5 bg-vokasi-primary text-white text-xs font-bold rounded-xl hover:bg-vokasi-dark">Cari</button>
                    </form>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-100/60 border-b border-gray-200 font-semibold text-gray-600 uppercase tracking-wider">
                                <th class="p-3.5 w-10 text-center">No</th>
                                <th class="p-3.5">Mahasiswa & Prodi</th>
                                <th class="p-3.5 w-48">Jadwal & Ruangan</th>
                                <th class="p-3.5">Dewan Penguji</th>
                                <th class="p-3.5 w-24 text-center">Status</th>
                                <th class="p-3.5 w-20 text-center">Nilai</th>
                                <th class="p-3.5 w-28 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($mahasiswas as $idx => $mhs)
                            @php $sem = $mhs->seminars->first(); @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-3.5 text-center text-gray-500 font-medium">{{ $mahasiswas->firstItem() + $idx }}</td>
                                <td class="p-3.5">
                                    <p class="font-bold text-gray-800 text-xs">{{ $mhs->name }}</p>
                                    <p class="text-[10px] text-gray-400">NIM: {{ $mhs->mahasiswaProfile->nim ?? '-' }} • {{ $mhs->mahasiswaProfile->prodi->nama_prodi ?? '-' }}</p>
                                </td>
                                <td class="p-3.5">
                                    @if($sem && $sem->waktu_seminar)
                                        <p class="font-bold text-gray-800">{{ $sem->waktu_seminar->format('d M Y, H:i') }} WITA</p>
                                        <p class="text-[10px] text-gray-500"><i class="fas fa-map-marker-alt text-red-500 mr-1"></i> {{ $sem->lokasi_ruangan ?? '-' }}</p>
                                    @else
                                        <span class="text-amber-600 italic font-semibold text-[11px]">Belum Dijadwalkan</span>
                                    @endif
                                </td>
                                <td class="p-3.5 space-y-0.5 text-[11px]">
                                    <p><strong class="text-vokasi-primary">Pembimbing:</strong> {{ $sem->pembimbing->name ?? '-' }}</p>
                                    <p><strong class="text-gray-600">Penguji 1:</strong> {{ $sem->penguji1->name ?? '-' }}</p>
                                    <p><strong class="text-gray-600">Penguji 2:</strong> {{ $sem->penguji2->name ?? '-' }}</p>
                                </td>
                                <td class="p-3.5 text-center">
                                    @if($sem && $sem->status_seminar === 'dijadwalkan')
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 font-bold rounded-full text-[10px]">Dijadwalkan</span>
                                    @elseif($sem && $sem->status_seminar === 'selesai')
                                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 font-bold rounded-full text-[10px]">Selesai</span>
                                    @elseif($sem && $sem->status_seminar === 'mengajukan')
                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-700 font-bold rounded-full text-[10px]">Mengajukan</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-500 font-bold rounded-full text-[10px]">Belum</span>
                                    @endif
                                </td>
                                <td class="p-3.5 text-center font-bold text-sm text-vokasi-primary">
                                    {{ $sem->nilai_akhir ?? '-' }}
                                </td>
                                <td class="p-3.5 text-center">
                                    <button type="button" @click="
                                        activeMhs = {{ json_encode($mhs) }};
                                        activeSeminar = {{ json_encode($sem) }};
                                        activeActionUrl = '{{ route('dashboard-admin-seminar-set', $mhs->id) }}';
                                        openSetModal = true;
                                    " class="px-3 py-1 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-lg font-bold text-[11px] shadow-sm transition-colors">
                                        <i class="fas fa-edit mr-1"></i> Atur Ujian
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-gray-400">Belum ada data mahasiswa ditemukan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-100 bg-white">
                    {{ $mahasiswas->links() }}
                </div>

            </div>

        @else

            <!-- ========================================================= -->
            <!-- STATE MAHASISWA: DETAIL SAYA                              -->
            <!-- ========================================================= -->
            @if(!$layakSeminar)
                <!-- STATE TERKUNCI MAHASISWA -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 max-w-3xl mx-auto my-6 text-center">
                    <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-5 text-3xl shadow-inner border border-red-100">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Akses Seminar Hasil Belum Terbuka</h3>
                    <p class="text-sm text-gray-500 mb-8 max-w-lg mx-auto leading-relaxed">
                        Anda belum dapat mendaftar atau melihat jadwal seminar hasil magang. Selesaikan persyaratan akademik berikut untuk membuka akses.
                    </p>

                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 text-left max-w-md mx-auto space-y-4">
                        <h4 class="font-bold text-xs uppercase tracking-wider text-gray-500 mb-3 border-b border-gray-200 pb-2">Status Syarat Kelayakan:</h4>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                @if($sudahPembekalan)
                                    <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-bold"><i class="fas fa-check"></i></div>
                                @else
                                    <div class="w-6 h-6 rounded-full bg-red-100 text-red-500 flex items-center justify-center text-xs font-bold"><i class="fas fa-times"></i></div>
                                @endif
                                <div>
                                    <p class="text-xs font-bold text-gray-800">1. Kehadiran Pembekalan Magang</p>
                                    <p class="text-[11px] text-gray-500">{{ $sudahPembekalan ? 'Sudah Mengikuti Pembekalan' : 'Belum Konfirmasi Kehadiran' }}</p>
                                </div>
                            </div>
                            @if(!$sudahPembekalan)
                                <a href="{{ route('dashboard-mahasiswa-pembekalan-magang') }}" class="text-[11px] font-bold text-vokasi-primary hover:underline">Ikuti</a>
                            @endif
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                @if($jamMemenuhi)
                                    <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-bold"><i class="fas fa-check"></i></div>
                                @else
                                    <div class="w-6 h-6 rounded-full bg-red-100 text-red-500 flex items-center justify-center text-xs font-bold"><i class="fas fa-times"></i></div>
                                @endif
                                <div>
                                    <p class="text-xs font-bold text-gray-800">2. Pemenuhan Target {{ $targetJam }} Jam Magang</p>
                                    <p class="text-[11px] text-gray-500">Capaian Saat Ini: <strong>{{ $jamTercapai }} / {{ $targetJam }} Jam</strong></p>
                                </div>
                            </div>
                            @if(!$jamMemenuhi)
                                <a href="{{ route('dashboard-mahasiswa-logbook') }}" class="text-[11px] font-bold text-vokasi-primary hover:underline">Isi Logbook</a>
                            @endif
                        </div>
                    </div>
                </div>

            @else

                <!-- STATE LAYAK SEMINAR HASIL MAHASISWA -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="border-b border-gray-100 p-4 bg-gray-50/50">
                                <h3 class="font-bold text-gray-800 flex items-center text-xs uppercase tracking-wider">
                                    <i class="fas fa-tasks text-vokasi-primary mr-2"></i> Status Kelayakan Seminar
                                </h3>
                            </div>
                            <div class="p-5 space-y-4 text-xs">
                                <div class="flex items-start gap-3">
                                    <div class="text-emerald-500 mt-0.5"><i class="fas fa-check-circle text-base"></i></div>
                                    <div>
                                        <p class="font-bold text-gray-800">Kehadiran Pembekalan</p>
                                        <p class="text-[11px] text-gray-500">Telah Mengikuti Pembekalan</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <div class="text-emerald-500 mt-0.5"><i class="fas fa-check-circle text-base"></i></div>
                                    <div>
                                        <p class="font-bold text-gray-800">Pemenuhan {{ $targetJam }} Jam Magang</p>
                                        <p class="text-[11px] text-gray-500">Tercapai {{ $jamTercapai }} / {{ $targetJam }} Jam</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-emerald-50 p-3 border-t border-emerald-100 text-center">
                                <span class="text-xs font-bold text-emerald-700"><i class="fas fa-user-check mr-1"></i> Syarat Lengkap. Anda Siap Seminar!</span>
                            </div>
                        </div>

                        <!-- Form Upload Presentasi PPT -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 space-y-3">
                            <h3 class="font-bold text-gray-800 text-xs uppercase tracking-wider border-b border-gray-100 pb-2 flex items-center">
                                <i class="fas fa-file-powerpoint text-orange-500 mr-2 text-sm"></i> Bahan Presentasi (PPT)
                            </h3>
                            <p class="text-xs text-gray-500 leading-relaxed">
                                Unggah berkas presentasi (.pptx / .pdf maks 10MB) untuk dipelajari dewan penguji.
                            </p>
                            
                            <form action="{{ route('dashboard-mahasiswa-seminar-store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                        <div class="flex flex-col items-center justify-center text-center p-2">
                                            <i class="fas fa-cloud-upload-alt text-gray-400 text-xl mb-1"></i>
                                            <p class="text-xs text-gray-600"><span class="font-bold text-vokasi-primary">Klik unggah</span> presentasi</p>
                                        </div>
                                        <input type="file" name="file_ppt" class="hidden" accept=".ppt,.pptx,.pdf" onchange="this.form.submit()" />
                                    </label>
                                </div>
                            </form>

                            @if($seminar && $seminar->file_ppt)
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 flex justify-between items-center text-xs">
                                <div class="flex items-center gap-2 overflow-hidden">
                                    <i class="fas fa-file-powerpoint text-orange-500 text-lg shrink-0"></i>
                                    <span class="font-bold text-blue-900 truncate">Berkas Presentasi Terunggah</span>
                                </div>
                                <form action="{{ route('dashboard-mahasiswa-seminar-destroy-ppt') }}" method="POST" onsubmit="return confirm('Hapus berkas presentasi?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-1" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-vokasi-dark p-6 text-white relative overflow-hidden">
                                <i class="fas fa-chalkboard-teacher absolute text-9xl right-0 -bottom-4 opacity-10"></i>
                                <div class="relative z-10">
                                    <h3 class="text-xs font-bold opacity-90 uppercase tracking-widest text-vokasi-light mb-1">Jadwal Ujian Hasil Anda</h3>
                                    @if($seminar && $seminar->waktu_seminar)
                                        <p class="text-2xl font-extrabold mb-2">{{ $seminar->waktu_seminar->isoFormat('dddd, D MMMM YYYY') }}</p>
                                        <div class="flex flex-wrap items-center gap-3 text-xs font-medium">
                                            <span class="bg-black/30 px-3 py-1 rounded-lg"><i class="far fa-clock mr-1.5"></i> {{ $seminar->waktu_seminar->format('H:i') }} WITA</span>
                                            <span class="bg-black/30 px-3 py-1 rounded-lg"><i class="fas fa-map-marker-alt mr-1.5"></i> {{ $seminar->lokasi_ruangan ?? 'Ruang Sidang Vokasi' }}</span>
                                        </div>
                                    @else
                                        <p class="text-xl font-bold mb-1 text-amber-300">Jadwal Belum Ditetapkan</p>
                                        <p class="text-xs text-gray-300">Pengelola sedang menyusun jadwal dan dewan penguji seminar Anda.</p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="p-6 text-xs text-gray-600 space-y-3">
                                <h4 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-2">Ketentuan Ujian:</h4>
                                <ul class="list-disc list-inside space-y-1.5 leading-relaxed">
                                    <li>Wajib mengenakan jas almamater Universitas Hasanuddin dan kemeja putih rapi.</li>
                                    <li>Hadir di lokasi 15 menit sebelum waktu ujian dimulai.</li>
                                    <li>Membawa salinan cetak draft laporan akhir sebanyak 3 rangkap.</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Dewan Penguji & Pembimbing -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-4">
                            <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-3 flex items-center text-xs uppercase tracking-wider">
                                <i class="fas fa-users text-vokasi-primary mr-2 text-sm"></i> Tim Penilai & Penguji
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                <div class="border border-gray-200 rounded-xl p-3.5 bg-gray-50/50">
                                    <span class="text-[10px] font-bold text-vokasi-primary uppercase tracking-wider mb-2 block">Dosen Pembimbing</span>
                                    <div class="flex items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($seminar->pembimbing->name ?? 'Dosen Pembimbing') }}&background=37A7AC&color=fff" class="w-10 h-10 rounded-full mr-3 border shadow-sm">
                                        <div>
                                            <p class="font-bold text-gray-800 text-xs">{{ $seminar->pembimbing->name ?? 'Belum Ditentukan' }}</p>
                                            <p class="text-[10px] text-gray-400">Ketua Sidang</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="border border-gray-200 rounded-xl p-3.5 bg-gray-50/50">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 block">Dosen Penguji I</span>
                                    <div class="flex items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($seminar->penguji1->name ?? 'Penguji 1') }}&background=f3f4f6&color=6b7280" class="w-10 h-10 rounded-full mr-3 border shadow-sm">
                                        <div>
                                            <p class="font-bold text-gray-800 text-xs">{{ $seminar->penguji1->name ?? 'Belum Ditentukan' }}</p>
                                            <p class="text-[10px] text-gray-400">Anggota Penguji</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="border border-gray-200 rounded-xl p-3.5 bg-gray-50/50">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 block">Dosen Penguji II</span>
                                    <div class="flex items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($seminar->penguji2->name ?? 'Penguji 2') }}&background=f3f4f6&color=6b7280" class="w-10 h-10 rounded-full mr-3 border shadow-sm">
                                        <div>
                                            <p class="font-bold text-gray-800 text-xs">{{ $seminar->penguji2->name ?? 'Belum Ditentukan' }}</p>
                                            <p class="text-[10px] text-gray-400">Anggota Penguji</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="border border-dashed border-gray-300 rounded-xl p-3.5 flex flex-col items-center justify-center text-center bg-teal-50/30">
                                    <i class="fas fa-award text-xl text-vokasi-primary mb-1"></i>
                                    <p class="text-xs font-bold text-gray-700">Nilai Akhir Seminar</p>
                                    @if($seminar && $seminar->nilai_akhir)
                                        <p class="text-xl font-extrabold text-vokasi-primary mt-0.5">{{ $seminar->nilai_akhir }}</p>
                                    @else
                                        <p class="text-[10px] text-gray-400 mt-0.5">Muncul setelah sidang selesai</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            @endif
        @endif

    </div>

<!-- ========================================================= -->
    <!-- MODAL POPUP ADMIN: SET JADWAL, DEWAN PENGUJI & NILAI     -->
    <!-- ========================================================= -->
    @if(isset($user) && $user->hasAnyRole(['admin', 'superadmin', 'admin_prodi']))
    <div x-show="openSetModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.away="openSetModal = false" class="bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-xl overflow-hidden" x-if="activeMhs">
            
            <div class="bg-vokasi-primary px-6 py-4 text-white flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-base"><i class="fas fa-calendar-alt mr-2"></i> Atur Ujian Seminar Hasil</h3>
                    <p class="text-xs opacity-90" x-text="activeMhs?.name"></p>
                </div>
                <button type="button" @click="openSetModal = false" class="text-white/80 hover:text-white"><i class="fas fa-times text-lg"></i></button>
            </div>

            <form :action="activeActionUrl" method="POST" class="p-6 space-y-4 text-xs">
                @csrf

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Waktu Ujian <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="waktu_seminar" :value="activeSeminar?.waktu_seminar ? activeSeminar.waktu_seminar.replace(' ', 'T').substring(0,16) : ''" required class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Lokasi Ruangan / Platform <span class="text-red-500">*</span></label>
                        <input type="text" name="lokasi_ruangan" :value="activeSeminar?.lokasi_ruangan ?? 'Ruang Sidang Vokasi (Lantai 2)'" required class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Dosen Pembimbing (Ketua Sidang)</label>
                    <select name="pembimbing_id" :value="activeSeminar?.pembimbing_id" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none">
                        <option value="">-- Pilih Dosen Pembimbing --</option>
                        @foreach($dosens as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Dosen Penguji I</label>
                        <select name="penguji_1_id" :value="activeSeminar?.penguji_1_id" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none">
                            <option value="">-- Pilih Penguji I --</option>
                            @foreach($dosens as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Dosen Penguji II</label>
                        <select name="penguji_2_id" :value="activeSeminar?.penguji_2_id" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none">
                            <option value="">-- Pilih Penguji II --</option>
                            @foreach($dosens as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-100">
                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Status Seminar <span class="text-red-500">*</span></label>
                        <select name="status_seminar" :value="activeSeminar?.status_seminar ?? 'dijadwalkan'" required class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none font-bold">
                            <option value="mengajukan">Mengajukan</option>
                            <option value="dijadwalkan">Dijadwalkan</option>
                            <option value="selesai">Selesai</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Nilai Akhir (0 - 100)</label>
                        <input type="number" step="0.01" min="0" max="100" name="nilai_akhir" :value="activeSeminar?.nilai_akhir" placeholder="Contoh: 88.50" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none font-bold text-vokasi-primary">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                    <button type="button" @click="openSetModal = false" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-medium">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-vokasi-primary hover:bg-vokasi-dark text-white rounded-xl font-bold shadow-sm">Simpan Plotting</button>
                </div>
            </form>

        </div>
    </div>
    @endif
    <!-- FOOTER -->
    <footer class="mt-auto py-4 text-center text-sm text-gray-500 border-t border-gray-200 bg-gray-50">
        Created with <i class="fas fa-heart text-red-500 mx-1"></i> from <span class="font-semibold text-gray-700">lagingodingdotcom</span> collaborate with <span class="font-semibold text-gray-700">Savages</span>
    </footer>

</main>
@endsection