<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CrmController extends Controller
{
    /**
     * Menampilkan halaman utama CRM yang berisi daftar, 
     * form tambah (via modal), dan detail (via modal).
     */
    public function index()
    {
        // Nantinya, di sini kita akan mengambil data klien dari database
        // $clients = Client::where('user_id', auth()->id())->get();
        
        return view('users.crm', ['title' => 'Manajemen Klien']);
    }
}