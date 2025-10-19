<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Lembur;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapAbsensiExport;

class AbsensiController extends Controller
{
    /**
     * Menampilkan data absensi harian.
     */
    public function index(Request $request)
    {
        $month = intval($request->input('month', now()->month));
        $year = intval($request->input('year', now()->year));
        $day = intval($request->input('day', now()->day));
        $divisi = $request->input('divisi');
        $status = $request->input('status');

        $divisions = User::select('divisi')->whereNotNull('divisi')->distinct()->pluck('divisi');

        $start_date_month = now()->year($year)->month($month)->startOfMonth()->format('Y-m-d');
        $end_date_month = now()->year($year)->month($month)->endOfMonth()->format('Y-m-d');
        
        $date_for_page = now()->year($year)->month($month)->day($day);
        
        $isWeekend = $date_for_page->isSunday();

        $query = Absensi::with('user')
                         ->whereBetween('tanggal', [$start_date_month, $end_date_month]);

        if ($divisi) {
            $query->whereHas('user', function ($q) use ($divisi) {
                $q->where('divisi', $divisi);
            });
        }
        
        if ($status) {
            $query->where('status', $status);
        }

        $absensi_harian = $query->whereDate('tanggal', $date_for_page->format('Y-m-d'))->get();

        $months = collect(range(1, 12))->mapWithKeys(function ($bulan) {
            return [$bulan => Carbon::create()->month($bulan)->translatedFormat('F')];
        });
        $years = range(now()->year, now()->year - 5);
        $daysInMonth = $date_for_page->daysInMonth;

        return view('admin.absensi.index', compact('absensi_harian', 'month', 'year', 'day', 'divisi', 'status', 'divisions', 'months', 'years', 'daysInMonth', 'isWeekend'));
    }

    /**
     * Menampilkan halaman rekap absensi bulanan.
     */
    public function rekap(Request $request)
    {
        $title = 'Rekap Absensi Bulanan';
        
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        $divisi = $request->input('divisi');
        
        $rekapData = $this->getRekapData($startDate, $endDate, $divisi);

        $divisions = User::select('divisi')->whereNotNull('divisi')->distinct()->pluck('divisi');
        $allDates = collect();
        if ($startDate && $endDate) {
            $allDates = collect(CarbonPeriod::create($startDate, $endDate));
        }

        return view('admin.absensi.rekap', compact('title', 'rekapData', 'allDates', 'divisions', 'divisi', 'startDate', 'endDate'));
    }

    /**
     * Download rekap absensi bulanan sebagai PDF.
     */
    public function downloadPdf(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        $divisi = $request->input('divisi');

        $rekapData = $this->getRekapData($startDate, $endDate, $divisi);
        
        $allDates = collect();
        if ($startDate && $endDate) {
            $allDates = collect(CarbonPeriod::create($startDate, $endDate));
        }

        $pdf = PDF::loadView('admin.absensi.pdf_rekap_bulanan', compact('rekapData', 'allDates', 'startDate', 'endDate', 'divisi'));
        
        $filename = 'rekap_absensi_'.Carbon::parse($startDate)->isoFormat('MMMM_YYYY').'.pdf';
        return $pdf->download($filename);
    }
    
