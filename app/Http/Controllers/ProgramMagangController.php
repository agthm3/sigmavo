<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProgramMagangController extends Controller
{
    public function index()
    {
        return view('dashboard.program-magang.index');
    }
}
