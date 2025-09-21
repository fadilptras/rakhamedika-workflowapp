<!DOCTYPE html>
<html>
<head>
    <title>Laporan Absensi Harian</title>
    <style>
        @page {
            size: landscape;
        }
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #dddddd; text-align: left; padding: 6px; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; font-size: 12px; }
        .status-badge {
            padding: 2px 5px;
            border-radius: 10px;
            color: white;
            font-weight: bold;
            font-size: 8px;
        }
        .status-hadir { background-color: #10B981; }
        .status-terlambat { background-color: #F59E0B; }
        .status-sakit { background-color: #EF4444; }
        .status-izin { background-color: #3B82F6; }
        .status-cuti { background-color: #8B5CF6; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Absensi Harian Karyawan</h1>
        <p>PT RAKHA MEDIKA NUSANTARA</p>
        <p>Tanggal: {{ \Carbon\Carbon::parse($date_for_page)->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Karyawan</th>
                <th>Jam Masuk</th>
                <th>Jam Keluar</th>
                <th>Durasi Kerja</th>
                <th>Status</th>
                <th>Keterangan</th>
                <th>Lembur</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($absensi_harian as $index => $record)
                @php
                    $statusText = 'Tidak Hadir';
                    $statusBadgeClass = 'status-tidak-hadir';
                    $durasiKerja = '-';

                    $jamMasuk = $record->jam_masuk ? \Carbon\Carbon::parse($record->jam_masuk) : null;
                    $jamKeluar = $record->jam_keluar ? \Carbon\Carbon::parse($record->jam_keluar) : null;
                    $lemburHariIni = App\Models\Lembur::where('user_id', $record->user_id)->where('tanggal', $record->tanggal)->whereNotNull('jam_keluar_lembur')->first();
                    $statusLembur = $lemburHariIni ? 'Ya' : 'Tidak';

                    if ($jamMasuk && $jamKeluar) {
                        $diff = $jamKeluar->diff($jamMasuk);
                        $durasiKerja = "{$diff->h}j {$diff->i}m";
                    }

                    if ($record->status == 'hadir') {
                        $isLate = $jamMasuk && $jamMasuk->gt(\Carbon\Carbon::createFromTimeString('08:00:00'));
                        $statusText = $isLate ? 'Hadir (Terlambat)' : 'Hadir';
                        $statusBadgeClass = $isLate ? 'status-terlambat' : 'status-hadir';
                    } elseif ($record->status == 'sakit') {
                        $statusText = 'Sakit';
                        $statusBadgeClass = 'status-sakit';
                    } elseif ($record->status == 'izin') {
                        $statusText = 'Izin';
                        $statusBadgeClass = 'status-izin';
                    } elseif ($record->status == 'cuti') {
                        $statusText = 'Cuti';
                        $statusBadgeClass = 'status-cuti';
                    }
                @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <p>{{ $record->user->name ?? 'User Dihapus' }}</p>
                    <p style="font-size: 8px; color: #666;">{{ $record->user->divisi ?? '-' }}</p>
                </td>
                <td>{{ $record->jam_masuk ? \Carbon\Carbon::parse($record->jam_masuk)->format('H:i') : '-' }}</td>
                <td>{{ $record->jam_keluar ? \Carbon\Carbon::parse($record->jam_keluar)->format('H:i') : '-' }}</td>
                <td>{{ $durasiKerja }}</td>
                <td><span class="status-badge {{ $statusBadgeClass }}">{{ $statusText }}</span></td>
                <td>{{ $record->keterangan ?? '-' }}</td>
                <td>{{ $statusLembur }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center;">Tidak ada data absensi untuk tanggal ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>