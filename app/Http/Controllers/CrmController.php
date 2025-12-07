<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Interaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; 

class CrmController extends Controller
{
    /**
     * Tampilkan Halaman Utama (Daftar Klien)
     */
    public function index()
    {
        $clients = Client::with('interactions')
                         ->orderBy('nama_user', 'asc')
                         ->get();
        
        return view('users.crm.index', [ 
            'title' => 'Sistem Informasi Sales (CRM)',
            'clients' => $clients
        ]);
    }

    /**
     * Simpan Identitas Klien Baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area'            => 'nullable|string|max:100',
            'pic'             => 'nullable|string|max:100',
            'nama_user'       => 'required|string|max:255',
            'nama_perusahaan' => 'required|string|max:255',
            'tanggal_berdiri' => 'nullable|date',
            'email'           => 'nullable|email|max:255',
            'no_telpon'       => 'nullable|string|max:50',
            'alamat'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'createClient')->withInput();
        }

        $data = $validator->validated();
        $data['user_id'] = Auth::id();
        
        $client = Client::create($data);

        return redirect()->route('crm.show', $client->id)
                         ->with('success', 'Data Klien berhasil dibuat! Silakan input transaksi sales.');
    }

    /**
     * Tampilkan Detail Klien, History, dan Rekap Tahunan
     */
    public function show(Client $client, Request $request)
    {
        // 1. Ambil tahun dari request (untuk filter rekap), default tahun sekarang
        $year = $request->input('year', date('Y'));

        // 2. Data untuk Tab "Riwayat Transaksi" (Pagination)
        $interactions = $client->interactions()
                               ->orderBy('tanggal_interaksi', 'desc')
                               ->paginate(10); 

        // 3. Data untuk Tab "Rekap Sales Tahunan"
        // Ambil semua transaksi di tahun yang dipilih
        $yearlyInteractions = $client->interactions()
                                     ->whereYear('tanggal_interaksi', $year)
                                     ->get();

        $recap = [];
        $totalSaldo = 0; // Saldo kumulatif berjalan

        // Loop bulan 1 s/d 12
        for ($m = 1; $m <= 12; $m++) {
            // Filter transaksi hanya di bulan $m
            $monthlyData = $yearlyInteractions->filter(function ($item) use ($m) {
                return Carbon::parse($item->tanggal_interaksi)->month == $m;
            });

            // Hitung Pemasukan (Sales)
            $in = $monthlyData->where('jenis_transaksi', 'IN')->sum('nilai_kontribusi');
            
            // Hitung Pengeluaran (Support/Usage)
            $out = $monthlyData->where('jenis_transaksi', 'OUT')->sum('nilai_kontribusi');
            
            // Hitung Net (Bersih bulan ini)
            $net = $in - $out;
            
            // Tambahkan ke saldo kumulatif tahunan
            $totalSaldo += $net;

            // Logika Komisi: Tampilkan 10% jika ada pemasukan sales, strip jika tidak ada
            $komisi_text = ($in > 0) ? '10%' : '-';

            $recap[] = [
                'month_name' => Carbon::create()->month($m)->translatedFormat('F'), // Nama Bulan
                'komisi'     => $komisi_text,
                'in'         => $in,
                'out'        => $out,
                'net'        => $net,
                'saldo'      => $totalSaldo
            ];
        }

        // Hitung Total Bawah (Footer Tabel)
        $yearlyTotals = [
            'in'  => collect($recap)->sum('in'),
            'out' => collect($recap)->sum('out'),
            'net' => collect($recap)->sum('net'),
        ];

        return view('users.crm.show', [
            'title'        => 'Detail Sales: ' . $client->nama_user,
            'client'       => $client,
            'interactions' => $interactions,
            'recap'        => $recap,        // Data tabel rekap
            'year'         => $year,         // Tahun terpilih
            'yearlyTotals' => $yearlyTotals  // Total setahun
        ]);
    }

    /**
     * Simpan Transaksi PEMASUKAN (Sales)
     */
    public function storeInteraction(Request $request)
    {
        $request->validate([
            'client_id'         => 'required|exists:clients,id',
            'nama_produk'       => 'required|string|max:255',
            'nilai_sales'       => 'required|numeric|min:0',      
            'komisi'            => 'required|numeric|min:0|max:100', 
            'tanggal_interaksi' => 'required|date',
            'catatan'           => 'nullable|string',
        ]);

        $netContribution = $request->nilai_sales * ($request->komisi / 100);

        Interaction::create([
            'client_id'         => $request->client_id,
            'jenis_transaksi'   => 'IN', 
            'nama_produk'       => $request->nama_produk,
            'tanggal_interaksi' => $request->tanggal_interaksi,
            // Tambahkan dua baris ini agar tersimpan di database:
            'nilai_sales'       => $request->nilai_sales, 
            'komisi'            => $request->komisi,
            // --------------------------------------------------
            'nilai_kontribusi'  => $netContribution,
            'catatan'           => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Transaksi sales berhasil ditambahkan!');
    }

    /**
     * Simpan Transaksi PENGELUARAN (Support)
     */
    public function storeSupport(Request $request)
    {
        $request->validate([
            'client_id'         => 'required|exists:clients,id',
            'keperluan'         => 'required|string|max:255',
            'nominal'           => 'required|numeric|min:0',
            'tanggal_interaksi' => 'required|date',
            'catatan'           => 'nullable|string',
        ]);

        Interaction::create([
            'client_id'         => $request->client_id,
            'jenis_transaksi'   => 'OUT', 
            'nama_produk'       => 'USAGE : ' . $request->keperluan,
            'tanggal_interaksi' => $request->tanggal_interaksi,
            'nilai_kontribusi'  => $request->nominal, 
            'catatan'           => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Dana support berhasil dicatat!');
    }

    /**
     * Hapus Data Klien
     */
    public function destroyClient(Client $client)
    {
        if ($client->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }
        $client->delete();
        return redirect()->route('crm.index')->with('success', 'Data klien dihapus.');
    }

    /**
     * Hapus Satu Transaksi
     */
    public function destroyInteraction(Interaction $interaction)
    {
        $interaction->delete();
        return redirect()->back()->with('success', 'Data transaksi berhasil dihapus.');
    }
}