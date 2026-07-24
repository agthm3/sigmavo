<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenilaianListingMahasiswaController extends Controller
{
    public function index ()
    {
        return view('dashboard.listing-mahasiswa.index');
    }
}
