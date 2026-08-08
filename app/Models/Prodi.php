<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prodi extends Model
{
    protected $fillable = ['kode_prodi', 'nama_prodi', 'jenjang'];

    public function mahasiswas(): HasMany
    {
        return $this->hasMany(MahasiswaProfile::class);
    }

    public function dosens(): HasMany
    {
        return $this->hasMany(DosenProfile::class);
    }
    
    public function cpmks(): HasMany
    {
        return $this->hasMany(Cpmk::class, 'prodi_id');
    }

    public function mahasiswaProfiles(): HasMany
    {
        return $this->hasMany(MahasiswaProfile::class, 'prodi_id');
    }

    /**
     * Relasi ke Profil Dosen
     */
    public function dosenProfiles(): HasMany
    {
        return $this->hasMany(DosenProfile::class, 'prodi_id');
    }
}