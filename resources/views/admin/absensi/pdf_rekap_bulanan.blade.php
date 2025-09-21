<!DOCTYPE html>
<html>
<head>
    <title>Rekap Absensi Bulanan</title>
    <style>
        @page {
            size: landscape;
        }
        body { font-family: sans-serif; font-size: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #dddddd; text-align: center; padding: 4px; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; font-size: 12px; }
        .rotate {
            transform: rotate(-90deg);
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Rekap Absensi Bulanan</h1>
        <p>PT RAKHA MEDIKA NUSANTARA</p>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMMM YYYY') }} - {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMMM YYYY') }}</p>
    </div>

    <table class="table-auto">
        <thead>
            <tr>
                <th rowspan="2" style="width: 15%; vertical-align: top;">Karyawan</th>
                <th colspan="{{ $allDates->count() }}" style="width: auto;">Bulan {{ \Carbon\Carbon::parse($startDate)->isoFormat('MMMM YYYY') }}</th>
                <th colspan="6">Rekap Kehadiran</th>
                <th>Waktu Terlambat</th>
            </tr>
            <tr>
                @foreach($allDates as $date)
                    @php
                        $isWeekend = $date->isWeekend();
                        $textColor = $isWeekend ? 'color: #9ca3af;' : ''; // Warna abu-abu untuk weekend
                        $bgColor = $isWeekend ? 'background-color: #f3f4f6;' : ''; // Warna latar belakang abu-abu
                    @endphp
                    <th style="width: 15px; padding: 2px; {{ $textColor }} {{ $bgColor }}">{{ $date->day }}</th>
                @endforeach
                <th style="color: green;">H</th>
                <th style="color: red;">S</th>
                <th style="color: orange;">I</th>
                <th style="color: blue;">C</th>
                <th style="color: gray;">A</th>
                <th style="color: purple;">L</th>
                <th style="width: 10%; padding: 2px;"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rekapData as $index => $data)
            <tr>
                <td style="text-align: left;">{{ $index + 1 }}. {{ $data['user']->name ?? 'User Dihapus' }}</td>
                @foreach($allDates as $date)
                    @php
                        $isWeekend = $date->isWeekend();
                        $statusString = $data['daily'][$date->toDateString()] ?? '-';
                        $hasLembur = str_contains($statusString, 'L');
                        $mainStatus = trim(str_replace('L', '', $statusString));

                        $color = '';
                        if ($isWeekend) {
                            $color = '#9ca3af'; // Warna abu-abu untuk weekend
                            $mainStatus = '-'; // Pastikan status di hari weekend adalah '-'
                        } else {
                            switch ($mainStatus) {
                                case 'H': $color = 'green'; break;
                                case 'S': $color = 'red'; break;
                                case 'I': $color = 'orange'; break;
                                case 'C': $color = 'blue'; break;
                                case 'A': $color = 'gray'; break;
                                default: $color = 'black'; break;
                            }
                        }
                    @endphp
                    <td style="background-color: {{ $isWeekend ? '#f3f4f6' : 'white' }};">
                        <span style="color: {{ $color }};">{{ $mainStatus }}</span>
                        @if ($hasLembur)
                            <span style="color: purple;">L</span>
                        @endif
                    </td>
                @endforeach
                <td style="color: green;">{{ $data['summary']['H'] }}</td>
                <td style="color: red;">{{ $data['summary']['S'] }}</td>
                <td style="color: orange;">{{ $data['summary']['I'] }}</td>
                <td style="color: blue;">{{ $data['summary']['C'] }}</td>
                <td style="color: gray;">{{ $data['summary']['A'] }}</td>
                <td style="color: purple;">{{ $data['summary']['L'] }}</td>
                <td>{{ $data['summary']['terlambat_formatted'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ $allDates->count() + 7 }}" style="text-align: center;">Tidak ada data absensi yang cocok dengan filter.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>