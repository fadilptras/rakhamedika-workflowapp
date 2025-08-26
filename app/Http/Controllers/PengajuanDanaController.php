<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PengajuanDanaController extends Controller
{
    /**
     * Menampilkan halaman untuk membuat data absen baru.
     */
    public function pengajuan_dana()
    {
        $title = 'Pengajuan Dana';
        return view('users.pengajuan-dana', compact('title'));
    }
}
