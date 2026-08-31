<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'Akses Ditolak');
        }

        // Default menampilkan role SPV jika tidak ada yang dipilih
        $role = $request->get('role', 'spv'); 

        $users = User::with(['roles', 'dosenProfile.prodi', 'spvProfile.perusahaan', 'adminProdiProfile.prodi'])
            ->whereHas('roles', function($q) use ($role) {
                $q->where('name', $role);
            })
            ->when($request->search, function($q) use ($request) {
                $q->where(function($sub) use ($request) {
                    $sub->where('name', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%");
                });
            })
            ->paginate(10)->withQueryString();

        return view('dashboard.monitoring.index', compact('users', 'role'));
    }
}