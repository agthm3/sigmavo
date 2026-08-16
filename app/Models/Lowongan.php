<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lowongan extends Model
{
    use HasFactory;

    protected $fillable = [
        'perusahaan_id',
        'prodi_id',
        'judul_posisi',
        'mode_kerja',
        'kuota',
        'kuota_terisi',
        'batas_pendaftaran',
        'durasi',
        'deskripsi',
        'status',
    ];

    protected $casts = [
        'batas_pendaftaran' => 'date',
    ];

    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    public function pendaftarans()
    {
        return $this->hasMany(Pendaftaran::class, 'lowongan_id');
    }
}