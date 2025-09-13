<!DOCTYPE html>
<html>
<head>
    <title>Rekap Absensi</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #dddddd; text-align: left; padding: 8px; font-size: 12px;}
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; }
        .header p { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Rekap Absensi Karyawan</h1>
        <p>PT RAKHA MEDIKA NUSANTARA</p>
        <p>Periode: {{ $periode }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Karyawan</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Telat</th>
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($absensiRecords as $index => $record)
            @php
                // Logika untuk menampilkan status dan badge
                $statusText = 'Belum Absen';
                $jamTelat = '-';
                if ($record->status == 'hadir') {
                     if (property_exists($record, 'isLate') && $record->isLate) {
                         $statusText = 'Terlambat';
                         $jamMasuk = \Carbon\Carbon::parse($record->tanggal . ' ' . $record->jam_masuk);
                         $jamMulaiKerja = \Carbon\Carbon::parse($record->tanggal . ' ' . $standardWorkHour);
                         $telatMenit = $jamMasuk->diffInMinutes($jamMulaiKerja);
                         $jamTelat = \Carbon\CarbonInterval::minutes($telatMenit)->cascade()->forHumans(['short' => true]);
                     } else {
                         $statusText = 'Hadir';
                     }
                } elseif ($record->status == 'sakit') {
                    $statusText = 'Sakit';
                } elseif ($record->status == 'izin') {
                    $statusText = 'Izin';
                } elseif ($record->status == 'cuti') {
                    $statusText = 'Cuti';
                } elseif ($record->status == 'tidak_hadir') {
                     $statusText = 'Tidak Hadir';
                }
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $record->user->name ?? 'User Dihapus' }}</td>
                <td>{{ \Carbon\Carbon::parse($record->tanggal)->isoFormat('D MMMM YYYY') }}</td>
                <td>{{ $record->jam_masuk ?? '-' }}</td>
                <td>{{ $jamTelat }}</td>
                <td style="text-transform: capitalize;">{{ $statusText }}</td>
                <td>{{ $record->keterangan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>