    /**
     * Download absensi harian sebagai PDF.
     */
    public function downloadPdfHarian(Request $request)
    {
        $month = intval($request->input('month', now()->month));
        $year = intval($request->input('year', now()->year));
        $day = intval($request->input('day', now()->day));
        $divisi = $request->input('divisi');

        $date_for_page = now()->year($year)->month($month)->day($day);

        $query = Absensi::with('user')
                         ->whereDate('tanggal', $date_for_page->format('Y-m-d'));

        if ($divisi) {
            $query->whereHas('user', function ($q) use ($divisi) {
                $q->where('divisi', $divisi);
            });
        }
        $absensi_harian = $query->get();
        
        $pdf = PDF::loadView('admin.absensi.pdf_harian', compact('absensi_harian', 'date_for_page'));
        
        $filename = 'absensi_harian_' . $date_for_page->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Download rekap absensi bulanan sebagai Excel.
     */
    public function downloadExcel(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        $divisi = $request->input('divisi');

        $rekapData = $this->getRekapData($startDate, $endDate, $divisi);

        $allDates = CarbonPeriod::create($startDate, $endDate);
        $fileName = 'rekap-absensi-' . Carbon::parse($startDate)->format('M-Y') . '.xlsx';

        return Excel::download(new RekapAbsensiExport($rekapData, $allDates, $startDate, $endDate), $fileName);
    }

    /**
     * Method private untuk mengambil dan memproses data rekapitulasi absensi.
     *
     * @param string $startDate
     * @param string $endDate
     * @param string|null $divisi
     * @return array
     */
    private function getRekapData($startDate, $endDate, $divisi)
    {
        $queryUsers = User::query();
        if ($divisi) {
            $queryUsers->where('divisi', $divisi);
        }
        
        $users = $queryUsers->where('role', 'user')
                    ->orderBy('name', 'asc')
                    ->get();

        $allDates = collect(CarbonPeriod::create($startDate, $endDate));
        $rekapData = [];
        $standardWorkHour = Carbon::createFromTime(8, 0, 0, 'Asia/Jakarta');

        foreach ($users as $user) {
            $absensiRecords = Absensi::where('user_id', $user->id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->get()->keyBy('tanggal');

            $lemburRecords = Lembur::where('user_id', $user->id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->whereNotNull('jam_keluar_lembur')
                ->get()->keyBy('tanggal');
            
            $summary = ['H' => 0, 'S' => 0, 'I' => 0, 'C' => 0, 'A' => 0, 'L' => 0, 'terlambat' => 0];
            $dailyRecords = [];

            foreach ($allDates as $date) {
                $record = $absensiRecords->get($date->toDateString());
                $lembur = $lemburRecords->get($date->toDateString());
                $status = '-'; // Default status untuk tanggal di masa depan atau hari opsional

                // --- AWAL PERUBAHAN LOGIKA ---
                if ($record) {
                    // 1. Jika ada catatan absensi, gunakan status dari catatan tersebut.
                    $status = strtoupper(substr($record->status, 0, 1));
                    if ($record->status === 'hadir') {
                        $jamMasuk = Carbon::parse($record->jam_masuk, 'Asia/Jakarta');
                        if ($jamMasuk->gt($standardWorkHour)) {
                            $summary['terlambat'] += abs($jamMasuk->diffInMinutes($standardWorkHour));
                        }
                    }
                    if($record->status == 'cuti') $status = 'C';
                    $summary[$status]++;
                } elseif ($date->isSunday()) {
                    // 2. Jika tidak ada catatan dan hari Minggu, statusnya strip.
                    $status = '-';
                } elseif ($date->isSaturday()) {
                    // 3. Jika tidak ada catatan dan hari Sabtu, statusnya strip (opsional).
                    $status = '-';
                } elseif ($date->lt(now()->startOfDay())) {
                    // 4. Jika tidak ada catatan, bukan Minggu/Sabtu, dan tanggal sudah lewat, maka dianggap Alpa.
                    $status = 'A';
                    $summary['A']++;
                }
                // --- AKHIR PERUBAHAN LOGIKA ---

                if ($lembur) $status .= ' L';
                $dailyRecords[$date->toDateString()] = $status;
            }
            
            $summary['L'] = Lembur::where('user_id', $user->id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->whereNotNull('jam_keluar_lembur')->count();

            $totalMinutes = $summary['terlambat'];
            $summary['terlambat_formatted'] = floor($totalMinutes / 60) . ' Jam ' . ($totalMinutes % 60) . ' Menit';
            
            $rekapData[] = ['user' => $user, 'daily' => $dailyRecords, 'summary' => $summary];
        }

        return $rekapData;
    }
}