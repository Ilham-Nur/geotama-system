<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuratTugasController extends Controller
{
    public function index()
    {
        return view('surat-tugas.index');
    }
}
