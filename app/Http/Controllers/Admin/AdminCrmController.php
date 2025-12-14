<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use App\Models\Interaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientAnnualExport;
use App\Exports\MatrixAnnualExport;

class AdminCrmController extends Controller
{
    /**
     * Halaman Utama Monitoring Sales (Index)
     */
    public function index(Request $request)
    {
        // 1. Ambil Filter Sales Person
        $userId = $request->input('user_id');

        // 2. Query Data Klien dengan Relasi
        $query = Client::with(['user', 'interactions']);

        // Filter jika ada user dipilih
        if ($userId) {
            $query->where('user_id', $userId);
        }

        // 3. Ambil data dengan Pagination
        $clients = $query->orderBy('updated_at', 'desc')->paginate(15);

        // 4. Hitung Statistik Global (Tanpa Pagination)
        $statsQuery = clone $query; 
        $statsQuery->getQuery()->orders = null;
        $statsQuery->getQuery()->limit = null;
        $statsQuery->getQuery()->offset = null;
        
        $allClients = $statsQuery->get();

        $totalOmset = 0; 
        $totalNet   = 0; 

        foreach($allClients as $c) {
            $c_gross_total = 0;
            $c_net_total   = 0; 
            $c_usage_total = 0; 

            foreach($c->interactions as $item) {
                if($item->jenis_transaksi == 'IN') {
                    $gross = $item->nilai_sales > 0 ? $item->nilai_sales : $item->nilai_kontribusi;
                    $rate = $item->komisi ?? 0;
                    if (!$rate && preg_match('/\[Rate:([\d\.]+)\]/', $item->catatan, $m)) {
                        $rate = floatval($m[1]);
                    }
                    $value = $gross * ($rate / 100);
                    $c_gross_total += $gross;
                    $c_net_total   += $value;
                } elseif ($item->jenis_transaksi == 'OUT') {
                    $c_usage_total += $item->nilai_kontribusi;
                }
            }
            $saldo_klien = $c_net_total - $c_usage_total;
            $totalOmset += $c_gross_total;
            $totalNet   += $saldo_klien;
        }

        $users = User::orderBy('name', 'asc')->get(); 

        return view('admin.crm.index', [
            'title'      => 'Monitoring Sales & CRM',
            'clients'    => $clients,
            'users'      => $users,
            'totalOmset' => $totalOmset,
            'totalNet'   => $totalNet,
            'filterUser' => $userId
        ]);
    }

    /**
     * Export Matrix Sales Tahunan (Excel)
     */
    public function exportMatrix(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $userId = $request->input('user_id');

        $query = Client::with('interactions')->orderBy('nama_user', 'asc');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $clients = $query->get();

        $months = [];
        for($m=1; $m<=12; $m++) {
            $months[$m] = Carbon::create()->month($m)->translatedFormat('F');
        }

        $filename = 'ADMIN_Laporan_Matrix_Sales_' . $year . '.xlsx';
        return Excel::download(new MatrixAnnualExport($clients, $months, $year), $filename);
    }

    /**
     * Simpan Klien Baru (Create)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Client
            'nama_user'         => 'required|string|max:255',
            'email'             => 'nullable|email|max:255',
            'no_telpon'         => 'nullable|string|max:50',
            'tanggal_lahir'     => 'nullable|date',
            'alamat_user'       => 'nullable|string', 

            // Perusahaan
            'nama_perusahaan'   => 'required|string|max:255',
            'tanggal_berdiri'   => 'nullable|date',
            'area'              => 'nullable|string|max:100',
            'alamat_perusahaan' => 'nullable|string', 

            // Bank
            'bank'              => 'nullable|string|max:50',
            'no_rekening'       => 'nullable|string|max:50',
            'nama_di_rekening'  => 'nullable|string|max:100',   
            'saldo_awal'        => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'createClient')
                ->withInput()
                ->with('error', 'Gagal menyimpan data klien. Periksa inputan anda.');
        }

        $data = $validator->validated();
        
        // Admin membuat klien atas nama dirinya sendiri
        $data['user_id'] = Auth::id();
        $data['pic']     = Auth::user()->name; 

        $client = Client::create($data);
        return redirect()->route('admin.crm.show', $client->id)->with('success', 'Data Klien berhasil dibuat oleh Admin!');
    }

    /**
     * Halaman Detail Klien (Show)
     */
    public function show(Client $client, Request $request)
    {
        $year = $request->input('year', date('Y'));

        // 1. Data Riwayat (Pagination)
        $interactions = $client->interactions()
                               ->orderBy('tanggal_interaksi', 'desc')
                               ->paginate(15); 

        // 2. Hitung Data Rekap
        $calc = $this->calculateRecapData($client, $year);

        return view('admin.crm.show', [
            'title'        => 'Detail Admin: ' . $client->nama_user,
            'client'       => $client,
            'interactions' => $interactions,
            'recap'        => $calc['recap'],
            'year'         => $year,
            'yearlyTotals' => $calc['totals'],
            
            // [FIXED] Variabel yang sebelumnya hilang
            'startingBalance' => $calc['starting_balance'], 
            'startingLabel'   => $calc['starting_label']
        ]);
    }

