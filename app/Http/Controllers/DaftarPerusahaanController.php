<?php

namespace App\Http\Controllers;

use App\Models\Perusahaan;
use Illuminate\Http\Request;

class DaftarPerusahaanController extends Controller
{
    public function index(Request $request)
    {
        $query = Perusahaan::query();

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_perusahaan', 'like', "%{$search}%")
                  ->orWhere('sektor_industri', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        // Filter Sektor Industri
        if ($request->filled('sektor') && $request->sektor !== 'semua') {
            $query->where('sektor_industri', $request->sektor);
        }

        // Filter Status Kerjasama
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status_kerjasama', $request->status);
        }

        $perusahaans = $query->latest()->paginate(10)->withQueryString();

        // Ringkasan Statistik
        $totalMitra = Perusahaan::count();
        $totalMoU = Perusahaan::where('status_kerjasama', 'MoU Resmi')->count();

        return view('dashboard.daftar-perusahaan.index', compact('perusahaans', 'totalMitra', 'totalMoU'));
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
}