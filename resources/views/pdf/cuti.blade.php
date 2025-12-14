<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pengajuan Cuti - {{ str_pad($cuti->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        /* Gaya Inti (Seragam dengan Pengajuan Barang/Dana) */
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        .container { width: 95%; margin: 15px auto; }
        
        /* Header */
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #003366; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; color: #003366; font-weight: bold; text-transform: uppercase; } 
        .header p { margin: 2px 0; font-size: 11px; }

        /* Tabel Data */
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table.data-table th, table.data-table td { border: 1px solid #ddd; padding: 7px; text-align: left; vertical-align: top; }
        table.data-table th { background-color: #f2f2f2; font-weight: bold; width: 30%; }

        /* Judul Bagian */
        .section-title {
            background-color: #eaf2f8;
            padding: 8px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            border-left: 4px solid #3498db;
            color: #003366;
            text-transform: uppercase;
        }

        /* Status Box */
        .status-box {
            padding: 10px;
            margin-top: 30px;
            border-radius: 4px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            border: 1px solid #ccc;
        }
        .status-disetujui { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
        .status-ditolak { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
        .status-diajukan { background-color: #fff3cd; color: #856404; border-color: #ffeeba; }
        .status-dibatalkan { background-color: #e2e3e5; color: #383d41; border-color: #d6d8db; }

        /* Tanda Tangan (2 Kolom - Polos Tanpa Garis) */
        .signatures { 
            width: 100%; 
            margin-top: 30px; 
            border: none; 
            table-layout: fixed; 
        }
        .signatures td { 
            width: 50%; 
            border: none; 
            text-align: center; 
            vertical-align: top; 
            padding: 10px; 
        }

        .ttd-header { margin-bottom: 25px; font-size: 11px; color: #333; }

        /* Status Text */
        .st-approved { color: #28a745; font-weight: bold; font-style: italic; font-size: 12px; margin-bottom: 20px; }
        .st-rejected { color: #dc3545; font-weight: bold; font-style: italic; font-size: 12px; margin-bottom: 20px; }
        .st-placeholder { margin: 20px 0; border-bottom: 1px dotted #aaa; color: #aaa; font-style: italic; font-size: 10px; padding-bottom: 5px; }

        /* Detail TTD */
        .ttd-nama { font-weight: bold; text-decoration: underline; font-size: 12px; color: #000; }
        .ttd-jabatan { font-size: 11px; color: #444; margin-top: 2px; }
        .ttd-tanggal { font-weight: bold; color: #555; font-size: 10px; margin-top: 5px; }

        .alasan-box {
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #fafafa;
            border-radius: 4px;
            min-height: 50px;
        }
    </style>
</head>
<body>
    <div class="container">

        {{-- HEADER --}}
        <div class="header">
            <h1>PT RAKHA NUSANTARA MEDIKA</h1>
            <p style="font-weight: bold;">FORMULIR PENGAJUAN CUTI</p>
            <p>ID Pengajuan: CUTI-{{ str_pad($cuti->id, 4, '0', STR_PAD_LEFT) }}</p>
        </div>

        {{-- I. DATA KARYAWAN --}}
        <div class="section-title">I. DATA KARYAWAN</div>
        <table class="data-table">
            <tr><th>Nama Lengkap</th><td>{{ $cuti->user->name }}</td></tr>
            <tr><th>Divisi</th><td>{{ $cuti->user->divisi }}</td></tr>
            <tr><th>Jabatan</th><td>{{ $cuti->user->jabatan ?? '-' }}</td></tr>
            <tr><th>Tanggal Pengajuan</th><td>{{ $cuti->created_at->translatedFormat('l, d F Y') }}</td></tr>
        </table>

        {{-- II. DETAIL CUTI --}}
        <div class="section-title">II. DETAIL CUTI</div>
        <table class="data-table">
            <tr><th>Jenis Cuti</th><td>{{ ucfirst($cuti->jenis_cuti) }}</td></tr>
            <tr><th>Tanggal Mulai</th><td>{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->translatedFormat('l, d F Y') }}</td></tr>
            <tr><th>Tanggal Selesai</th><td>{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->translatedFormat('l, d F Y') }}</td></tr>
            <tr><th>Lama Cuti</th><td>{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($cuti->tanggal_selesai)) + 1 }} Hari</td></tr>
            <tr>
                <th>Alasan Cuti</th>
                <td><div class="alasan-box">{{ $cuti->alasan }}</div></td>
            </tr>
        </table>

        {{-- III. PERSETUJUAN --}}
        <div class="section-title">III. LEMBAR PERSETUJUAN</div>
        <table class="signatures">
            <tr>
                {{-- KOLOM 1: PEMOHON --}}
                <td>
                    <div class="ttd-header">Diajukan oleh,</div>
                    {{-- Pemohon selalu dianggap 'signed' saat mengajukan --}}
                    <div class="st-placeholder" style="border:none; margin: 20px 0;">&nbsp;</div> 
                    
                    <p class="ttd-nama">{{ $cuti->user->name }}</p>
                    <p class="ttd-jabatan">{{ $cuti->user->jabatan ?? 'Karyawan' }}</p>
                    <p class="ttd-tanggal">{{ $cuti->created_at->translatedFormat('l, d F Y H:i') }} WIB</p>
                </td>

                {{-- KOLOM 2: ATASAN / APPROVER --}}
                <td>
                    <div class="ttd-header">Disetujui oleh (Atasan Langsung),</div>

                    @if($cuti->status == 'disetujui')
                        <div class="st-approved">[ DISETUJUI ]</div>
                        <p class="ttd-nama">{{ $approver->name ?? 'Atasan' }}</p>
                        <p class="ttd-jabatan">{{ $approver->jabatan ?? '-' }}</p>
                        {{-- Menggunakan updated_at sebagai waktu persetujuan --}}
                        <p class="ttd-tanggal">{{ $cuti->updated_at->translatedFormat('l, d F Y H:i') }} WIB</p>
                    
                    @elseif($cuti->status == 'ditolak')
                        <div class="st-rejected">[ DITOLAK ]</div>
                        <p class="ttd-nama">{{ $approver->name ?? 'Atasan' }}</p>
                        <p class="ttd-tanggal">{{ $cuti->updated_at->translatedFormat('l, d F Y H:i') }} WIB</p>

                    @elseif($cuti->status == 'dibatalkan')
                        <div class="st-placeholder">( Dibatalkan oleh Pemohon )</div>

                    @else
                        <div class="st-placeholder">( Menunggu Persetujuan )</div>
                        <p class="ttd-nama" style="text-decoration:none; color:#999;">{{ $approver->name ?? 'Atasan' }}</p>
                    @endif
                </td>
            </tr>
        </table>

        {{-- CATATAN ATASAN (Jika Ada) --}}
        @if($cuti->catatan_approval)
            <div class="section-title">IV. CATATAN PIMPINAN</div>
            <div style="border: 1px solid #ccc; padding: 10px; background: #fff; font-style: italic; color: #555;">
                "{{ $cuti->catatan_approval }}"
            </div>
        @endif

        {{-- STATUS FINAL BOX --}}
        @php
            $statusClass = match($cuti->status) {
                'disetujui' => 'status-disetujui',
                'ditolak' => 'status-ditolak',
                'dibatalkan' => 'status-dibatalkan',
                default => 'status-diajukan',
            };
        @endphp
        <div class="status-box {{ $statusClass }}">
            STATUS PENGAJUAN: {{ strtoupper($cuti->status) }}
        </div>

    </div>
</body>
</html>