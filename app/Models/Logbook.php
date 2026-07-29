<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Logbook extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pendaftaran_id',
        'tanggal',
        'uraian_kegiatan',
        'mata_kuliah',
        'foto_dokumentasi',
        'status_asistensi',
        'catatan_dosen',
        'verifikator_id',
        'waktu_verifikasi',
    ];

    protected $casts = [
        'tanggal'          => 'date',
        'mata_kuliah'      => 'array',
        'waktu_verifikasi' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }
}