    /**
     * Halaman Edit Klien
     */
    public function edit(Client $client)
    {
        return view('admin.crm.show', ['title' => 'Edit Data Klien', 'client' => $client]);
    }

    /**
     * Update Data Klien
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'nama_user'       => 'required|string|max:255',
            'nama_perusahaan' => 'required|string|max:255',
            'area'            => 'nullable|string|max:100',
            'email'           => 'nullable|email',
            'no_telpon'       => 'nullable|string',
            'alamat_user'     => 'nullable|string',
            'alamat_perusahaan' => 'nullable|string',
            'bank'            => 'nullable|string',
            'no_rekening'     => 'nullable|string',
            'nama_di_rekening'=> 'nullable|string',
            'saldo_awal'      => 'nullable|numeric',
            'tanggal_berdiri' => 'nullable|date',
            'tanggal_lahir'   => 'nullable|date',
        ]);

        $client->update($validated);

        return redirect()->back()->with('success', 'Data klien berhasil diperbarui oleh Admin!');
    }

    /**
     * Simpan Transaksi Sales (IN)
     */
    public function storeInteraction(Request $request)
    {
        $request->merge([
            'nilai_sales' => str_replace('.', '', $request->nilai_sales),
        ]);

        $request->validate([
            'client_id'         => 'required|exists:clients,id',
            'nama_produk'       => 'required|string|max:255',
            'nilai_sales'       => 'required|numeric|min:0',      
            'komisi'            => 'required|numeric|min:0|max:100', 
            'tanggal_interaksi' => 'required|date',
            'catatan'           => 'nullable|string',
        ]);

        // Simpan note format lama untuk kompatibilitas
        $noteWithRate = "[Rate:" . $request->komisi . "] " . $request->catatan;

        Interaction::create([
            'client_id'         => $request->client_id,
            'jenis_transaksi'   => 'IN', 
            'nama_produk'       => $request->nama_produk,
            'tanggal_interaksi' => $request->tanggal_interaksi,
            'nilai_sales'       => $request->nilai_sales, 
            'nilai_kontribusi'  => $request->nilai_sales,
            'komisi'            => $request->komisi,
            'catatan'           => $noteWithRate,
        ]);

        return redirect()->back()->with('success', 'Transaksi sales berhasil ditambahkan!');
    }

    /**
     * Simpan Pengeluaran Support (OUT)
     */
    public function storeSupport(Request $request)
    {
        $request->merge([
            'nominal' => str_replace('.', '', $request->nominal),
        ]);

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
            'nilai_sales'       => 0, 
            'nilai_kontribusi'  => $request->nominal, 
            'catatan'           => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Dana support berhasil dicatat!');
    }

    /**
     * Simpan Entertain (Tanpa Potong Saldo)
     */
    public function storeEntertain(Request $request)
    {
        $request->merge([
            'nominal' => str_replace('.', '', $request->nominal),
        ]);

        $request->validate([
            'client_id'         => 'required|exists:clients,id',
            'tanggal_interaksi' => 'required|date',
            'catatan'           => 'required|string', 
            'nominal'           => 'required|numeric|min:0',
            'lokasi'            => 'nullable|string|max:255',
            'peserta'           => 'nullable|string|max:255',
        ]);

        Interaction::create([
            'client_id'         => $request->client_id,
            'jenis_transaksi'   => 'ENTERTAIN', 
            'nama_produk'       => 'Activity / Entertain',
            'tanggal_interaksi' => $request->tanggal_interaksi,
            'nilai_sales'       => 0, 
            'nilai_kontribusi'  => $request->nominal, 
            'catatan'           => $request->catatan,
            'lokasi'            => $request->lokasi,
            'peserta'           => $request->peserta,
        ]);

        return redirect()->back()->with('success', 'Aktivitas Entertain berhasil dicatat (Saldo aman).');
    }

    /**
     * Hapus Data Klien
     */
    public function destroyClient(Client $client)
    {
        $client->delete();
        return redirect()->route('admin.crm.index')->with('success', 'Data klien berhasil dihapus oleh Admin.');
    }

