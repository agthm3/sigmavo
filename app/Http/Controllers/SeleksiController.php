<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SeleksiController extends Controller
{
    public function index()
    {
        return view('dashboard.seleksi.index');
    }
}
