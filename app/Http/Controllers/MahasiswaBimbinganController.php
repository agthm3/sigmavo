<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MahasiswaBimbinganController extends Controller
{
    public function index()
    {
        return view('dashboard.mahasiswa-bimbingan.index');
    }
}
