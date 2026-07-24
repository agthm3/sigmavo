<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PerluVerifikasiController extends Controller
{
    public function index()
    {
        return view('dashboard.perlu-verifikasi.index');
    }
}
