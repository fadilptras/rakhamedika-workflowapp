<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PengajuanDokumenController extends Controller
{
    public function pengajuan_dokumen()
    {
        $title = 'Pengajuan Dokumen';
        return view('users.pengajuan-dokumen', compact('title'));
    }
}
