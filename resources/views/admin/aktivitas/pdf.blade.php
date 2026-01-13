<!DOCTYPE html>
<html>
<head>
    <title>Laporan Aktivitas Harian</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; color: #333; }
        
        /* Header Laporan */
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; }
        .header h2 { margin: 0; text-transform: uppercase; color: #1e3a8a; }
        .header p { margin: 2px 0; color: #555; font-size: 9pt; }
        
        /* Info Filter */
        .meta-info { margin-bottom: 15px; font-size: 10pt; }
        
        /* Tabel Data */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        
        /* Header Tabel Biru */
        th { 
            background-color: #2563eb; /* Blue-600 */
            color: #ffffff; 
            padding: 8px; 
            text-align: left; 
            font-weight: bold;
            font-size: 9pt;
            border: 1px solid #1e40af;
        }
        
        td { 
            border: 1px solid #cbd5e1; 
            padding: 8px; 
            vertical-align: top; 
            font-size: 9pt;
        }

        /* Zebra Striping agar mudah dibaca */
        tr:nth-child(even) { background-color: #f1f5f9; }

        /* Helpers */
        .text-bold { font-weight: bold; }
        .text-small { font-size: 8pt; color: #64748b; }
        .badge { 
            display: inline-block; 
            padding: 2px 6px; 
            background: #e0e7ff; 
            color: #3730a3;
            border-radius: 4px; 
            font-size: 7pt; 
            border: 1px solid #c7d2fe;
            margin-bottom: 2px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>PT Rakha Nusantara Medika</h2>
        <p>Laporan Aktivitas Harian Karyawan</p>
    </div>

    <div class="meta-info">
        <table style="border: none; margin: 0; padding: 0;">
            <tr style="background-color: transparent;">
                <td style="border: none; padding: 2px; width: 80px;"><strong>Tanggal</strong></td>
                <td style="border: none; padding: 2px;">: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</td>
            </tr>
            <tr style="background-color: transparent;">
                <td style="border: none; padding: 2px;"><strong>Filter</strong></td>
                <td style="border: none; padding: 2px;">: {{ $filterInfo }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25%;">Karyawan</th>
                <th style="width: 15%;">Waktu</th>
                <th style="width: 40%;">Keterangan</th>
                <th style="width: 20%;">Lampiran & Lokasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($aktivitas as $item)
            <tr>
                {{-- Kolom Karyawan --}}
                <td>
                    <div class="text-bold">{{ $item->user->name ?? 'User Dihapus' }}</div>
                    <div class="text-small">{{ $item->user->divisi ?? '-' }}</div>
                </td>

                {{-- Kolom Waktu --}}
                <td>
                    {{ $item->created_at->format('H:i') }} WIB
                </td>

                {{-- Kolom Keterangan (FIX: Menggunakan title & keterangan, bukan deskripsi) --}}
                <td>
                    <div class="text-bold">{{ $item->title ?? 'Tidak ada judul' }}</div>
                </td>

                {{-- Kolom Lampiran (FIX: Menggunakan lampiran, bukan foto) --}}
                <td>
                    @php $hasAttachment = false; @endphp

                    @if($item->lampiran)
                        <div class="badge">Ada Foto</div>
                        @php $hasAttachment = true; @endphp
                    @endif
                    
                    @if($item->latitude && $item->longitude)
                        <div class="badge">Ada Lokasi</div>
                        @php $hasAttachment = true; @endphp
                    @endif

                    @if(!$hasAttachment)
                        <span class="text-small">-</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 15px; color: #777;">
                    Tidak ada data aktivitas yang ditemukan pada tanggal ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right; font-size: 8pt; color: #777; border-top: 1px solid #ddd; padding-top: 5px;">
        Dicetak otomatis oleh sistem pada: {{ now()->translatedFormat('d F Y H:i') }}
    </div>

</body>
</html>