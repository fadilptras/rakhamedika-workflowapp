<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Lembur;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;
use Barryvdh\DomPDF\Facade\Pdf;

class AbsensiController extends Controller
{
    /**
     * Menampilkan data absensi untuk admin dengan filter dan pagination.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Mendapatkan bulan dan tahun dari request, jika tidak ada, gunakan bulan dan tahun saat ini
        $month = intval($request->input('month', now()->month));
        $year = intval($request->input('year', now()->year));
        $day = intval($request->input('day', now()->day));
        $divisi = $request->input('divisi');
        $status = $request->input('status');

        // Mengambil semua divisi unik dari tabel users
        $divisions = User::select('divisi')->whereNotNull('divisi')->distinct()->pluck('divisi');

        // Buat tanggal awal dan akhir berdasarkan bulan dan tahun
        $start_date_month = now()->year($year)->month($month)->startOfMonth()->format('Y-m-d');
        $end_date_month = now()->year($year)->month($month)->endOfMonth()->format('Y-m-d');
        
        // Buat objek tanggal untuk hari yang dipilih
        $date_for_page = now()->year($year)->month($month)->day($day);
        
        // Perubahan: Tambahkan pengecekan apakah hari yang dipilih adalah akhir pekan
        $isWeekend = $date_for_page->isWeekend();

        // Query dasar untuk mengambil data dari model Absensi
        $query = Absensi::with('user')
                         ->whereBetween('tanggal', [$start_date_month, $end_date_month]);

        // Menambahkan filter opsional jika ada
        if ($divisi) {
            $query->whereHas('user', function ($q) use ($divisi) {
                $q->where('divisi', $divisi);
            });
        }
        
        if ($status) {
            $query->where('status', $status);
        }

        // Ambil data untuk hari tertentu (sesuai pagination)
        $absensi_harian = $query->whereDate('tanggal', $date_for_page->format('Y-m-d'))->get();

        // Data untuk dropdown filter bulan dan tahun
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
        
        // Mengambil filter dari request, atau menggunakan nilai default
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        $divisi = $request->input('divisi');
        
        // Ambil daftar semua user yang absensi dalam periode ini, atau semua user jika tidak ada filter divisi
        $queryUsers = User::query();
        if ($divisi) {
            $queryUsers->where('divisi', $divisi);
        }
        $users = $queryUsers->where('role', 'user')->get();

        $divisions = User::select('divisi')->whereNotNull('divisi')->distinct()->pluck('divisi');
        $allDates = collect();
        if ($startDate && $endDate) {
            $allDates = collect(CarbonPeriod::create($startDate, $endDate));
        }

        $rekapData = [];
        $standardWorkHour = Carbon::createFromTime(8, 0, 0, 'Asia/Jakarta');

        foreach ($users as $user) {
            $absensiRecords = Absensi::where('user_id', $user->id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->get()
                ->keyBy('tanggal');

            // Ambil juga data lembur dalam satu query
            $lemburRecords = Lembur::where('user_id', $user->id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->whereNotNull('jam_keluar_lembur')
                ->get()
                ->keyBy('tanggal');
            
            $summary = [
                'H' => 0, 'S' => 0, 'I' => 0, 'C' => 0, 'A' => 0, 'L' => 0, 'terlambat' => 0
            ];
            
            $dailyRecords = [];

            foreach ($allDates as $date) {
                $record = $absensiRecords->get($date->toDateString());
                $lembur = $lemburRecords->get($date->toDateString());

                $status = '-'; // Default: tanda hubung untuk tanggal yang belum ada absensi
                
                // Perubahan utama ada di sini: cek jika hari libur atau akhir pekan
                if ($date->isWeekend()) {
                    $status = '-';
                    // Kita tidak menambah summary untuk hari libur, karena bukan hari kerja
                } elseif ($record) {
                    $status = strtoupper(substr($record->status, 0, 1));
                    if ($record->status === 'hadir') {
                        $jamMasuk = Carbon::parse($record->jam_masuk, 'Asia/Jakarta');
                        if ($jamMasuk->gt($standardWorkHour)) {
                            $diffInMinutes = abs($jamMasuk->diffInMinutes($standardWorkHour));
                            $summary['terlambat'] += $diffInMinutes;
                        }
                    }
                    if($record->status == 'cuti') {
                       $status = 'C';
                    }
                    $summary[$status]++;
                } else {
                    // Jika tidak ada record dan tanggal sudah terlewat, hitung sebagai tidak hadir
                    if ($date->lt(now()->startOfDay())) {
                        $status = 'A';
                        $summary['A']++;
                    }
                }

                // Tambahkan 'L' jika ada lembur
                if ($lembur) {
                    $status .= ' L';
                }

                $dailyRecords[$date->toDateString()] = $status;
            }
            
            // Hitung jumlah lembur untuk user dan periode ini
            $jumlahLembur = Lembur::where('user_id', $user->id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->whereNotNull('jam_keluar_lembur')
                ->count();
            $summary['L'] = $jumlahLembur;

            // Konversi total menit terlambat ke format Jam Menit
            $totalMinutes = $summary['terlambat'];
            $hours = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;
            $summary['terlambat_formatted'] = $hours . ' Jam ' . $minutes . ' Menit';
            
            $rekapData[] = [
                'user' => $user,
                'daily' => $dailyRecords,
                'summary' => $summary,
            ];
        }

        return view('admin.absensi.rekap', compact('title', 'rekapData', 'allDates', 'divisions', 'divisi', 'startDate', 'endDate'));
    }

    /**
     * Download rekap absensi bulanan sebagai PDF.
     */
    public function downloadPdf(Request $request)
    {
        // Re-use logic dari method rekap
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        $divisi = $request->input('divisi');

        $queryUsers = User::query();
        if ($divisi) {
            $queryUsers->where('divisi', $divisi);
        }
        $users = $queryUsers->where('role', 'user')->get();
        $allDates = collect();
        if ($startDate && $endDate) {
            $allDates = collect(CarbonPeriod::create($startDate, $endDate));
        }

        $rekapData = [];
        $standardWorkHour = Carbon::createFromTime(8, 0, 0, 'Asia/Jakarta');

        foreach ($users as $user) {
            $absensiRecords = Absensi::where('user_id', $user->id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->get()
                ->keyBy('tanggal');
            
            $lemburRecords = Lembur::where('user_id', $user->id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->whereNotNull('jam_keluar_lembur')
                ->get()
                ->keyBy('tanggal');

            $summary = [
                'H' => 0, 'S' => 0, 'I' => 0, 'C' => 0, 'A' => 0, 'L' => 0, 'terlambat' => 0
            ];
            
            $dailyRecords = [];
            foreach ($allDates as $date) {
                $record = $absensiRecords->get($date->toDateString());
                $lembur = $lemburRecords->get($date->toDateString());
                $status = '-'; 
                if ($record) {
                    $status = strtoupper(substr($record->status, 0, 1));
                    if ($record->status === 'hadir') {
                        $jamMasuk = Carbon::parse($record->jam_masuk, 'Asia/Jakarta');
                        if ($jamMasuk->gt($standardWorkHour)) {
                            $diffInMinutes = abs($jamMasuk->diffInMinutes($standardWorkHour));
                            $summary['terlambat'] += $diffInMinutes;
                        }
                    }
                    if($record->status == 'cuti') {
                        $status = 'C';
                    }
                    $summary[$status]++;
                } else {
                    if ($date->lt(now()->startOfDay())) {
                        $status = 'A';
                        $summary['A']++;
                    }
                }
                
                // Tambahkan 'L' jika ada lembur
                if ($lembur) {
                    $status .= ' L';
                }
                
                $dailyRecords[$date->toDateString()] = $status;
            }
            
            // Hitung jumlah lembur untuk user dan periode ini
            $jumlahLembur = Lembur::where('user_id', $user->id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->whereNotNull('jam_keluar_lembur')
                ->count();
            $summary['L'] = $jumlahLembur;
            
            $totalMinutes = $summary['terlambat'];
            $hours = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;
            $summary['terlambat_formatted'] = $hours . ' Jam ' . $minutes . ' Menit';
            
            $rekapData[] = [
                'user' => $user,
                'daily' => $dailyRecords,
                'summary' => $summary,
            ];
        }

        // Perbaikan: Mengubah nama view yang dipanggil
        $pdf = PDF::loadView('admin.absensi.pdf_rekap_bulanan', compact('rekapData', 'allDates', 'startDate', 'endDate', 'divisi'));
        
        $filename = 'rekap_absensi_'.Carbon::parse($startDate)->isoFormat('MMMM_YYYY').'.pdf';
        return $pdf->download($filename);
    }
    
    /**
     * Download absensi harian sebagai PDF.
     */
    public function downloadPdfHarian(Request $request)
    {
        // Re-use logic dari method index
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
}