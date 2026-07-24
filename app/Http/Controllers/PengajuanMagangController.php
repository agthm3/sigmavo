<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PengajuanMagangController extends Controller
{
    public function index()
    {
        return view('dashboard.pengajuan-magang.index');
    }
}
