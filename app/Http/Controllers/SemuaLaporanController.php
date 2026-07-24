<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SemuaLaporanController extends Controller
{
    public function index()
    {
        return view('dashboard.semua-laporan.index');
    }
}
