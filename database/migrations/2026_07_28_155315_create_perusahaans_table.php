<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perusahaans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan');
            $table->string('sektor_industri');
            $table->string('status_kerjasama')->default('Mitra Reguler'); // MoU Resmi, Mitra Reguler, Mandiri Partner
            $table->string('website')->nullable();
            $table->string('email_hrd');
            $table->text('alamat');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perusahaans');
    }
};