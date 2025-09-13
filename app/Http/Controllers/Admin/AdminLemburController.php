<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lembur;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class AdminLemburController extends Controller
{
    /**
     * Menampilkan rekap lembur karyawan.
     */
    public function index(Request $request)
    {
        $lemburRecords = Lembur::with('user')->latest()->paginate(15);
        $users = User::where('role', 'user')->orderBy('name')->get();
        $divisions = User::select('divisi')->whereNotNull('divisi')->distinct()->pluck('divisi');

        return view('admin.lembur.index', [
            'title' => 'Rekap Lembur Karyawan',
            'lemburRecords' => $lemburRecords,
            'users' => $users,
            'divisions' => $divisions,
            'months' => [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ],
            'years' => range(Carbon::now()->year, Carbon::now()->year - 5)
        ]);
    }
}