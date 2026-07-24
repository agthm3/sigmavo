<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DaftarLowonganController extends Controller
{
    public function index()
    {
        return view('dashboard.daftar-lowongan.index');
    }
}
