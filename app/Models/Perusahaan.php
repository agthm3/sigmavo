<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perusahaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_perusahaan',
        'sektor_industri',
        'status_kerjasama',
        'website',
        'email_hrd',
        'alamat',
        'latitude',
        'longitude',
    ];

    /**
     * Relasi ke pendaftaran magang mahasiswa di perusahaan ini
     */
    public function pendaftarans(): HasMany
    {
        return $this->hasMany(Pendaftaran::class, 'perusahaan_id');
    }

    // Helper singkatan logo (Contoh: PT. SmartPlay Inovasi -> SP)
    public function getInisialAttribute(): string
    {
        $words = explode(' ', str_replace(['PT.', 'CV.', 'BRIDA'], '', $this->nama_perusahaan));
        $words = array_filter($words);
        $inisial = '';
        foreach (array_slice($words, 0, 2) as $w) {
            $inisial .= strtoupper(substr(trim($w), 0, 1));
        }
        return $inisial ?: 'PT';
    }
}