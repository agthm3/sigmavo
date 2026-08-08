<?php

namespace App\Http\Controllers;

use App\Models\RubrikPenilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RubrikPenilaianController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasRole('admin')) {
            return redirect()->route('dashboard-analitik')->with('error', 'Akses Ditolak! Hanya Superadmin yang dapat mengakses Rubrik Penilaian Global.');
        }

        $rubriks = RubrikPenilaian::orderBy('no_urut', 'asc')->get();
        $totalBobot = $rubriks->sum('bobot');
        $isBobotValid = $totalBobot == 100;

        return view('dashboard.manajemen-akun.rubrik-penilaian', compact('rubriks', 'totalBobot', 'isBobotValid'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Akses Ditolak');
        }

        $request->validate([
            'no_urut'   => 'required|integer|min:1',
            'komponen'  => 'required|string|max:255',
            'indikator' => 'required|string',
            'bobot'     => 'required|numeric|min:1|max:100',
        ]);

        RubrikPenilaian::create([
            'no_urut'   => $request->no_urut,
            'komponen'  => $request->komponen,
            'indikator' => $request->indikator,
            'bobot'     => $request->bobot,
        ]);

        return redirect()->back()->with('success', 'Berhasil menambahkan Komponen Penilaian baru. Pastikan Total Bobot mencapai 100%!');
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Akses Ditolak');
        }

        $rubrik = RubrikPenilaian::findOrFail($id);

        $request->validate([
            'no_urut'   => 'required|integer|min:1',
            'komponen'  => 'required|string|max:255',
            'indikator' => 'required|string',
            'bobot'     => 'required|numeric|min:1|max:100',
        ]);

        $rubrik->update([
            'no_urut'   => $request->no_urut,
            'komponen'  => $request->komponen,
            'indikator' => $request->indikator,
            'bobot'     => $request->bobot,
        ]);

        return redirect()->back()->with('success', 'Berhasil memperbarui Komponen Penilaian.');
    }

    public function destroy($id)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Akses Ditolak');
        }

        $rubrik = RubrikPenilaian::findOrFail($id);
        $rubrik->delete();

        return redirect()->back()->with('success', 'Komponen Penilaian berhasil dihapus.');
    }
}