<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pendaftaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lowongan_id',
        'dosen_id',
        'status_seleksi',
        'file_cv',
        'file_transkrip',
        'catatan_seleksi',
    ];

    // Mahasiswa Pelamar
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Lowongan
    public function lowongan(): BelongsTo
    {
        return $this->belongsTo(Lowongan::class, 'lowongan_id');
    }

    // Dosen Pendamping
    public function dosen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }
}