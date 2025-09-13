<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use DateInterval;
use DatePeriod;

class AbsensiController extends Controller
{
    /**
     * halaman rekap absensi seluruh karyawan
     */
    public function index(Request $request)
    {
        $title = 'Rekap Absensi Karyawan';
        $standardWorkHour = '08:00:00';
        $notPresentThresholdMinutes = 180; // Batas jam 11:00 WIB (3 jam dari jam 8:00)

        // Tentukan rentang tanggal berdasarkan filter.
        // Jika tidak ada filter yang spesifik, gunakan bulan ini sebagai default.
        if ($request->filled('tanggal')) {
            $startDate = Carbon::parse($request->tanggal)->startOfDay();
            $endDate = Carbon::parse($request->tanggal)->endOfDay();
        } elseif ($request->filled('filter_rentang') && $request->filter_rentang !== 'semua') {
            switch ($request->filter_rentang) {
                case 'minggu_ini':
                    $startDate = now()->startOfWeek();
                    $endDate = now()->endOfWeek();
                    break;
                case 'bulan_ini':
                    $startDate = now()->startOfMonth();
                    $endDate = now()->endOfMonth();
                    break;
            }
        } elseif ($request->filled('bulan') && $request->filled('tahun')) {
            $startDate = Carbon::createFromDate($request->tahun, $request->bulan, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($request->tahun, $request->bulan, 1)->endOfMonth();
        } else {
            // Default: Gunakan bulan ini jika tidak ada filter waktu
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
        }
        

        // Ambil semua karyawan yang relevan
        $userQuery = User::where('role', 'user');
        if ($request->filled('divisi')) {
            $userQuery->where('divisi', $request->divisi);
        }
        if ($request->filled('user_id')) {
            $userQuery->where('id', $request->user_id);
        }
        $users = $userQuery->orderBy('name')->get();

        // Ambil semua data absensi dalam rentang waktu yang ditentukan
        $absensiQuery = Absensi::with('user');
        if ($startDate && $endDate) {
            $absensiQuery->whereBetween('tanggal', [$startDate, $endDate]);
        }
        $absensiRecords = $absensiQuery->get()->keyBy(function ($item) {
            return $item->user_id . '-' . $item->tanggal;
        });

        $finalRecords = collect();
        $totalLateMinutes = 0;
        $notPresentAfter = Carbon::parse($standardWorkHour)->addMinutes($notPresentThresholdMinutes);

        // Iterasi setiap hari dalam rentang dan setiap user
        if ($startDate && $endDate) {
            $period = new DatePeriod($startDate, new DateInterval('P1D'), $endDate->copy()->addDay());
            foreach ($period as $date) {
                // Perbaikan: Ubah objek DateTime menjadi Carbon sebelum memanggil isFuture()
                $carbonDate = Carbon::instance($date);
                if ($carbonDate->isFuture()) {
                    continue;
                }

                foreach ($users as $user) {
                    $key = $user->id . '-' . $carbonDate->toDateString();
                    $record = $absensiRecords->get($key);

                    // LOGIKA BARU: Kategorikan sebagai "Tidak Hadir" jika tidak ada record
                    if (!$record) {
                        // Hanya tambahkan jika hari ini sudah lewat jam 11:00 atau jika harinya sudah lewat
                        if ($carbonDate->isPast() || (today()->isSameDay($carbonDate) && now()->greaterThan($notPresentAfter))) {
                            $record = (object)[
                                'user_id' => $user->id,
                                'user' => $user,
                                'tanggal' => $carbonDate->toDateString(),
                                'jam_masuk' => null,
                                'jam_keluar' => null,
                                'status' => 'tidak_hadir',
                                'keterangan' => 'Tidak hadir dan tidak ada absensi tercatat.',
                                'lampiran' => null,
                                'lampiran_keluar' => null,
                                'latitude' => null,
                                'longitude' => null,
                                'latitude_keluar' => null,
                                'longitude_keluar' => null,
                            ];
                            $finalRecords->push($record);
                        }
                    } else {
                        // LOGIKA BARU: Jika terlambat lebih dari 3 jam, ubah status menjadi tidak hadir
                        if ($record->status == 'hadir' && Carbon::parse($record->jam_masuk)->greaterThan($notPresentAfter)) {
                            $record->status = 'tidak_hadir';
                            $record->keterangan = 'Terlambat lebih dari 3 jam dari jam masuk standar.';
                        }

                        // Hitung keterlambatan untuk yang tidak terlalu telat
                        if ($record->status == 'hadir' && Carbon::parse($record->jam_masuk)->greaterThan(Carbon::parse($standardWorkHour)) && Carbon::parse($record->jam_masuk)->lessThanOrEqualTo($notPresentAfter)) {
                            $jamMasuk = Carbon::parse($record->jam_masuk);
                            $jamMulaiKerja = Carbon::parse($record->tanggal . ' ' . $standardWorkHour);
                            $minutesLate = $jamMasuk->diffInMinutes($jamMulaiKerja);
                            $totalLateMinutes += $minutesLate;
                            $record->isLate = true;
                        } else {
                            $record->isLate = false;
                        }
                        $finalRecords->push($record);
                    }
                }
            }
        }
        
        // Filter berdasarkan status
        if ($request->filled('status') && in_array($request->status, ['hadir', 'sakit', 'izin', 'cuti'])) {
            $finalRecords = $finalRecords->where('status', $request->status);
        } elseif ($request->status == 'terlambat') {
            $finalRecords = $finalRecords->filter(fn ($record) => property_exists($record, 'isLate') && $record->isLate);
        } elseif ($request->status == 'tidak_hadir') {
            $finalRecords = $finalRecords->where('status', 'tidak_hadir');
        }

        // Terapkan paginasi pada koleksi hasil akhir
        $perPage = 15;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $pagedData = $finalRecords->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $absensiRecords = new LengthAwarePaginator($pagedData, count($finalRecords), $perPage, $currentPage, ['path' => $request->url(), 'query' => $request->query()]);

        $totalLate = CarbonInterval::minutes($totalLateMinutes)->cascade()->forHumans();

        $months = array_map(fn ($m) => Carbon::create()->month($m)->translatedFormat('F'), range(1, 12));
        $years = range(now()->year, now()->year - 5);
        $divisions = User::select('divisi')->whereNotNull('divisi')->distinct()->pluck('divisi');
        $allUsers = User::where('role', 'user')->orderBy('name')->get();

        return view('admin.absensi.index', compact('title', 'absensiRecords', 'months', 'years', 'divisions', 'allUsers', 'totalLate', 'standardWorkHour'));
    }

    /**
     * Method untuk download PDF.
     */
    public function downloadPDF(Request $request)
    {
        $standardWorkHour = '08:00:00';
        $notPresentThresholdMinutes = 180;
        $periode = "Semua Waktu";

        // Tentukan rentang tanggal berdasarkan filter.
        // Jika tidak ada filter yang spesifik, gunakan bulan ini sebagai default.
        if ($request->filled('tanggal')) {
            $startDate = Carbon::parse($request->tanggal)->startOfDay();
            $endDate = Carbon::parse($request->tanggal)->endOfDay();
            $periode = Carbon::parse($request->tanggal)->isoFormat('D MMMM YYYY');
        } elseif ($request->filled('filter_rentang') && $request->filter_rentang !== 'semua') {
            $rentang = $request->filter_rentang;
            if ($rentang == 'minggu_ini') {
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                $periode = "Minggu Ini";
            } elseif ($rentang == 'bulan_ini') {
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                $periode = "Bulan " . Carbon::now()->isoFormat('MMMM YYYY');
            }
        } elseif ($request->filled('bulan') && $request->filled('tahun')) {
            $startDate = Carbon::createFromDate($request->tahun, $request->bulan, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($request->tahun, $request->bulan, 1)->endOfMonth();
            $periode = "Bulan " . Carbon::create()->month($request->bulan)->isoFormat('MMMM') . " " . $request->tahun;
        } else {
             // Default: Gunakan bulan ini jika tidak ada filter waktu
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
            $periode = "Bulan " . Carbon::now()->isoFormat('MMMM YYYY');
        }

        // Ambil semua karyawan yang relevan
        $userQuery = User::where('role', 'user');
        if ($request->filled('divisi')) {
            $userQuery->where('divisi', $request->divisi);
        }
        if ($request->filled('user_id')) {
            $userQuery->where('id', $request->user_id);
        }
        $users = $userQuery->orderBy('name')->get();

        // Ambil semua data absensi dalam rentang waktu yang ditentukan
        $absensiQuery = Absensi::with('user');
        if ($startDate && $endDate) {
            $absensiQuery->whereBetween('tanggal', [$startDate, $endDate]);
        }
        $absensiRecords = $absensiQuery->get()->keyBy(function ($item) {
            return $item->user_id . '-' . $item->tanggal;
        });

        $finalRecords = collect();
        $notPresentAfter = Carbon::parse($standardWorkHour)->addMinutes($notPresentThresholdMinutes);

        // Iterasi setiap hari dalam rentang dan setiap user
        if ($startDate && $endDate) {
            $period = new DatePeriod($startDate, new DateInterval('P1D'), $endDate->copy()->addDay());
            foreach ($period as $date) {
                // Perbaikan: Ubah objek DateTime menjadi Carbon sebelum memanggil isFuture()
                $carbonDate = Carbon::instance($date);
                if ($carbonDate->isFuture()) {
                    continue;
                }

                foreach ($users as $user) {
                    $key = $user->id . '-' . $carbonDate->toDateString();
                    $record = $absensiRecords->get($key);

                    if (!$record) {
                        if ($carbonDate->isPast() || (today()->isSameDay($carbonDate) && now()->greaterThan($notPresentAfter))) {
                            $record = (object)[
                                'user_id' => $user->id,
                                'user' => $user,
                                'tanggal' => $carbonDate->toDateString(),
                                'jam_masuk' => null,
                                'status' => 'tidak_hadir',
                                'keterangan' => 'Tidak hadir dan tidak ada absensi tercatat.',
                            ];
                            $finalRecords->push($record);
                        }
                    } else {
                        if ($record->status == 'hadir' && Carbon::parse($record->jam_masuk)->greaterThan($notPresentAfter)) {
                            $record->status = 'tidak_hadir';
                            $record->keterangan = 'Terlambat lebih dari 3 jam dari jam masuk standar.';
                        }

                        if ($record->status == 'hadir' && Carbon::parse($record->jam_masuk)->greaterThan(Carbon::parse($standardWorkHour)) && Carbon::parse($record->jam_masuk)->lessThanOrEqualTo($notPresentAfter)) {
                            $record->isLate = true;
                        } else {
                            $record->isLate = false;
                        }
                        $finalRecords->push($record);
                    }
                }
            }
        }
        
        // Filter PDF berdasarkan status
        if ($request->filled('status') && in_array($request->status, ['hadir', 'sakit', 'izin', 'cuti'])) {
            $finalRecords = $finalRecords->where('status', $request->status);
        } elseif ($request->status == 'terlambat') {
            $finalRecords = $finalRecords->filter(fn($record) => property_exists($record, 'isLate') && $record->isLate);
        } elseif ($request->status == 'tidak_hadir') {
            $finalRecords = $finalRecords->where('status', 'tidak_hadir');
        }

        $pdf = Pdf::loadView('admin.absensi.pdf', [
            'absensiRecords' => $finalRecords,
            'periode' => $periode,
            'standardWorkHour' => $standardWorkHour,
            'lateThresholdMinutes' => $notPresentThresholdMinutes,
        ]);

        return $pdf->download('rekap-absensi-' . now()->format('Y-m-d') . '.pdf');
    }
}