<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerifikasiMenungguController extends Controller
{
    public function index()
    {
        return view('dashboard.verifikasi-menunggu.index');
    }
}
