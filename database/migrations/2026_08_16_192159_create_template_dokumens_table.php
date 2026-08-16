<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_dokumens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prodi_id')->nullable()->constrained('prodis')->nullOnDelete();
            $table->string('judul_dokumen');
            $table->text('deskripsi')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_extension', 20)->default('docx');
            $table->string('file_size', 50)->nullable();
            $table->string('kategori', 50)->default('Wajib');
            $table->string('versi', 20)->default('v1.0');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_dokumens');
    }
};