<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Lembur;
use App\Models\Aktivitas; // Tambahkan Model Aktivitas
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\CarbonPeriod;
use App\Models\User;

class RekapAbsenController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Rekap Absensi Saya';
        $user = Auth::user();

        $bulanDipilih = $request->input('bulan', now()->month);
        $tahunDipilih = $request->input('tahun', now()->year);
        
        $startDate = Carbon::create($tahunDipilih, $bulanDipilih, 1)->startOfMonth();
        $endDate = Carbon::create($tahunDipilih, $bulanDipilih, 1)->endOfMonth();

        // 1. Ambil Data Absensi
        $absensiDalamPeriode = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->keyBy(fn($item) => Carbon::parse($item->tanggal)->toDateString());

        // 2. Ambil Data Cuti
        $cutiDalamPeriode = Cuti::where('user_id', $user->id)
            ->where('status', 'disetujui') 
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where('tanggal_mulai', '<=', $endDate)
                      ->where('tanggal_selesai', '>=', $startDate);
            })
            ->get();
            
        // 3. Ambil Data Lembur
        $lemburDalamPeriode = Lembur::where('user_id', $user->id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->whereNotNull('jam_keluar_lembur')
            ->get()
            ->keyBy(fn($item) => Carbon::parse($item->tanggal)->toDateString());

        // 4. Ambil Data Aktivitas (Hitung jumlahnya per hari)
        $aktivitasDalamPeriode = Aktivitas::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->selectRaw('DATE(created_at) as date, count(*) as total')
            ->groupBy('date')
            ->get()
            ->keyBy('date');
            
        // 5. Inisialisasi Rekap
        $rekap = [
            'hadir' => 0,
            'sakit' => 0,
            'izin'  => 0,
            'cuti'  => 0,
            'alpa'  => 0,
            'lembur' => 0,
            'terlambat' => 0 
        ];
        $standardWorkHour = Carbon::createFromTime(8, 0, 0, 'Asia/Jakarta');
        
        $detailHarian = [];
        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            $tanggalFormatted = $date->toDateString();
            $recordAbsensi = $absensiDalamPeriode->get($tanggalFormatted);
            $recordLembur = $lemburDalamPeriode->get($tanggalFormatted);
            $recordAktivitas = $aktivitasDalamPeriode->get($tanggalFormatted);
            
            $isOnLeave = $cutiDalamPeriode->first(function ($cuti) use ($date) {
                return $date->between(Carbon::parse($cuti->tanggal_mulai), Carbon::parse($cuti->tanggal_selesai));
            });
            
            $dailyData = [
                'tanggal' => $date,
                'status' => '-',
                'jam_masuk' => null,
                'jam_keluar' => null,
                'keterangan' => $date->isWeekend() ? 'Akhir Pekan' : null,
                'is_weekend' => $date->isWeekend(),
                'jumlah_aktivitas' => $recordAktivitas ? $recordAktivitas->total : 0 // Data untuk fitur baru
            ];

            if ($date->isWeekend()) {
                // Weekend logic handled below for Overtime
            } elseif ($isOnLeave) {
                $rekap['cuti']++;
                $dailyData['status'] = 'cuti';
                $dailyData['keterangan'] = 'Cuti Tahunan';
            } elseif ($recordAbsensi) {
                $status = strtolower($recordAbsensi->status);
                if (array_key_exists($status, $rekap)) {
                    $rekap[$status]++;
                }
                
                $dailyData['status'] = $recordAbsensi->status;
                $dailyData['jam_masuk'] = $recordAbsensi->jam_masuk;
                $dailyData['jam_keluar'] = $recordAbsensi->jam_keluar;
                $dailyData['keterangan'] = $recordAbsensi->keterangan;
                
                // Hitung Keterlambatan (Sesuai AbsenController)
                if ($recordAbsensi->status === 'hadir' && $recordAbsensi->jam_masuk) {
                    $jamMasuk = Carbon::parse($recordAbsensi->jam_masuk, 'Asia/Jakarta');
                    // Reset detik ke 0 agar adil (opsional, tergantung kebijakan)
                    // $jamMasuk->second(0); 
                    
                    if ($jamMasuk->gt($standardWorkHour)) {
                        $diffInMinutes = abs($jamMasuk->diffInMinutes($standardWorkHour));
                        $rekap['terlambat'] += $diffInMinutes;
                    }
                }
            } else {
                if ($date->isPast() && !$date->isToday()) {
                    $rekap['alpa']++;
                    $dailyData['status'] = 'alpa';
                    $dailyData['keterangan'] = 'Tidak ada keterangan';
                } else {
                    $dailyData['keterangan'] = 'Belum ada data';
                }
            }

            // Logika Lembur (Bisa terjadi di Weekend atau Hari Kerja)
            if ($recordLembur) {
                $rekap['lembur']++;
                
                if ($date->isWeekend()) {
                    $dailyData['status'] = 'lembur';
                    $dailyData['keterangan'] = $recordLembur->keterangan ?: 'Lembur Akhir Pekan';
                    $dailyData['jam_masuk'] = $recordLembur->jam_masuk_lembur;
                    $dailyData['jam_keluar'] = $recordLembur->jam_keluar_lembur;
                } else {
                    // Append keterangan lembur jika hari kerja
                    $ketAwal = $dailyData['keterangan'] ? $dailyData['keterangan'] . '. ' : '';
                    $dailyData['keterangan'] = $ketAwal . '(Lembur: ' . \Carbon\Carbon::parse($recordLembur->jam_masuk_lembur)->format('H:i') . ' - ' . \Carbon\Carbon::parse($recordLembur->jam_keluar_lembur)->format('H:i') . ')';
                }
            }
            
            $detailHarian[] = (object)$dailyData;
        }
        
        // Format Tampilan Terlambat
        $totalMenitTerlambat = $rekap['terlambat'];
        $jamTerlambat = floor($totalMenitTerlambat / 60);
        $menitTerlambat = $totalMenitTerlambat % 60;
        $rekap['terlambat_formatted'] = $jamTerlambat . ' Jam ' . $menitTerlambat . ' Menit';

        $daftarBulan = collect(range(1, 12))->mapWithKeys(function ($bulan) {
            return [$bulan => Carbon::create()->month($bulan)->translatedFormat('F')];
        });
        $daftarTahun = range(now()->year, now()->year - 5);

        return view('users.rekap_absen', compact(
            'title',
            'detailHarian',
            'rekap',
            'bulanDipilih',
            'tahunDipilih',
            'daftarBulan',
            'daftarTahun'
        ));
    }
}