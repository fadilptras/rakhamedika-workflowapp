<!DOCTYPE html>
<html>
<head>
    <title>Laporan Lembur Karyawan</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #dddddd; text-align: left; padding: 6px; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Lembur Karyawan</h1>
        <p>PT RAKHA MEDIKA NUSANTARA</p>
        <p>Periode: {{ $dateForDays->isoFormat('MMMM YYYY') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Karyawan</th>
                <th>Tanggal</th>
                <th>Waktu Mulai</th>
                <th>Waktu Selesai</th>
                <th>Durasi Lembur</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($lemburRecords as $index => $record)
                @php
                    $jamMasuk = $record->jam_masuk_lembur ? \Carbon\Carbon::parse($record->jam_masuk_lembur) : null;
                    $jamKeluar = $record->jam_keluar_lembur ? \Carbon\Carbon::parse($record->jam_keluar_lembur) : null;
                    $durasiLembur = '-';

                    if ($jamMasuk && $jamKeluar) {
                        $diff = $jamKeluar->diff($jamMasuk);
                        $durasiLembur = "{$diff->h} jam {$diff->i} menit";
                    }
                @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $record->user->name ?? 'User Dihapus' }}</td>
                <td>{{ \Carbon\Carbon::parse($record->tanggal)->isoFormat('D MMMM YYYY') }}</td>
                <td>{{ $jamMasuk ? $jamMasuk->format('H:i') : '-' }}</td>
                <td>{{ $jamKeluar ? $jamKeluar->format('H:i') : '-' }}</td>
                <td>{{ $durasiLembur }}</td>
                <td>{{ $record->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">Tidak ada data lembur yang tersedia.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>