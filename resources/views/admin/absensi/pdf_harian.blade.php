<!DOCTYPE html>
<html>
<head>
    <title>Laporan Absensi Harian</title>
    <style>
        @page {
            size: landscape;
            margin: 10mm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #333;
        }
        
        /* HEADER DOKUMEN (KOP) */
        .header-doc {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #1e3a8a;
            padding-bottom: 10px;
        }
        .header-doc h1 { 
            margin: 0; 
            font-size: 22px; 
            text-transform: uppercase; 
            color: #1e3a8a;
            font-weight: 800;
        }
        .header-doc p { 
            margin: 2px 0; 
            font-size: 11px;
            color: #555; 
        }

        /* TABEL UTAMA */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background-color: white;
            table-layout: fixed;
        }
        
        /* HEADER TABEL */
        th {
            background-color: #1e3a8a;
            color: white;
            padding: 10px 6px;
            text-transform: uppercase;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid #1e3a8a;
            letter-spacing: 0.5px;
            vertical-align: middle;
        }

        td {
            border: 1px solid #e5e7eb;
            padding: 8px 6px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        /* Zebra Striping */
        tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        /* PENGATURAN LEBAR KOLOM */
        .col-no { width: 5%; text-align: center; }
        .col-nama { width: 25%; } 
        .col-waktu { width: 12%; text-align: center; }
        .col-durasi { width: 10%; text-align: center; }
        .col-status { width: 15%; text-align: center; }
        .col-lembur { width: 8%; text-align: center; }
        .col-ket { width: 20%; }
        
        /* TYPOGRAPHY */
        .name-main { font-weight: bold; font-size: 11px; color: #1e3a8a; margin-bottom: 2px; }
        .name-sub { font-size: 9px; color: #64748b; }
        
        .date-text { font-weight: bold; font-size: 9px; color: #334155; display: block; margin-bottom: 2px; }
        .time-text { font-size: 10px; color: #0f172a; font-weight: bold; }

        /* STATUS BADGES */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 8px;
            text-transform: capitalize;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .bg-green { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .bg-red { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .bg-amber { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .bg-purple { background-color: #f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff; }
        .bg-gray { background-color: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }

    </style>
</head>
<body>
    <div class="header-doc">
        <h1>Laporan Absensi Harian</h1>
        <p>PT RAKHA NUSANTARA MEDIKA</p>
        <p>Tanggal Laporan: <strong>{{ \Carbon\Carbon::parse($date_for_page)->isoFormat('dddd, D MMMM YYYY') }}</strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-nama" style="text-align: left; padding-left: 10px;">Karyawan</th>
                <th class="col-waktu">Waktu Masuk</th>
                <th class="col-waktu">Waktu Keluar</th>
                <th class="col-durasi">Durasi</th>
                <th class="col-status">Status</th>
                <th class="col-lembur">Lembur</th>
                <th class="col-ket">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($absensi_harian as $index => $record)
                @php
                    $jamMasuk = $record->jam_masuk ? \Carbon\Carbon::parse($record->jam_masuk) : null;
                    $jamKeluar = $record->jam_keluar ? \Carbon\Carbon::parse($record->jam_keluar) : null;
                    
                    // --- PERBAIKAN: HITUNG DURASI MANUAL ---
                    $durasiKerja = '-';
                    if ($jamMasuk && $jamKeluar) {
                        // Hitung selisih waktu
                        $diff = $jamMasuk->diff($jamKeluar);
                        // Format output: X Jam Y Menit
                        $durasiKerja = $diff->format('%h Jam %i Menit');
                    }
                    // ----------------------------------------

                    // --- STATUS LOGIC ---
                    $statusText = ucfirst($record->status);
                    $badgeClass = 'bg-gray';

                    if ($record->status == 'hadir') {
                        $batasWaktu = '08:00:00';
                        $waktuMasukStr = $jamMasuk ? $jamMasuk->format('H:i:s') : '00:00:00';
                        
                        if ($jamMasuk && $waktuMasukStr > $batasWaktu) {
                            $statusText = 'Hadir (Terlambat)'; // Teks sesuai request
                            $badgeClass = 'bg-green';
                        } else {
                            $statusText = 'Hadir';
                            $badgeClass = 'bg-green';
                        }
                    } elseif ($record->status == 'sakit') {
                        $badgeClass = 'bg-red';
                    } elseif ($record->status == 'izin') {
                        $badgeClass = 'bg-amber';
                    } elseif ($record->status == 'cuti') {
                        $badgeClass = 'bg-purple';
                    } elseif ($record->status == 'tidak hadir') {
                        $badgeClass = 'bg-gray';
                    }
                    
                    $statusLembur = $record->lembur ? 'Ya' : 'Tidak';
                    $lemburClass = $record->lembur ? 'bg-purple' : 'bg-gray';

                    // Format Tanggal
                    $tglMasuk = \Carbon\Carbon::parse($record->tanggal)->isoFormat('D MMM Y');
                    $tglKeluar = $record->tanggal_keluar 
                                ? \Carbon\Carbon::parse($record->tanggal_keluar)->isoFormat('D MMM Y') 
                                : $tglMasuk;
                @endphp
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="padding-left: 10px;">
                    <div class="name-main">{{ $record->user->name ?? 'User Dihapus' }}</div>
                    <div class="name-sub">{{ $record->user->divisi ?? '-' }}</div>
                </td>
                <td style="text-align: center;">
                    @if($jamMasuk)
                        <span class="date-text">{{ $tglMasuk }}</span>
                        <span class="time-text">{{ $jamMasuk->format('H:i') }} WIB</span>
                    @else
                        -
                    @endif
                </td>
                <td style="text-align: center;">
                    @if($jamKeluar)
                        <span class="date-text">{{ $tglKeluar }}</span>
                        <span class="time-text">{{ $jamKeluar->format('H:i') }} WIB</span>
                    @else
                        -
                    @endif
                </td>
                <td style="text-align: center; font-weight: bold; color: #444;">
                    {{ $durasiKerja }}
                </td>
                <td style="text-align: center;">
                    <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                </td>
                <td style="text-align: center;">
                    <span class="badge {{ $lemburClass }}">{{ $statusLembur }}</span>
                </td>
                <td>{{ $record->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 20px; font-style: italic; color: #666;">
                    Tidak ada data absensi untuk tanggal ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="margin-top: 20px; text-align: right; font-size: 8px; color: #888;">
        Dicetak otomatis oleh sistem pada: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>