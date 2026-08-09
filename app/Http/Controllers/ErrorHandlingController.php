<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ErrorHandlingController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        if (!$currentUser->hasAnyRole(['admin', 'superadmin'])) {
            return redirect()->route('dashboard-analitik')->with('error', 'Akses ditolak.');
        }

        $showErrorDetail = Setting::getByKey('show_error_detail', 'true') === 'true';

        return view('dashboard.manajemen-akun.error-handling', compact('showErrorDetail', 'currentUser'));
    }

    public function updateToggle(Request $request)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        if (!$currentUser->hasAnyRole(['admin', 'superadmin'])) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $status = $request->has('show_error_detail') ? 'true' : 'false';
        Setting::setKey('show_error_detail', $status);

        $pesan = $status === 'true' 
            ? 'Detail Technical Error Code (Collapsible) BERHASIL DIAKTIFKAN pada halaman error.' 
            : 'Detail Technical Error Code BERHASIL DINONAKTIFKAN (Disembunyikan sepenuhnya dari user).';

        return redirect()->back()->with('success', $pesan);
    }
}