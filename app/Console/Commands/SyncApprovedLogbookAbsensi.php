<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Logbook;
use App\Models\Absensi;
use App\Models\Pendaftaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SyncApprovedLogbookAbsensi extends Command
{
    protected $signature = 'logbook:sync-jam {--fix : Otomatis perbaiki data absensi yang macet}';
    protected $description = 'Deteksi dan sinkronkan jam absensi untuk semua logbook yang telah disetujui SPV & Dosen';

    public function handle()
    {
        $this->info("Memulai pemindaian data absensi dan logbook seluruh mahasiswa...");

        // Ambil semua logbook yang berstatus approved penuh (baik master approved maupun dual approved)
        $approvedLogs = Logbook::where(function ($q) {
            $q->where('status_asistensi', 'approved')
              ->orWhere(function ($sub) {
                  $sub->where('status_spv', 'approved')
                      ->where('status_dosen', 'approved');
              });
        })->with('user')->get();

        $anomali = [];

        foreach ($approvedLogs as $log) {
            $tgl = Carbon::parse($log->tanggal)->format('Y-m-d');
            $absensi = Absensi::where('user_id', $log->user_id)
                ->whereDate('tanggal', $tgl)
                ->first();

            // Jika absensi belum ada ATAU jamnya masih kurang dari 8 jam
            if (!$absensi || $absensi->jam_diperoleh < 8) {
                $anomali[] = [
                    'user_id'    => $log->user_id,
                    'nama'       => $log->user->name ?? 'User #' . $log->user_id,
                    'tanggal'    => $tgl,
                    'logbook_id' => $log->id,
                    'kondisi'    => !$absensi ? 'Tidak ada record absensi' : (empty($absensi->waktu_pulang) ? 'Lupa Absen Pulang (NULL)' : 'Jam masih 0 (belum sinkron)'),
                ];
            }
        }

        if (empty($anomali)) {
            $this->info("✅ Semua data sinkron! Tidak ditemukan mahasiswa dengan jam yang tertahan.");
            return 0;
        }

        $this->warn("Ditemukan " . count($anomali) . " kasus logbook approved yang jam absensinya belum 8 jam:");
        $this->table(['User ID', 'Nama Mahasiswa', 'Tanggal', 'Logbook ID', 'Kondisi'], $anomali);

        if ($this->option('fix')) {
            $this->info("Menjalankan perbaikan massal...");

            DB::transaction(function () use ($anomali) {
                foreach ($anomali as $item) {
                    $pendaftaran = Pendaftaran::where('user_id', $item['user_id'])->latest()->first();

                    Absensi::updateOrCreate(
                        ['user_id' => $item['user_id'], 'tanggal' => $item['tanggal']],
                        [
                            'pendaftaran_id'    => $pendaftaran?->id,
                            'tipe_kehadiran'    => 'hadir',
                            'waktu_masuk'       => DB::raw("COALESCE(waktu_masuk, '08:00:00')"),
                            'waktu_pulang'      => DB::raw("COALESCE(waktu_pulang, '17:00:00')"),
                            'status_verifikasi' => 'approved',
                            'jam_diperoleh'     => 8,
                        ]
                    );

                    Logbook::where('id', $item['logbook_id'])->update([
                        'status_asistensi' => 'approved',
                    ]);
                }
            });

            $this->info("✅ Sukses! Seluruh data anomali telah diperbaiki dan jam absensi sudah sinkron 8 jam.");
        } else {
            $this->comment("Jalankan kembali dengan opsi '--fix' untuk otomatis memperbaiki semua data di atas: php artisan logbook:sync-jam --fix");
        }

        return 0;
    }
}