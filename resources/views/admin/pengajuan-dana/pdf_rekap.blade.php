<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Pengajuan Dana</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #333; }
        .container { width: 100%; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 25px; }
        .header h1 { margin: 0; font-size: 22px; color: #003366; }
        .header p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .filter-info {
            border: 1px solid #e3e3e3;
            background-color: #f9f9f9;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 11px;
        }
        .text-right { text-align: right; }
        .total-row td { font-weight: bold; background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PT RAKHA NUSANTARA MEDIKA</h1>
            <p>Rekap Laporan Pengajuan Dana</p>
        </div>

        <div class="filter-info">
            <strong>Filter Data:</strong><br>
            - Nama Karyawan: <strong>{{ $karyawanName }}</strong> <br>
            - Divisi: <strong>{{ $divisiName }}</strong> <br>
            - Periode: <strong>{{ $startDate ? \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') : 'Awal' }}</strong> s/d <strong>{{ $endDate ? \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') : 'Akhir' }}</strong>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">Tanggal</th>
                    <th>Nama Karyawan</th>
                    <th>Judul Pengajuan</th>
                    <th style="width: 13%;">Status</th>
                    <th style="width: 18%;" class="text-right">Total Dana</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grandTotal = 0;
                @endphp
                @forelse($pengajuanDana as $pengajuan)
                    <tr>
                        <td>{{ $pengajuan->created_at->format('d M Y') }}</td>
                        <td>{{ $pengajuan->user->name }}</td>
                        <td>{{ $pengajuan->judul_pengajuan }}</td>
                        <td>{{ ucfirst($pengajuan->status) }}</td>
                        <td class="text-right">Rp {{ number_format($pengajuan->total_dana, 0, ',', '.') }}</td>
                    </tr>
                    @php
                        // Hanya total dana yang disetujui atau masih diproses yang dihitung
                        if (in_array($pengajuan->status, ['disetujui', 'diproses'])) {
                            $grandTotal += $pengajuan->total_dana;
                        }
                    @endphp
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px;">
                            Tidak ada data untuk ditampilkan berdasarkan filter yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" class="text-right">GRAND TOTAL (Hanya status Disetujui & Diproses)</td>
                    <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>