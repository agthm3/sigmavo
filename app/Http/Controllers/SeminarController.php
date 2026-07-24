<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SeminarController extends Controller
{
    public function index()
    {
        return view('dashboard.seminar.index');
    }
}
