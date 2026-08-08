<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\Penilaian;
use App\Models\PenilaianDetail;
use App\Models\Prodi;
use App\Models\RubrikPenilaian;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class PenilaianListingMahasiswaController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        // Base Query Pendaftaran Magang
        $query = Pendaftaran::with([
            'user.mahasiswaProfile.prodi', 
            'lowongan.perusahaan', 
            'penilaians.details',
            'user.logbooks'
        ])->where('status_seleksi', 'diterima');

        // 1. Scope Hak Akses Role
        if ($currentUser->hasRole('admin_prodi')) {
            $query->whereHas('user.mahasiswaProfile', fn($q) => $q->where('prodi_id', $currentUser->prodi_id));
        } elseif ($currentUser->hasRole('dosen')) {
            $query->where('dosen_id', $currentUser->id);
        }

        // 2. Filter Pencarian Teks (Nama, NIM, Perusahaan)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', fn($sq) => $sq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('user.mahasiswaProfile', fn($sq) => $sq->where('nim', 'like', "%{$search}%"))
                  ->orWhereHas('lowongan.perusahaan', fn($sq) => $sq->where('nama_perusahaan', 'like', "%{$search}%"))
                  ->orWhere('nama_instansi_mandiri', 'like', "%{$search}%");
            });
        }

        // 3. Filter Program Studi
        if ($request->filled('prodi_id') && $request->prodi_id !== 'semua') {
            $query->whereHas('user.mahasiswaProfile', fn($q) => $q->where('prodi_id', $request->prodi_id));
        }

        // 4. Filter Status Penilaian Dosen
        if ($request->filled('status_nilai') && $request->status_nilai !== 'semua') {
            if ($request->status_nilai === 'lengkap') {
                $query->whereHas('penilaians', fn($q) => $q->where('tipe_penilai', 'dosen'));
            } elseif ($request->status_nilai === 'belum') {
                $query->whereDoesntHave('penilaians', fn($q) => $q->where('tipe_penilai', 'dosen'));
            }
        }

        // Ambil Data Terpaginasi
        $pendaftarans = $query->paginate(15)->withQueryString();

        // 5. Filter Syarat Jam Magang (Post-Paginator / Collection Filter)
        $minJamMagang = (int) Setting::getByKey('min_jam_magang', '900');

        if ($request->filled('status_jam') && $request->status_jam !== 'semua') {
            $filteredItems = $pendaftarans->getCollection()->filter(function($pendaftaran) use ($minJamMagang, $request) {
                // Hitung total logbook yang terdata (jika ada kolom durasi gunakan sum, jika tidak hitung hari * 8 jam)
                $totalJam = $pendaftaran->user->logbooks->sum('durasi') ?: ($pendaftaran->user->logbooks->count() * 8);

                if ($request->status_jam === 'selesai') {
                    return $totalJam >= $minJamMagang;
                } elseif ($request->status_jam === 'belum') {
                    return $totalJam < $minJamMagang;
                }
                return true;
            });
            $pendaftarans->setCollection($filteredItems);
        }

        // Ambil Data Master Pendukung
        $rubriks = RubrikPenilaian::where('is_active', true)->orderBy('no_urut')->get();
        $prodis = Prodi::all();

        return view('dashboard.listing-mahasiswa.index', compact('pendaftarans', 'rubriks', 'prodis', 'currentUser', 'minJamMagang'));
    }

    public function storePenilaian(Request $request, $pendaftaran_id)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        
        if (!$currentUser->hasAnyRole(['dosen', 'admin', 'superadmin', 'admin_prodi'])) {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya Dosen atau Admin yang berhak memberikan nilai.');
        }

        $request->validate([
            'nilai' => 'required|array',
            'nilai.*' => 'required|numeric|min:0|max:100',
        ]);

        $pendaftaran = Pendaftaran::findOrFail($pendaftaran_id);
        $rubriks = RubrikPenilaian::where('is_active', true)->get();

        $totalNilaiAkhir = 0;

        $penilaian = Penilaian::firstOrCreate(
            ['pendaftaran_id' => $pendaftaran->id, 'tipe_penilai' => 'dosen'],
            ['penilai_id' => $currentUser->id, 'nilai_akhir' => 0]
        );

        foreach ($rubriks as $rubrik) {
            $nilaiMentah = $request->nilai[$rubrik->id] ?? 0;
            $nilaiTertimbang = $nilaiMentah * ($rubrik->bobot / 100);
            $totalNilaiAkhir += $nilaiTertimbang;

            PenilaianDetail::updateOrCreate(
                ['penilaian_id' => $penilaian->id, 'rubrik_id' => $rubrik->id],
                ['nilai_mentah' => $nilaiMentah]
            );
        }

        $penilaian->update([
            'nilai_akhir' => $totalNilaiAkhir,
            'penilai_id'  => $currentUser->id,
        ]);

        return redirect()->back()->with('success', "Berhasil menyimpan penilaian akhir untuk {$pendaftaran->user->name}.");
    }

    /**
     * Export PDF dengan Filter Komprehensif
     */
    public function exportPdf(Request $request)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        // 1. Query Pendaftaran Magang
        $query = Pendaftaran::with([
            'user.mahasiswaProfile.prodi', 
            'lowongan.perusahaan', 
            'penilaians.details.rubrik',
            'user.logbooks'
        ])->where('status_seleksi', 'diterima');

        // Filter Scope Role
        if ($currentUser->hasRole('admin_prodi')) {
            $query->whereHas('user.mahasiswaProfile', fn($q) => $q->where('prodi_id', $currentUser->prodi_id));
        } elseif ($currentUser->hasRole('dosen')) {
            $query->where('dosen_id', $currentUser->id);
        }

        // Filter Search Teks
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', fn($sq) => $sq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('user.mahasiswaProfile', fn($sq) => $sq->where('nim', 'like', "%{$search}%"))
                  ->orWhereHas('lowongan.perusahaan', fn($sq) => $sq->where('nama_perusahaan', 'like', "%{$search}%"))
                  ->orWhere('nama_instansi_mandiri', 'like', "%{$search}%");
            });
        }

        // Filter Program Studi
        if ($request->filled('prodi_id') && $request->prodi_id !== 'semua') {
            $query->whereHas('user.mahasiswaProfile', fn($q) => $q->where('prodi_id', $request->prodi_id));
        }

        // Filter Status Penilaian Dosen
        if ($request->filled('status_nilai') && $request->status_nilai !== 'semua') {
            if ($request->status_nilai === 'lengkap') {
                $query->whereHas('penilaians', fn($q) => $q->where('tipe_penilai', 'dosen'));
            } elseif ($request->status_nilai === 'belum') {
                $query->whereDoesntHave('penilaians', fn($q) => $q->where('tipe_penilai', 'dosen'));
            }
        }

        $pendaftarans = $query->get();

        // Filter Syarat Jam Magang
        $minJamMagang = (int) Setting::getByKey('min_jam_magang', '900');

        if ($request->filled('status_jam') && $request->status_jam !== 'semua') {
            $pendaftarans = $pendaftarans->filter(function($pendaftaran) use ($minJamMagang, $request) {
                $totalJam = $pendaftaran->user->logbooks->sum('durasi') ?: ($pendaftaran->user->logbooks->count() * 8);

                if ($request->status_jam === 'selesai') {
                    return $totalJam >= $minJamMagang;
                } elseif ($request->status_jam === 'belum') {
                    return $totalJam < $minJamMagang;
                }
                return true;
            });
        }

        if ($pendaftarans->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data mahasiswa yang memenuhi kriteria filter untuk di-export.');
        }

        // 2. Pre-Processing Base64 Foto Logbook
        foreach ($pendaftarans as $pendaftaran) {
            if ($pendaftaran->user && $pendaftaran->user->logbooks) {
                foreach ($pendaftaran->user->logbooks as $logbook) {
                    $logbook->foto_base64 = null;

                    if (!empty($logbook->foto_dokumentasi)) {
                        if (Storage::disk('public')->exists($logbook->foto_dokumentasi)) {
                            $fileContents = Storage::disk('public')->get($logbook->foto_dokumentasi);
                            $mimeType = Storage::disk('public')->mimeType($logbook->foto_dokumentasi);
                            $logbook->foto_base64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileContents);
                        } else {
                            $localPath = storage_path('app/public/' . ltrim($logbook->foto_dokumentasi, '/'));
                            if (file_exists($localPath)) {
                                $fileContents = file_get_contents($localPath);
                                $mimeType = mime_content_type($localPath);
                                $logbook->foto_base64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileContents);
                            }
                        }
                    }
                }
            }
        }

        // 3. Render PDF
        $pdf = Pdf::loadView('dashboard.listing-mahasiswa.pdf-transkrip', compact('pendaftarans', 'minJamMagang'))
                  ->setPaper('a4', 'portrait')
                  ->setOptions([
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled'      => false,
                      'chroot'               => [public_path(), storage_path(), base_path()],
                  ]);

        return $pdf->stream('Laporan_Evaluasi_dan_Logbook_Magang.pdf');
    }
}