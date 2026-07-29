<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembekalanMateri extends Model
{
    use HasFactory;

    protected $fillable = [
        'pembekalan_id',
        'judul_materi',
        'tipe_file',
        'ukuran_file',
        'file_path',
    ];

    /**
     * Relasi balik ke Pembekalan
     */
    public function pembekalan(): BelongsTo
    {
        return $this->belongsTo(Pembekalan::class);
    }
}