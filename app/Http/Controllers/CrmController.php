<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Interaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientAnnualExport;
use App\Exports\MatrixAnnualExport;

class CrmController extends Controller
{
    // Helper Hak Akses
    private function hasFullAccess()
    {
        $user = Auth::user();
        if ($user->jabatan === 'Direktur') return true;
        if ($user->is_kepala_divisi && in_array($user->divisi, ['Marketing dan Operasional'])) return true;
        return false;
    }

    public function index()
    {
        $query = Client::with('interactions')->orderBy('nama_user', 'asc');
        if (!$this->hasFullAccess()) {
            $query->where('user_id', Auth::id());
        }
        return view('users.crm.index', ['title' => 'Sistem Informasi Sales (CRM)', 'clients' => $query->get()]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Client
            'nama_user'         => 'required|string|max:255',
            'email'             => 'nullable|email|max:255',
            'no_telpon'         => 'nullable|string|max:50',
            'tanggal_lahir'     => 'nullable|date',
            'alamat_user'       => 'nullable|string', 
            'jabatan'           => 'nullable|string|max:100', 
            'hobby_client'      => 'nullable|string|max:255', 

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

        if ($validator->fails()) return redirect()->back()->withErrors($validator, 'createClient')->withInput();

        $data = $validator->validated();
        $data['user_id'] = Auth::id();
        $data['pic']     = Auth::user()->name; 

        $client = Client::create($data);
        return redirect()->route('crm.show', $client->id)->with('success', 'Data Klien berhasil dibuat!');
    }

    public function show(Client $client, Request $request)
    {
        if ($client->user_id !== Auth::id() && !$this->hasFullAccess()) abort(403, 'Akses Ditolak.');

        $year = $request->input('year', date('Y'));
        $calc = $this->calculateRecapData($client, $year);

        $interactions = $client->interactions()->orderBy('tanggal_interaksi', 'desc')->paginate(10); 

        return view('users.crm.show', [
            'title' => 'Detail Sales: ' . $client->nama_user,
            'client' => $client,
            'interactions' => $interactions,
            'recap' => $calc['recap'],
            'year' => $year,
            'yearlyTotals' => $calc['totals'],

            // DATA BARU DIKIRIM KE VIEW
            'startingBalance' => $calc['starting_balance'], 
            'startingLabel'   => $calc['starting_label']
        ]);
    }

    public function edit(Client $client)
    {
        if ($client->user_id !== Auth::id() && !$this->hasFullAccess()) abort(403);
        return view('users.crm.show', ['title' => 'Edit Data Klien', 'client' => $client]);
    }

    public function update(Request $request, Client $client)
    {
        if ($client->user_id !== Auth::id() && !$this->hasFullAccess()) abort(403);
        $validated = $request->validate([
            'nama_user'         => 'required|string|max:255',
            'email'             => 'nullable|email',
            'no_telpon'         => 'nullable|string',
            'tanggal_lahir'     => 'nullable|date',
            'alamat_user'       => 'nullable|string',
            'jabatan'           => 'nullable|string|max:100', 
            'hobby_client'      => 'nullable|string|max:255', 

            'nama_perusahaan'   => 'required|string|max:255',
            'tanggal_berdiri'   => 'nullable|date',
            'area'              => 'nullable|string',
            'alamat_perusahaan' => 'nullable|string',

            'bank'              => 'nullable|string',
            'no_rekening'       => 'nullable|string',
            'nama_di_rekening'  => 'nullable|string',
            'saldo_awal'        => 'nullable|numeric',
        ]);
        $client->update($validated);
        return redirect()->route('crm.show', $client->id)->with('success', 'Data klien berhasil diperbarui!');
    }

    // Simpan Sales (IN)
    public function storeInteraction(Request $request)
    {
        $client = Client::findOrFail($request->client_id);
        if ($client->user_id !== Auth::id() && !$this->hasFullAccess()) abort(403);

        $request->merge([
            'nilai_sales' => str_replace('.', '', $request->nilai_sales),
        ]);

        $request->validate([
            'client_id' => 'required|exists:clients,id', 
            'nama_produk' => 'required|string|max:255',
            'nilai_sales' => 'required|numeric|min:0', 
            'komisi' => 'required|numeric|min:0|max:100', 
            'tanggal_interaksi' => 'required|date', 
            'catatan' => 'nullable|string',
        ]);

        Interaction::create([
            'client_id' => $request->client_id, 'jenis_transaksi' => 'IN', 
            'nama_produk' => $request->nama_produk, 'tanggal_interaksi' => $request->tanggal_interaksi,
            'nilai_sales' => $request->nilai_sales, 'nilai_kontribusi' => $request->nilai_sales,
            'komisi' => $request->komisi, 'catatan' => "[Rate:" . $request->komisi . "] " . $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Transaksi sales berhasil ditambahkan!');
    }

    // Simpan Support (OUT) - MENGURANGI SALDO
    public function storeSupport(Request $request)
    {
        $client = Client::findOrFail($request->client_id);
        if ($client->user_id !== Auth::id() && !$this->hasFullAccess()) abort(403);

        $request->merge([
            'nominal' => str_replace('.', '', $request->nominal),
        ]);

        $request->validate([
            'client_id' => 'required|exists:clients,id', 'keperluan' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0', 'tanggal_interaksi' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        Interaction::create([
            'client_id' => $request->client_id, 'jenis_transaksi' => 'OUT', 
            'nama_produk' => 'USAGE : ' . $request->keperluan, 'tanggal_interaksi' => $request->tanggal_interaksi,
            'nilai_sales' => 0, 'nilai_kontribusi' => $request->nominal, 'catatan' => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Dana support berhasil dicatat!');
    }

    // === [BARU] Simpan Entertain (TIDAK MENGURANGI SALDO) ===
    public function storeEntertain(Request $request)
    {
        $client = Client::findOrFail($request->client_id);
        if ($client->user_id !== Auth::id() && !$this->hasFullAccess()) abort(403);

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

        return redirect()->back()->with('success', 'Aktivitas berhasil dicatat (Saldo aman).');
    }

    public function destroyClient(Client $client)
    {
        if ($client->user_id !== Auth::id() && !$this->hasFullAccess()) return redirect()->back()->with('error', 'Akses ditolak.');
        $client->delete(); 
        return redirect()->route('crm.index')->with('success', 'Data klien berhasil dihapus.');
    }

    public function destroyInteraction(Interaction $interaction)
    {
        if ($interaction->client->user_id !== Auth::id() && !$this->hasFullAccess()) abort(403);
        $interaction->delete();
        return redirect()->back()->with('success', 'Data transaksi berhasil dihapus.');
    }

    public function matrix(Request $request)
    {
        $year = $request->input('year', date('Y'));
        
        // PERBAIKAN: Hapus constraint ->whereYear(...) agar kita bisa hitung saldo masa lalu
        $query = Client::with('interactions')->orderBy('nama_user', 'asc');

        if (!$this->hasFullAccess()) $query->where('user_id', Auth::id());

        $clients = $query->get();
        $months = [];
        for($m=1; $m<=12; $m++) $months[$m] = Carbon::create()->month($m)->translatedFormat('F');

        $monthlyTotals = [];
        $grandTotalYear = 0;

        foreach($months as $m => $name) {
            $sumMonth = 0;
            foreach($clients as $c) {
                // PERBAIKAN: Filter data harus spesifik BULAN dan TAHUN karena kita meload semua data
                $monthlyData = $c->interactions->filter(function($i) use ($m, $year){ 
                    return Carbon::parse($i->tanggal_interaksi)->month == $m 
                        && Carbon::parse($i->tanggal_interaksi)->year == $year; 
                });
                
                $income = 0;
                foreach($monthlyData->where('jenis_transaksi', 'IN') as $sale) {
                    $r = $sale->komisi ?? 0;
                    if(!$r && preg_match('/\[Rate:([\d\.]+)\]/', $sale->catatan, $matches)) $r = (float)$matches[1];
                    $nom = $sale->nilai_sales > 0 ? $sale->nilai_sales : $sale->nilai_kontribusi;
                    $income += $nom * ($r/100);
                }
                $usage = $monthlyData->where('jenis_transaksi', 'OUT')->sum('nilai_kontribusi');
                $sumMonth += ($income - $usage);
            }
            $monthlyTotals[$m] = $sumMonth;
            $grandTotalYear += $sumMonth;
        }

        return view('users.crm.matrix', [
            'title' => 'Matrix Sales ' . $year, 'clients' => $clients, 'year' => $year,
            'months' => $months, 'monthlyTotals' => $monthlyTotals, 'grandTotalYear' => $grandTotalYear
        ]);
    }

    public function exportMatrix(Request $request)
    {
        $year = $request->input('year', date('Y'));

        // PERBAIKAN: Hapus constraint ->whereYear(...) sama seperti method matrix
        $query = Client::with('interactions')->orderBy('nama_user', 'asc');

        if (!$this->hasFullAccess()) {
            $query->where('user_id', Auth::id());
        }

        $clients = $query->get();

        $months = [];
        for($m=1; $m<=12; $m++) {
            $months[$m] = Carbon::create()->month($m)->translatedFormat('F');
        }

        $filename = 'Laporan_Matrix_Sales_' . $year . '.xlsx';
        return Excel::download(new MatrixAnnualExport($clients, $months, $year), $filename);
    }
    
    public function exportClientRecap(Client $client, Request $request)
    {
        if ($client->user_id !== Auth::id() && !$this->hasFullAccess()) abort(403);
        $year = $request->input('year', date('Y'));
        $calc = $this->calculateRecapData($client, $year);
        $filename = 'Rekap_Sales_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $client->nama_user) . '_' . $year . '.xlsx';
        return Excel::download(new ClientAnnualExport($client, $calc['recap'], $year, $calc['totals']), $filename);
    }

    // === HELPER PERHITUNGAN REKAP ===
    private function calculateRecapData(Client $client, $year)
    {
        // A. Tentukan Label Saldo
        // Jika tahun yg dipilih > tahun pembuatan klien, labelnya "Saldo [Tahun Lalu]"
        // Jika sama atau kurang, labelnya "Saldo Awal"
        $creationYear = $client->created_at->format('Y');
        if ($year > $creationYear) {
            $startingLabel = "Saldo Tahun " . ($year - 1);
        } else {
            $startingLabel = "Saldo Awal";
        }

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
        $currentSaldo = $startingBalance; 

        for ($m = 1; $m <= 12; $m++) {
            $monthlyData = $yearlyInteractions->filter(fn($item) => Carbon::parse($item->tanggal_interaksi)->month == $m);

            // Gross
            $grossSales = $monthlyData->where('jenis_transaksi', 'IN')->sum(fn($item) => $item->nilai_sales > 0 ? $item->nilai_sales : $item->nilai_kontribusi);
            // Out
            $usageOut = $monthlyData->where('jenis_transaksi', 'OUT')->sum('nilai_kontribusi');
            // Net
            $netRevenue = 0;
            $komisiList = [];
            foreach($monthlyData->where('jenis_transaksi', 'IN') as $sale) {
                $rate = $sale->komisi ?? 0;
                if (!$rate && preg_match('/\[Rate:([\d\.]+)\]/', $sale->catatan, $matches)) $rate = floatval($matches[1]);
                $nominal = $sale->nilai_sales > 0 ? $sale->nilai_sales : $sale->nilai_kontribusi;
                $netRevenue += $nominal * ($rate / 100);
                if($rate > 0) $komisiList[] = $rate.'%';
            }
            
            $currentSaldo += ($netRevenue - $usageOut);
            
            $komisiList = array_unique($komisiList);
            $komisiText = empty($komisiList) ? '-' : implode(', ', $komisiList);
            if(empty($komisiList) && $grossSales > 0) $komisiText = 'Var'; 

            $recap[] = [
                'month_name' => Carbon::create()->month($m)->translatedFormat('F'),
                'komisi_text'=> $komisiText, 
                'gross_in' => $grossSales,
                'net_value'  => $netRevenue, 
                'out' => $usageOut, 
                'saldo' => $currentSaldo
            ];
        }

        $yearlyTotals = [
            'gross_in' => collect($recap)->sum('gross_in'), 
            'net_value' => collect($recap)->sum('net_value'),
            'out' => collect($recap)->sum('out'), 
            'saldo' => $currentSaldo
        ];

        return [
            'recap' => $recap, 
            'totals' => $yearlyTotals, 
            'starting_balance' => $startingBalance,
            'starting_label' => $startingLabel // Kirim label dinamis
        ];
    }
}