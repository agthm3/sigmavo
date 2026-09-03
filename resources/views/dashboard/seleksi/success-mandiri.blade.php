@extends('layouts.dashboard')

@section('content')
@php
    $data = session('spvData');
@endphp

<div class="flex-1 flex flex-col h-screen overflow-y-auto bg-gray-50 p-4 lg:p-8" x-data="{ copied: false }">
    <div class="max-w-2xl mx-auto w-full mt-10">
        
        <!-- Main Card -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            
            <!-- Header Status -->
            <div class="bg-gradient-to-r from-emerald-500 to-teal-500 p-8 text-center text-white relative">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <i class="fas fa-check text-4xl text-emerald-500"></i>
                </div>
                <h2 class="text-2xl font-bold mb-2">Pendaftaran Mandiri Disetujui!</h2>
                <p class="text-emerald-50 font-medium">Mahasiswa <span class="font-bold text-white">{{ $data['mahasiswa'] }}</span> resmi ditempatkan di <span class="font-bold text-white">{{ $data['instansi'] }}</span>.</p>
            </div>

            <!-- Body Kredensial SPV -->
            <div class="p-8">
                
                @if($data['status'] == 'created')
                    <!-- Kondisi: Akun Baru Dibuat -->
                    <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-xl mb-6">
                        <h4 class="font-bold text-amber-900 text-sm flex items-center"><i class="fas fa-exclamation-triangle mr-2"></i> Tindakan Diperlukan (Tugas Admin)</h4>
                        <p class="text-xs text-amber-800 mt-1 leading-relaxed">Sistem telah otomatis membuatkan akun akses untuk Supervisor Lapangan. <strong>Wajib salin (copy) informasi di bawah ini dan berikan kepada mahasiswa</strong> agar diteruskan ke Supervisor di instansinya.</p>
                    </div>

                    <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 relative">
                        <!-- Tombol Copy -->
                        <button @click="
                                navigator.clipboard.writeText(`*INFORMASI AKUN SUPERVISOR MAGANG*\n\nHalo Bapak/Ibu Supervisor ({{ $data['name'] }}),\n\nBerikut adalah akses login untuk memantau logbook magang mahasiswa atas nama *{{ $data['mahasiswa'] }}* di portal SIGMAVO:\n\nEmail: {{ $data['email'] }}\nPassword: {{ $data['password'] }}\n\nSilakan login melalui web SIGMAVO.`);
                                copied = true;
                                setTimeout(() => copied = false, 2000);
                            " 
                            class="absolute top-4 right-4 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-sm flex items-center gap-2">
                            <span x-text="copied ? 'Tersalin!' : 'Salin Pesan WA'"></span>
                            <i :class="copied ? 'fas fa-check text-emerald-500' : 'fas fa-copy'"></i>
                        </button>

                        <div class="space-y-4 text-sm mt-2">
                            <div>
                                <p class="text-gray-500 text-xs font-bold uppercase mb-1">Nama Supervisor</p>
                                <p class="font-semibold text-gray-900">{{ $data['name'] }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs font-bold uppercase mb-1">Email Login / Username</p>
                                <div class="bg-white border border-gray-200 p-2.5 rounded-lg font-mono text-vokasi-primary font-bold inline-block">{{ $data['email'] }}</div>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs font-bold uppercase mb-1">Password Default</p>
                                <div class="bg-white border border-gray-200 p-2.5 rounded-lg font-mono text-red-600 font-bold inline-block">{{ $data['password'] }}</div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Kondisi: Akun Sudah Pernah Ada -->
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-xl mb-6">
                        <h4 class="font-bold text-blue-900 text-sm flex items-center"><i class="fas fa-info-circle mr-2"></i> Akun SPV Sudah Terdaftar</h4>
                        <p class="text-xs text-blue-800 mt-1 leading-relaxed">Sistem mendeteksi bahwa Supervisor <strong>{{ $data['name'] }} ({{ $data['email'] }})</strong> sudah memiliki akun di sistem. Mahasiswa tersebut telah otomatis dihubungkan ke akun beliau. Tidak ada password baru yang dibuat.</p>
                    </div>
                @endif

                <!-- Navigasi -->
                <div class="mt-8 text-center">
                    <a href="{{ route('dashboard-daftar-lowongan-seleksi') }}" class="inline-flex items-center justify-center bg-gray-100 hover:bg-gray-200 border border-gray-300 text-gray-700 px-6 py-3 rounded-xl font-bold text-sm transition-colors shadow-sm w-full md:w-auto">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Seleksi
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection