<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TerverifikasiController extends Controller
{
    public function index()
    {
        return view('dashboard.terverifikasi.index');
    }
}
