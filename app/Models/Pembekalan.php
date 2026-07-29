<?php

namespace App\Models;

use App\Models\Prodi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pembekalan extends Model
{
    use HasFactory;

    protected $fillable = [
        'prodi_id',
        'judul',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi',
        'link_zoom',
        'pemateri',
        'topik_utama',
        'status',
    ];
    protected $casts = [
        'waktu_mulai'   => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    public function presensis(): HasMany
    {
        return $this->hasMany(PembekalanPresensi::class);
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function materis(): HasMany
    {
        return $this->hasMany(PembekalanMateri::class);
    }
}