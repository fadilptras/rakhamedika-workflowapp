<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use App\Models\Interaction; // Tambahkan ini
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel; // Tambahkan ini
use App\Exports\ClientAnnualExport; // Tambahkan ini

class AdminCrmController extends Controller
{
    // ... (Method index() biarkan seperti sebelumnya) ...

    public function index(Request $request)
    {
        // ... (Kode lama index tidak berubah) ...
        // Agar hemat tempat, saya skip penulisan ulang index()
        // Pastikan copy-paste dari file lama Anda untuk bagian index()
        
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

        // 4. Hitung Statistik Global
        $statsQuery = clone $query; 
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
     * Helper Perhitungan Rekap (Sama persis dengan User Controller)
     */
    private function calculateRecapData(Client $client, $year)
    {
        $yearlyInteractions = $client->interactions()->whereYear('tanggal_interaksi', $year)->get();
        $recap = [];
        $totalSaldo = 0;

        for ($m = 1; $m <= 12; $m++) {
            $monthlyData = $yearlyInteractions->filter(function ($item) use ($m) {
                return Carbon::parse($item->tanggal_interaksi)->month == $m;
            });

            $grossSales = $monthlyData->where('jenis_transaksi', 'IN')->sum(function($item){
                return $item->nilai_sales > 0 ? $item->nilai_sales : $item->nilai_kontribusi;
            });
            
            $usageOut = $monthlyData->where('jenis_transaksi', 'OUT')->sum('nilai_kontribusi');
            
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

            $monthlyNet = $netRevenue - $usageOut;
            $totalSaldo += $monthlyNet;

            $komisiList = array_unique($komisiList);
            $komisiText = empty($komisiList) ? '-' : implode(', ', $komisiList);
            if(empty($komisiList) && $grossSales > 0) $komisiText = 'Var'; 

            $recap[] = [
                'month_name' => Carbon::create()->month($m)->translatedFormat('F'),
                'komisi_text'=> $komisiText,
                'gross_in'   => $grossSales,
                'net_value'  => $netRevenue,
                'out'        => $usageOut,
                'saldo'      => $totalSaldo
            ];
        }

        $yearlyTotals = [
            'gross_in'  => collect($recap)->sum('gross_in'),
            'net_value' => collect($recap)->sum('net_value'),
            'out'       => collect($recap)->sum('out'),
            'saldo'     => $totalSaldo
        ];

        return ['recap' => $recap, 'totals' => $yearlyTotals];
    }

    public function show(Client $client, Request $request)
    {
        $year = $request->input('year', date('Y'));

        // 1. Data Riwayat (Pagination)
        $interactions = $client->interactions()
                               ->orderBy('tanggal_interaksi', 'desc')
                               ->paginate(15); 

        // 2. Hitung Data Rekap (Pakai Helper)
        $calc = $this->calculateRecapData($client, $year);

        return view('admin.crm.show', [
            'title'        => 'Detail Admin: ' . $client->nama_user,
            'client'       => $client,
            'interactions' => $interactions,
            'recap'        => $calc['recap'],
            'year'         => $year,
            'yearlyTotals' => $calc['totals']
        ]);
    }

    /**
     * ADMIN: Update Data Klien
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'nama_user'       => 'required|string|max:255',
            'nama_perusahaan' => 'required|string|max:255',
            'area'            => 'nullable|string|max:100',
            'email'           => 'nullable|email',
            'no_telpon'       => 'nullable|string',
            'alamat'          => 'nullable|string',
            'bank'            => 'nullable|string',
            'no_rekening'     => 'nullable|string',
            'saldo_awal'      => 'nullable|numeric',
            'tanggal_berdiri' => 'nullable|date',
        ]);

        $client->update($validated);

        return redirect()->back()->with('success', 'Data klien berhasil diperbarui oleh Admin!');
    }

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

        return redirect()->back()->with('success', 'Transaksi sales berhasil ditambahkan oleh Admin!');
    }

    /**
     * ADMIN: Simpan Pengeluaran Support (OUT).
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
            'nilai_sales'       => 0, 
            'nilai_kontribusi'  => $request->nominal, 
            'catatan'           => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Dana support berhasil dicatat oleh Admin!');
    }

    /**
     * ADMIN: Hapus Klien
     */
    public function destroyClient(Client $client)
    {
        $client->delete();
        return redirect()->route('admin.crm.index')->with('success', 'Data klien berhasil dihapus oleh Admin.');
    }

    /**
     * ADMIN: Hapus Transaksi Sales/Interaction
     */
    public function destroyInteraction(Interaction $interaction)
    {
        $interaction->delete();
        return redirect()->back()->with('success', 'Transaksi berhasil dihapus oleh Admin.');
    }

    /**
     * ADMIN: Export Excel (Logic sama persis dengan User)
     */
    public function exportClientRecap(Client $client, Request $request)
    {
        $year = $request->input('year', date('Y'));
        $calc = $this->calculateRecapData($client, $year);

        $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '_', $client->nama_user);
        $filename = 'ADMIN_Rekap_Sales_' . $cleanName . '_' . $year . '.xlsx';

        return Excel::download(new ClientAnnualExport($client, $calc['recap'], $year, $calc['totals']), $filename);
    }
}