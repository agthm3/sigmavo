<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DaftarPerusahaanController extends Controller
{
    public function index()
    {
        return view('dashboard.daftar-perusahaan.index');
    }
}
