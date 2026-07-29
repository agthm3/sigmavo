<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'avatar', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // RELASI KE PROFIL
    public function mahasiswaProfile(): HasOne
    {
        return $this->hasOne(MahasiswaProfile::class);
    }

    public function dosenProfile(): HasOne
    {
        return $this->hasOne(DosenProfile::class);
    }

    public function mitraProfile(): HasOne
    {
        return $this->hasOne(MitraProfile::class);
    }

    // Tambahkan di dalam kelas User
    public function adminProdiProfile(): HasOne
    {
        return $this->hasOne(AdminProdiProfile::class);
    }

    /**
     * Mendapatkan ID Prodi dari User (baik Mahasiswa, Dosen, maupun Admin Prodi)
     */
    public function getProdiIdAttribute()
    {
        if ($this->hasRole('admin_prodi')) {
            return $this->adminProdiProfile?->prodi_id;
        }
        if ($this->hasRole('mahasiswa')) {
            return $this->mahasiswaProfile?->prodi_id;  
        }
        if ($this->hasRole('dosen')) {
            return $this->dosenProfile?->prodi_id;
        }
        return null; // Superadmin / Admin Fakultas
    }

    public function seminars()
    {
        return $this->hasMany(Seminar::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }
}