    /**
     * Hapus Transaksi
     */
    public function destroyInteraction(Interaction $interaction)
    {
        $interaction->delete();
        return redirect()->back()->with('success', 'Transaksi berhasil dihapus.');
    }

    /**
     * Export Rekap Per Client
     */
    public function exportClientRecap(Client $client, Request $request)
    {
        $year = $request->input('year', date('Y'));
        $calc = $this->calculateRecapData($client, $year);

        $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '_', $client->nama_user);
        $filename = 'ADMIN_Rekap_Sales_' . $cleanName . '_' . $year . '.xlsx';

        return Excel::download(new ClientAnnualExport($client, $calc['recap'], $year, $calc['totals']), $filename);
    }

    /**
     * Helper Perhitungan Rekap (Private) - [LOGIKA DIPERBAIKI]
     */
    private function calculateRecapData(Client $client, $year)
    {
        // A. Tentukan Label Saldo
        $creationYear = $client->created_at->format('Y');
        $startingLabel = ($year > $creationYear) ? "Saldo Tahun " . ($year - 1) : "Saldo Awal";

        // B. Hitung Nominal Saldo Awal (Carry Forward)
        $startingBalance = $client->saldo_awal ?? 0;

        // Tambahkan semua akumulasi transaksi dari tahun-tahun sebelumnya
        $pastInteractions = $client->interactions()
                                   ->whereYear('tanggal_interaksi', '<', $year)
                                   ->get();

        foreach($pastInteractions as $item) {
            if ($item->jenis_transaksi == 'OUT') {
                $startingBalance -= $item->nilai_kontribusi;
            } 
            elseif ($item->jenis_transaksi == 'IN') {
                 $rate = $item->komisi ?? 0;
                 if (!$rate && preg_match('/\[Rate:([\d\.]+)\]/', $item->catatan, $matches)) {
                     $rate = floatval($matches[1]);
                 }
                 $nominal = $item->nilai_sales > 0 ? $item->nilai_sales : $item->nilai_kontribusi;
                 $net = $nominal * ($rate / 100);
                 
                 $startingBalance += $net;
            }
        }

        // C. Hitung Bulanan Tahun Ini
        $yearlyInteractions = $client->interactions()->whereYear('tanggal_interaksi', $year)->get();
        $recap = [];
        $currentSaldo = $startingBalance; // Start from carry forward

        for ($m = 1; $m <= 12; $m++) {
            $monthlyData = $yearlyInteractions->filter(function ($item) use ($m) {
                return Carbon::parse($item->tanggal_interaksi)->month == $m;
            });

            // Gross
            $grossSales = $monthlyData->where('jenis_transaksi', 'IN')->sum(function($item){
                return $item->nilai_sales > 0 ? $item->nilai_sales : $item->nilai_kontribusi;
            });
            
            // Out
            $usageOut = $monthlyData->where('jenis_transaksi', 'OUT')->sum('nilai_kontribusi');
            
            // Net
            $netRevenue = 0;
            $komisiList = [];

            foreach($monthlyData->where('jenis_transaksi', 'IN') as $sale) {
                $rate = $sale->komisi ?? 0;
                if (!$rate && preg_match('/\[Rate:([\d\.]+)\]/', $sale->catatan, $matches)) {
                    $rate = floatval($matches[1]);
                }
                $nominal = $sale->nilai_sales > 0 ? $sale->nilai_sales : $sale->nilai_kontribusi;
                $netRevenue += $nominal * ($rate / 100);
                if($rate > 0) $komisiList[] = $rate.'%';
            }

            // Hitung saldo berjalan
            $currentSaldo += ($netRevenue - $usageOut);

            $komisiList = array_unique($komisiList);
            $komisiText = empty($komisiList) ? '-' : implode(', ', $komisiList);
            if(empty($komisiList) && $grossSales > 0) $komisiText = 'Var'; 

            $recap[] = [
                'month_name' => Carbon::create()->month($m)->translatedFormat('F'),
                'komisi_text'=> $komisiText,
                'gross_in'   => $grossSales,
                'net_value'  => $netRevenue,
                'out'        => $usageOut,
                'saldo'      => $currentSaldo
            ];
        }

        $yearlyTotals = [
            'gross_in'  => collect($recap)->sum('gross_in'),
            'net_value' => collect($recap)->sum('net_value'),
            'out'       => collect($recap)->sum('out'),
            'saldo'     => $currentSaldo // Saldo akhir tahun
        ];

        return [
            'recap' => $recap, 
            'totals' => $yearlyTotals,
            'starting_balance' => $startingBalance, // Kirim Saldo Awal
            'starting_label' => $startingLabel      // Kirim Label Saldo
        ];
    }
}