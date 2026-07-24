<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ListingProgramController extends Controller
{
    public function index()
    {
        return view('dashboard.listing-program.index');
    }
}
