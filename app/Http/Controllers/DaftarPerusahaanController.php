<?php

namespace App\Http\Controllers;

use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DaftarPerusahaanController extends Controller
{
    public function index(Request $request)
    {
        $query = Perusahaan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_perusahaan', 'like', "%{$search}%")
                  ->orWhere('sektor_industri', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if ($request->filled('sektor') && $request->sektor !== 'semua') {
            $query->where('sektor_industri', $request->sektor);
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status_kerjasama', $request->status);
        }

        $perusahaans = $query->latest()->paginate(10)->withQueryString();
        
        $allPerusahaan = Perusahaan::orderBy('nama_perusahaan', 'asc')->get();
        $totalMitra = Perusahaan::count();
        $totalMoU = Perusahaan::where('status_kerjasama', 'MoU Resmi')->count();

        return view('dashboard.daftar-perusahaan.index', compact('perusahaans', 'allPerusahaan', 'totalMitra', 'totalMoU'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_perusahaan'  => 'required|string|max:255',
            'sektor_industri'  => 'required|string',
            'status_kerjasama' => 'required|string',
            'website'          => 'nullable|url',
            'email_hrd'        => 'required|email|max:255',
            'alamat'           => 'required|string',
            'latitude'         => 'nullable|string',
            'longitude'        => 'nullable|string',
        ]);

        Perusahaan::create($request->all());
        return redirect()->back()->with('success', 'Perusahaan mitra baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $perusahaan = Perusahaan::findOrFail($id);
        $request->validate([
            'nama_perusahaan'  => 'required|string|max:255',
            'sektor_industri'  => 'required|string',
            'status_kerjasama' => 'required|string',
            'website'          => 'nullable|url',
            'email_hrd'        => 'required|email|max:255',
            'alamat'           => 'required|string',
            'latitude'         => 'nullable|string',
            'longitude'        => 'nullable|string',
        ]);

        $perusahaan->update($request->all());
        return redirect()->back()->with('success', "Data '{$perusahaan->nama_perusahaan}' berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $perusahaan = Perusahaan::findOrFail($id);
        $perusahaan->delete();
        return redirect()->back()->with('success', 'Perusahaan mitra berhasil dihapus.');
    }

    /**
     * AJAX Preview Detail Data sebelum Di-Merge (UPDATED DENGAN DATA DETAIL)
     */
    public function previewMerge(Request $request)
    {
        $targetId = $request->target_id ?? null;
        $sourceIds = $request->source_ids ?? [];

        $response = [
            'target_spv' => [],
            'sources' => [
                'spvs' => [],
                'lowongans' => [],
                'pendaftars' => []
            ]
        ];

        // 1. Ambil data SPV dari Perusahaan Master (Target)
        if ($targetId) {
            $response['target_spv'] = DB::table('spv_profiles')
                ->join('users', 'spv_profiles.user_id', '=', 'users.id')
                ->where('spv_profiles.perusahaan_id', $targetId)
                ->select('users.name', 'users.email')
                ->get();
        }

        // 2. Ambil data Relasi dari Perusahaan Duplikat (Sources)
        if (!empty($sourceIds)) {
            $response['sources']['spvs'] = DB::table('spv_profiles')
                ->join('users', 'spv_profiles.user_id', '=', 'users.id')
                ->join('perusahaans', 'spv_profiles.perusahaan_id', '=', 'perusahaans.id')
                ->whereIn('spv_profiles.perusahaan_id', $sourceIds)
                ->select('users.name', 'users.email', 'perusahaans.nama_perusahaan as asal')
                ->get();

            $response['sources']['lowongans'] = DB::table('lowongans')
                ->join('perusahaans', 'lowongans.perusahaan_id', '=', 'perusahaans.id')
                ->whereIn('lowongans.perusahaan_id', $sourceIds)
                ->select('lowongans.judul_posisi', 'perusahaans.nama_perusahaan as asal')
                ->get();

            $response['sources']['pendaftars'] = DB::table('pendaftarans')
                ->join('users', 'pendaftarans.user_id', '=', 'users.id')
                ->leftJoin('mahasiswa_profiles', 'users.id', '=', 'mahasiswa_profiles.user_id')
                ->join('lowongans', 'pendaftarans.lowongan_id', '=', 'lowongans.id')
                ->join('perusahaans', 'lowongans.perusahaan_id', '=', 'perusahaans.id')
                ->whereIn('lowongans.perusahaan_id', $sourceIds)
                ->select('users.name', 'mahasiswa_profiles.nim', 'perusahaans.nama_perusahaan as asal')
                ->get();
        }

        return response()->json($response);
    }

    /**
     * EKSEKUSI PENGGABUNGAN (MERGE)
     */
    public function merge(Request $request)
    {
        $request->validate([
            'target_id'    => 'required|exists:perusahaans,id',
            'source_ids'   => 'required|array|min:1',
            'source_ids.*' => 'exists:perusahaans,id'
        ]);

        $targetId = $request->target_id;
        $sourceIds = $request->source_ids;

        if (in_array($targetId, $sourceIds)) {
            return redirect()->back()->with('error', 'Perusahaan Utama (Target) tidak boleh sama dengan Perusahaan Duplikat.');
        }

        try {
            DB::transaction(function() use ($targetId, $sourceIds) {
                $targetPerusahaan = Perusahaan::findOrFail($targetId);
                $sourcePerusahaans = Perusahaan::whereIn('id', $sourceIds)->get();
                $oldNames = $sourcePerusahaans->pluck('nama_perusahaan')->toArray();

                DB::table('lowongans')->whereIn('perusahaan_id', $sourceIds)->update(['perusahaan_id' => $targetId]);
                DB::table('spv_profiles')->whereIn('perusahaan_id', $sourceIds)->update(['perusahaan_id' => $targetId]);

                DB::table('pendaftarans')
                    ->whereIn('nama_instansi_mandiri', $oldNames)
                    ->where('jalur_magang', 'mandiri')
                    ->update(['nama_instansi_mandiri' => $targetPerusahaan->nama_perusahaan]);

                Perusahaan::whereIn('id', $sourceIds)->delete();
            });

            return redirect()->back()->with('success', 'Data instansi/perusahaan berhasil digabungkan. Semua riwayat pendaftar & logbook telah dipindahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menggabungkan perusahaan: Terjadi masalah sistem.');
        }
    }
}