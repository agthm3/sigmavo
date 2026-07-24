<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RiwayatMagangController extends Controller
{
    public function index()
    {
        return view('dashboard.riwayat-magang.index');
    }
}
