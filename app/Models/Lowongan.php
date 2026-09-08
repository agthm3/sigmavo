<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lowongan extends Model
{
    use HasFactory;

    protected $fillable = [
        'perusahaan_id',
        'spv_id', // Pastikan kolom ini terdaftar
        'prodi_id',
        'judul_posisi',
        'mode_kerja',
        'kuota',
        'batas_pendaftaran',
        'durasi',
        'deskripsi',
        'status',
    ];

    protected $casts = [
        'batas_pendaftaran' => 'date',
    ];

    /**
     * Relasi ke Supervisor (User akun SPV)
     */
    public function spv(): BelongsTo
    {
        return $this->belongsTo(User::class, 'spv_id');
    }

    /**
     * Relasi ke Perusahaan Mitra
     */
    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class, 'perusahaan_id');
    }

    /**
     * Relasi ke Program Studi (Opsional / Nullable jika semua prodi)
     */
    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }

    /**
     * Relasi ke Pendaftaran Mahasiswa
     */
    public function pendaftarans(): HasMany
    {
        return $this->hasMany(Pendaftaran::class, 'lowongan_id');
    }
}