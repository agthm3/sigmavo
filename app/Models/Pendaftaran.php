<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pendaftaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lowongan_id',
        'dosen_id',
        'jalur_magang',
        'nama_instansi_mandiri',
        'divisi_mandiri',
        'status_seleksi',
        'nomor_surat',
        'perihal_surat',
        'tgl_mulai_magang',
        'tgl_selesai_magang',
        'status_surat',
        'file_cv',
        'file_transkrip',
        'catatan_seleksi',
        'surat_balasan',
        'allow_logbook_susulan',
    ];

    protected $casts = [
        'tgl_mulai_magang' => 'date',
        'tgl_selesai_magang' => 'date',
        'allow_logbook_susulan' => 'boolean',
    ];

    /**
     * Relasi ke Lowongan Magang
     */
    public function lowongan(): BelongsTo
    {
        return $this->belongsTo(Lowongan::class, 'lowongan_id');
    }

    /**
     * Relasi ke User (Mahasiswa)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Dosen Pembimbing
     */
    public function dosen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function penilaians(): HasMany
    {
        return $this->hasMany(Penilaian::class, 'pendaftaran_id');
    }
    public function perusahaan()
    {
        return $this->hasOneThrough(
            Perusahaan::class,
            Lowongan::class,
            'id',            // Foreign key di tabel lowongans (lowongans.id)
            'id',            // Foreign key di tabel perusahaans (perusahaans.id)
            'lowongan_id',   // Local key di tabel pendaftarans (pendaftarans.lowongan_id)
            'perusahaan_id'  // Local key di tabel lowongans (lowongans.perusahaan_id)
        );
    }

    public function getNamaPerusahaanAttribute()
    {
        if ($this->jalur_magang === 'mandiri' || $this->nama_instansi_mandiri) {
            return $this->nama_instansi_mandiri;
        }

        return $this->lowongan?->perusahaan?->nama_perusahaan ?? '-';
    }
}