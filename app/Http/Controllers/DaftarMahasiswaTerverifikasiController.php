<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DaftarMahasiswaTerverifikasiController extends Controller
{
    public function index()
    {
        return view('dashboard.verifikasi.index');
    }
}
