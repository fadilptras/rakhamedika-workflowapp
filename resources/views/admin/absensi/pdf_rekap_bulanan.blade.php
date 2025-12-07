<!DOCTYPE html>
<html>
<head>
    <title>Rekap Absensi Bulanan</title>
    <style>
        @page {
            size: landscape;
            margin: 10mm 5mm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8px;
            color: #333;
        }

        /* --- HEADER DOKUMEN --- */
        .header-doc {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px solid #1e3a8a;
            padding-bottom: 10px;
        }
        .header-doc h1 { 
            margin: 0; 
            font-size: 18px; 
            text-transform: uppercase; 
            color: #1e3a8a; 
            font-weight: 800;
        }
        .header-doc p { 
            margin: 2px 0; 
            font-size: 10px; 
            color: #555; 
        }

        /* --- TABEL --- */
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 10px;
        }

        table.main-table th, table.main-table td {
            border: 1px solid #d1d5db;
            padding: 0; /* Padding 0 agar status bertumpuk rapi */
            vertical-align: middle;
            text-align: center;
            word-wrap: break-word;
        }
        
        /* Padding khusus untuk sel yang bukan status (Nama, No, Summary) */
        table.main-table td.pad-normal {
            padding: 4px 2px;
        }

        /* HEADER TABEL */
        table.main-table thead th {
            background-color: #1e3a8a;
            color: white;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
            padding: 4px 2px;
        }

        table.main-table thead tr:nth-child(2) th {
            background-color: #2563eb;
        }

        .th-minggu { background-color: #dc2626 !important; color: white !important; }
        .th-sabtu { background-color: #64748b !important; color: white !important; }

        /* UKURAN KOLOM */
        .col-karyawan { width: 15%; text-align: left; padding-left: 5px; }
        .col-date { width: 16px; } /* Sedikit diperlebar agar tumpukan muat */
        .col-late { width: 10%; }
        .col-sum { width: auto; font-weight: bold; background-color: #eff6ff; }

        /* ISI TABEL */
        table.main-table tbody tr:nth-child(even) { background-color: #f8fafc; }
        
        /* Typography Nama & Jabatan (RATA KIRI) */
        .name-text { 
            font-weight: bold; 
            color: #1e3a8a; 
            display: block; 
            text-align: left; /* Pastikan rata kiri */
        }
        .jabatan-text {
            display: block;
            text-align: left; /* Pastikan rata kiri */
            color: #64748b;
            font-style: italic;
            margin-top: 2px;
        }
        
        /* Layout Status Bertumpuk (H/L) */
        .status-stack {
            display: block;
            width: 100%;
        }
        .status-top {
            display: block;
            width: 100%;
            border-bottom: 1px solid #e5e7eb; /* Garis pemisah */
            padding: 2px 0;
            line-height: 1;
        }
        .status-bottom {
            display: block;
            width: 100%;
            padding: 2px 0;
            line-height: 1;
        }
        .status-single {
            display: block;
            padding: 5px 0; /* Padding vertikal agar teks di tengah */
        }

        /* Warna Status */
        .st-h { color: #166534; font-weight: 900; }
        .st-s { color: #dc2626; font-weight: bold; }
        .st-i { color: #d97706; font-weight: bold; }
        .st-c { color: #2563eb; font-weight: bold; }
        .st-a { color: #374151; font-weight: bold; }
        .st-l { color: #7c3aed; font-weight: bold; }
        .st-empty { color: #d1d5db; }

        .bg-minggu { background-color: #fef2f2; }
        .bg-sabtu { background-color: #f3f4f6; }

        /* FOOTER */
        .footer-table { width: 100%; margin-top: 5px; border: none; }
        .footer-table td { border: none; padding: 5px 0; vertical-align: top; }
        .legend-item { margin-right: 15px; font-size: 8px; }
        .timestamp { font-size: 8px; color: #555; font-style: italic; text-align: right; }

    </style>
</head>
<body>
    <div class="header-doc">
        <h1>Laporan Rekap Absensi Bulanan</h1>
        <p>PT RAKHA NUSANTARA MEDIKA</p>
        <p>Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMMM YYYY') }} - {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMMM YYYY') }}</strong></p>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="2" class="col-karyawan" style="text-align: center;">Karyawan</th>
                <th colspan="{{ $allDates->count() }}">Bulan {{ \Carbon\Carbon::parse($startDate)->isoFormat('MMMM YYYY') }}</th>
                <th colspan="6">Rekap Kehadiran</th>
                <th rowspan="2" class="col-late">Waktu Terlambat</th>
            </tr>
            <tr>
                @foreach($allDates as $date)
                    @php
                        $class = '';
                        if ($date->isSunday()) $class = 'th-minggu';
                        elseif ($date->isSaturday()) $class = 'th-sabtu';
                    @endphp
                    <th class="col-date {{ $class }}">{{ $date->day }}</th>
                @endforeach
                
                <th class="col-sum" style="color:#166534;">H</th>
                <th class="col-sum" style="color:#dc2626;">S</th>
                <th class="col-sum" style="color:#d97706;">I</th>
                <th class="col-sum" style="color:#2563eb;">C</th>
                <th class="col-sum" style="color:#374151;">A</th>
                <th class="col-sum" style="color:#7c3aed;">L</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rekapData as $index => $data)
            <tr>
                {{-- 1. NAMA & JABATAN (RATA KIRI) --}}
                <td class="col-karyawan pad-normal" style="text-align: left;">
                    <span class="name-text">{{ $data['user']->name ?? 'User Dihapus' }}</span>
                    <span class="jabatan-text">{{ $data['user']->divisi ?? '-' }}</span>
                </td>

                {{-- 2. LOOP TANGGAL --}}
                @foreach($allDates as $date)
                    @php
                        $isSunday = $date->isSunday();
                        $isSaturday = $date->isSaturday();
                        
                        $statusString = $data['daily'][$date->toDateString()] ?? '-';
                        $hasLembur = str_contains($statusString, 'L');
                        $mainStatus = $hasLembur ? trim(str_replace('L', '', $statusString)) : $statusString;
                        if ($mainStatus == "") $mainStatus = 'L'; // Handle jika cuma Lembur

                        // Tentukan Warna Teks
                        $textClass = 'st-empty';
                        switch ($mainStatus) {
                            case 'H': $textClass = 'st-h'; break;
                            case 'S': $textClass = 'st-s'; break;
                            case 'I': $textClass = 'st-i'; break;
                            case 'C': $textClass = 'st-c'; break;
                            case 'A': $textClass = 'st-a'; break;
                            case 'L': $textClass = 'st-l'; $hasLembur = false; break; // Jika main status L, matikan flag lembur ganda
                        }

                        $bgClass = '';
                        if ($isSunday) $bgClass = 'bg-minggu';
                        elseif ($isSaturday) $bgClass = 'bg-sabtu';
                    @endphp
                    
                    <td class="{{ $bgClass }}">
                        {{-- LOGIKA TAMPILAN BERTUMPUK (H di atas, L di bawah) --}}
                        @if ($hasLembur && $mainStatus != '-' && $mainStatus != 'L')
                            <div class="status-stack">
                                <span class="{{ $textClass }} status-top">{{ $mainStatus }}</span>
                                <span class="st-l status-bottom">L</span>
                            </div>
                        @else
                            {{-- Tampilan Biasa (Single Status) --}}
                            <span class="{{ $textClass }} status-single">{{ $mainStatus }}</span>
                        @endif
                    </td>
                @endforeach

                {{-- 3. SUMMARY --}}
                <td class="col-sum pad-normal st-h">{{ $data['summary']['H'] }}</td>
                <td class="col-sum pad-normal st-s">{{ $data['summary']['S'] }}</td>
                <td class="col-sum pad-normal st-i">{{ $data['summary']['I'] }}</td>
                <td class="col-sum pad-normal st-c">{{ $data['summary']['C'] }}</td>
                <td class="col-sum pad-normal st-a">{{ $data['summary']['A'] }}</td>
                <td class="col-sum pad-normal st-l">{{ $data['summary']['L'] }}</td>
                
                {{-- 4. TERLAMBAT --}}
                <td class="pad-normal" style="color: #b91c1c; font-weight: bold;">
                    {{ $data['summary']['terlambat_formatted'] }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ $allDates->count() + 8 }}" class="pad-normal" style="padding: 15px; font-style: italic; color: #666;">
                    Data tidak ditemukan.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer-table">
        <tr>
            <td style="text-align: left; width: 70%;">
                <span style="font-weight: bold; font-size: 9px; display:block; margin-bottom:3px;">Keterangan Kode:</span>
                <span class="legend-item"><strong class="st-h">H</strong> : Hadir</span>
                <span class="legend-item"><strong class="st-s">S</strong> : Sakit</span>
                <span class="legend-item"><strong class="st-i">I</strong> : Izin</span>
                <span class="legend-item"><strong class="st-c">C</strong> : Cuti</span>
                <span class="legend-item"><strong class="st-a">A</strong> : Alpha</span>
                <span class="legend-item"><strong class="st-l">L</strong> : Lembur</span>
            </td>
            <td class="timestamp">
                Dicetak otomatis oleh sistem pada: {{ now()->format('d/m/Y H:i') }}
            </td>
        </tr>
    </table>

</body>
</html>