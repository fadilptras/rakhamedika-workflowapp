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
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($absensiRecords as $index => $record)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $record->user->name ?? 'User Dihapus' }}</td>
                <td>{{ \Carbon\Carbon::parse($record->tanggal)->isoFormat('D MMMM YYYY') }}</td>
                <td>{{ $record->jam_masuk }}</td>
                <td style="text-transform: capitalize;">{{ $record->status }}</td>
                <td>{{ $record->keterangan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>