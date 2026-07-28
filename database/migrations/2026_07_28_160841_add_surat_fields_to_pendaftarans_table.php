<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->enum('jalur_magang', ['reguler', 'mandiri'])->default('reguler')->after('lowongan_id');
            $table->string('nama_instansi_mandiri')->nullable()->after('jalur_magang');
            $table->string('divisi_mandiri')->nullable()->after('nama_instansi_mandiri');
            
            // Surat Pengantar Fields
            $table->string('nomor_surat')->nullable()->after('status_seleksi');
            $table->string('perihal_surat')->nullable()->after('nomor_surat');
            $table->date('tgl_mulai_magang')->nullable()->after('perihal_surat');
            $table->date('tgl_selesai_magang')->nullable()->after('tgl_mulai_magang');
            $table->enum('status_surat', ['menunggu', 'terbit', 'ditolak'])->default('menunggu')->after('tgl_selesai_magang');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->dropColumn([
                'jalur_magang', 'nama_instansi_mandiri', 'divisi_mandiri',
                'nomor_surat', 'perihal_surat', 'tgl_mulai_magang', 'tgl_selesai_magang', 'status_surat'
            ]);
        });
    }
};