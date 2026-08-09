@extends('layouts.dashboard')

@section('content')
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 lg:p-8 custom-scrollbar">
    
    <div class="max-w-5xl mx-auto">
        
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Pratinjau Data Import Massal</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Sistem mendeteksi <strong>{{ count($parsedData) }} baris data user</strong> dari file Excel Anda. Pilih Program Studi tujuan lalu periksa data sebelum disimpan.
                </p>
            </div>
        </div>

        <form id="formImportStore" action="{{ route('dashboard-manajemen-aktivasi-import-store') }}" method="POST">
            @csrf
            
            <input type="hidden" name="users_data" value="{{ json_encode($parsedData) }}">

            <div class="mb-6 p-5 bg-white rounded-2xl border-2 border-vokasi-primary/30 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-vokasi-primary/10 text-vokasi-primary flex items-center justify-center shrink-0 font-bold text-lg mt-0.5">
                        <i class="fas fa-university"></i>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider">
                            Program Studi Tujuan <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Seluruh akun di bawah ini akan dihubungkan ke Master Program Studi ini.
                        </p>
                    </div>
                </div>

                <div class="w-full md:w-80 shrink-0">
                    @if($currentUser->hasAnyRole(['admin_prodi', 'admin-prodi']) && $currentUser->adminProdiProfile?->prodi_id)
                        <input type="hidden" name="prodi_id" value="{{ $currentUser->adminProdiProfile->prodi_id }}">
                        <div class="px-4 py-2.5 bg-vokasi-primary/10 border border-vokasi-primary/30 rounded-xl font-bold text-vokasi-primary text-sm flex items-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ $currentUser->adminProdiProfile->prodi->nama_prodi }}</span>
                        </div>
                    @else
                        <!-- INI PENTING: NAME HARUS "prodi_id" -->
                        <select name="prodi_id" required class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-vokasi-primary focus:bg-white font-bold text-gray-800">
                            <option value="" disabled selected>-- Pilih Master Program Studi --</option>
                            @foreach($prodis as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>

            <!-- Tabel Preview -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-6">
                <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-users text-vokasi-primary"></i> Daftar Akun Siap Diimport
                    </span>
                    <span class="px-3 py-1 bg-vokasi-primary/10 text-vokasi-primary text-xs font-bold rounded-full">
                        {{ count($parsedData) }} Pengguna
                    </span>
                </div>

                <div class="overflow-x-auto max-h-[450px] custom-scrollbar">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead class="sticky top-0 bg-gray-100 z-10">
                            <tr class="text-gray-600 uppercase text-[10px] font-bold tracking-wider border-b border-gray-200">
                                <th class="p-3.5 pl-6 w-12 text-center">No</th>
                                <th class="p-3.5">Nama Lengkap</th>
                                <th class="p-3.5">Email</th>
                                <th class="p-3.5">Password</th>
                                <th class="p-3.5 text-center">Role</th>
                                <th class="p-3.5">NIM / NIP</th>
                                <th class="p-3.5 pr-6 text-center">Angkatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @foreach($parsedData as $index => $row)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="p-3.5 pl-6 font-medium text-gray-500 text-center">{{ $index + 1 }}</td>
                                <td class="p-3.5 font-bold text-gray-800">{{ $row['nama'] }}</td>
                                <td class="p-3.5 text-gray-600">{{ $row['email'] }}</td>
                                <td class="p-3.5 font-mono text-gray-400">{{ $row['password'] }}</td>
                                <td class="p-3.5 text-center">
                                    <span class="px-2.5 py-1 rounded-md uppercase font-bold text-[10px] tracking-wider bg-gray-100 text-gray-700">
                                        {{ $row['role'] }}
                                    </span>
                                </td>
                                <td class="p-3.5 font-mono text-gray-800">{{ $row['nim_nip'] ?: '-' }}</td>
                                <td class="p-3.5 pr-6 text-center text-gray-600">{{ $row['angkatan'] ?: '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex flex-col sm:flex-row justify-end items-center gap-3">
                <a href="{{ route('dashboard-manajemen-aktivasi-user') }}" class="w-full sm:w-auto text-center px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold text-xs rounded-xl shadow-sm hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Batal & Kembali
                </a>
                <button type="submit" id="btnConfirmSubmit" class="w-full sm:w-auto px-6 py-2.5 bg-vokasi-primary hover:bg-vokasi-dark text-white font-bold text-xs rounded-xl shadow-sm flex items-center justify-center gap-2 transition-colors">
                    <i class="fas fa-check-circle"></i> Konfirmasi & Simpan {{ count($parsedData) }} User
                </button>
            </div>
        </form>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('formImportStore');
        const btnSubmit = document.getElementById('btnConfirmSubmit');

        if (form) {
            form.addEventListener('submit', function (e) {
                const prodiSelect = form.querySelector('select[name="prodi_id"]');
                if (prodiSelect && !prodiSelect.value) {
                    return;
                }

                Swal.fire({
                    title: 'Memproses Import Data...',
                    html: 'Sistem sedang mendaftarkan akun dan membuat profil pengguna secara massal. Harap tunggu sejenak.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                if (btnSubmit) {
                    btnSubmit.disabled = true;
                    btnSubmit.classList.add('opacity-75', 'cursor-not-allowed');
                }
            });
        }
    });
</script>
@endsection