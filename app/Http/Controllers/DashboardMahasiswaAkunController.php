<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardMahasiswaAkunController extends Controller
{
    public function index()
    {
        return view('dashboard.akun.index');
    }
}
