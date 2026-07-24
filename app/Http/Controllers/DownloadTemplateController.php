<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DownloadTemplateController extends Controller
{
    public function index()
    {
        return view('dashboard.download-template.index');
    }
}
