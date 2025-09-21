<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Cuti;
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

        // 1. Ambil filter dari request, atau gunakan nilai default (bulan & tahun sekarang)
        $bulanDipilih = $request->input('bulan', now()->month);
        $tahunDipilih = $request->input('tahun', now()->year);
        
        $startDate = Carbon::create($tahunDipilih, $bulanDipilih, 1)->startOfMonth();
        $endDate = Carbon::create($tahunDipilih, $bulanDipilih, 1)->endOfMonth();

        // 2. Ambil semua data yang relevan dalam satu kali panggilan ke database
        $absensiDalamPeriode = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->keyBy(fn($item) => Carbon::parse($item->tanggal)->toDateString());

        $cutiDalamPeriode = Cuti::where('user_id', $user->id)
            ->where('status', 'disetujui') // Pastikan hanya cuti yang disetujui
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where('tanggal_mulai', '<=', $endDate)
                      ->where('tanggal_selesai', '>=', $startDate);
            })
            ->get();
            
        // 3. Inisialisasi rekapitulasi dan standar jam kerja
        $rekap = [
            'hadir' => 0,
            'sakit' => 0,
            'izin'  => 0,
            'cuti'  => 0,
            'alpa'  => 0,
            'terlambat' => 0 // Dalam menit
        ];
        $standardWorkHour = Carbon::createFromTime(8, 0, 0, 'Asia/Jakarta');
        
        // ======================= PERUBAHAN UTAMA DI SINI =======================
        // 4. Buat array baru untuk menampung detail setiap hari
        $detailHarian = [];
        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            $tanggalFormatted = $date->toDateString();
            $recordAbsensi = $absensiDalamPeriode->get($tanggalFormatted);
            
            $isOnLeave = $cutiDalamPeriode->first(function ($cuti) use ($date) {
                return $date->between(Carbon::parse($cuti->tanggal_mulai), Carbon::parse($cuti->tanggal_selesai));
            });
            
            $dailyData = [
                'tanggal' => $date,
                'status' => '-',
                'jam_masuk' => null,
                'jam_keluar' => null,
                'keterangan' => $date->isWeekend() ? 'Akhir Pekan' : null,
                'is_weekend' => $date->isWeekend()
            ];

            if ($date->isWeekend()) {
                // Biarkan status default untuk akhir pekan
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
                
                if ($recordAbsensi->status === 'hadir' && $recordAbsensi->jam_masuk) {
                    $jamMasuk = Carbon::parse($recordAbsensi->jam_masuk, 'Asia/Jakarta');
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
            $detailHarian[] = (object)$dailyData;
        }
        // ===================== AKHIR PERUBAHAN UTAMA =====================
        
        // 5. Format total menit terlambat ke format "Jam Menit"
        $totalMenitTerlambat = $rekap['terlambat'];
        $jamTerlambat = floor($totalMenitTerlambat / 60);
        $menitTerlambat = $totalMenitTerlambat % 60;
        $rekap['terlambat_formatted'] = $jamTerlambat . ' Jam ' . $menitTerlambat . ' Menit';

        // 6. Siapkan data untuk dropdown filter
        $daftarBulan = collect(range(1, 12))->mapWithKeys(function ($bulan) {
            return [$bulan => Carbon::create()->month($bulan)->translatedFormat('F')];
        });
        $daftarTahun = range(now()->year, now()->year - 5);

        return view('users.rekap_absen', compact(
            'title',
            'detailHarian', // Mengirim data harian yang sudah lengkap
            'rekap',
            'bulanDipilih',
            'tahunDipilih',
            'daftarBulan',
            'daftarTahun'
        ));
    }
}