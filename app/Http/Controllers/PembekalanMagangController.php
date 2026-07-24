<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PembekalanMagangController extends Controller
{
    public function index()
    {
        return view('dashboard.pembekalan-magang.index');
    }
}
