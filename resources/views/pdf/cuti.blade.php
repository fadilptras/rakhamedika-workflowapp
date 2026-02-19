<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pengajuan Cuti - {{ str_pad($cuti->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        /* Gaya Inti (Seragam dengan Pengajuan Barang/Dana) */
        body { font-family: 'Helvetica', sans-serif; font-size: 9px; color: #333; line-height: 1.2; }
        .container { width: 95%; margin: 15px auto; }
        
        /* Header */
        .header { text-align: center; margin-bottom: 20px; border-bottom: none; padding-bottom: 10px; }
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
            font-size: 11px;
            font-weight: bold;
            margin-top: 10px;
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
            min-height: 20px;
        }
    </style>
</head>
<body>
    <div class="container">

        {{-- HEADER --}}
        <div class="header">
            <h1>PT RAKHA NUSANTARA MEDIKA</h1>
            <p style="font-weight: bold;">FORMULIR PENGAJUAN CUTI</p>
            <p>ID Pengajuan: {{ str_pad($cuti->id, 4, '0', STR_PAD_LEFT) }}</p>
        </div>

        {{-- I. DATA KARYAWAN --}}
        {{-- I. DATA KARYAWAN --}}
        <div class="section-title">I. DATA KARYAWAN</div>
        <table class="data-table">
            <tr>
                <th>Nama Lengkap</th>
                <td>{{ $cuti->user->name }}</td>
            </tr>
            <tr>
                <th>Divisi</th>
                <td>{{ $cuti->user->divisi }}</td>
            </tr>
            <tr>
                <th>Jabatan</th>
                <td>{{ $cuti->user->jabatan ?? '-' }}</td>
            </tr>
            <tr>
                <th>Sisa Cuti Tahunan</th> 
                <td><strong>{{ $sisaCuti }} Hari</strong></td>
            </tr>
            <tr>
                <th>Tanggal Pengajuan</th>
                <td>{{ $cuti->created_at->translatedFormat('l, d F Y') }}</td>
            </tr>
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

        {{-- III. PERSETUJUAN (3 KOLOM) --}}
        <div class="section-title">III. LEMBAR PERSETUJUAN</div>
        <table class="signatures">
            <tr>
                {{-- KOLOM 1: APPROVER 1 (Atasan Langsung) --}}
                <td>
                    <div class="ttd-header">Disetujui oleh (Approver 1),</div>
                    
                    @if($cuti->status_approver_1 == 'disetujui')
                        <div class="st-approved">[ DISETUJUI ]</div>
                        <p class="ttd-nama">{{ $cuti->approver1->name ?? 'Atasan' }}</p>
                        <p class="ttd-jabatan">{{ $cuti->approver1->jabatan ?? 'Atasan Langsung' }}</p>
                        <p class="ttd-tanggal">{{ $cuti->updated_at->translatedFormat('d/m/Y H:i') }} WIB</p>
                    
                    @elseif($cuti->status_approver_1 == 'ditolak')
                        <div class="st-rejected">[ DITOLAK ]</div>
                        <p class="ttd-nama">{{ $cuti->approver1->name ?? 'Atasan' }}</p>
                        <p class="ttd-tanggal">{{ $cuti->updated_at->translatedFormat('d/m/Y H:i') }} WIB</p>

                    @elseif($cuti->status_approver_1 == 'skipped')
                        <div class="st-placeholder">( Dilewati )</div>
                    @else
                        <div class="st-placeholder">( Menunggu Persetujuan )</div>
                    @endif
                </td>

                {{-- KOLOM 2: APPROVER 2 (Manager) --}}
                <td>
                    <div class="ttd-header">Disetujui oleh (Approver 2),</div>

                    @if($cuti->status_approver_2 == 'disetujui')
                        <div class="st-approved">[ DISETUJUI ]</div>
                        <p class="ttd-nama">{{ $cuti->approver2->name ?? 'Manager' }}</p>
                        <p class="ttd-jabatan">{{ $cuti->approver2->jabatan ?? 'Manager' }}</p>
                        <p class="ttd-tanggal">{{ $cuti->updated_at->translatedFormat('d/m/Y H:i') }} WIB</p>

                    @elseif($cuti->status_approver_2 == 'ditolak')
                        <div class="st-rejected">[ DITOLAK ]</div>
                        <p class="ttd-nama">{{ $cuti->approver2->name ?? 'Manager' }}</p>
                        <p class="ttd-tanggal">{{ $cuti->updated_at->translatedFormat('d/m/Y H:i') }} WIB</p>

                    @elseif($cuti->status_approver_2 == 'skipped')
                        <div class="st-placeholder">( Dilewati )</div>
                    @else
                        <div class="st-placeholder">( Menunggu Persetujuan )</div>
                    @endif
                </td>

                {{-- KOLOM 3: APPROVER 3 (HRD) --}}
                <td>
                    <div class="ttd-header">Disetujui oleh (HRD),</div>

                    @if($cuti->status_approver_3 == 'disetujui')
                        <div class="st-approved">[ DISETUJUI ]</div>
                        <p class="ttd-nama">{{ $cuti->approver3->name ?? 'HRD' }}</p>
                        <p class="ttd-jabatan">{{ $cuti->approver3->jabatan ?? 'Human Resources' }}</p>
                        <p class="ttd-tanggal">{{ $cuti->updated_at->translatedFormat('d/m/Y H:i') }} WIB</p>

                    @elseif($cuti->status_approver_3 == 'ditolak')
                        <div class="st-rejected">[ DITOLAK ]</div>
                        <p class="ttd-nama">{{ $cuti->approver3->name ?? 'HRD' }}</p>
                        <p class="ttd-tanggal">{{ $cuti->updated_at->translatedFormat('d/m/Y H:i') }} WIB</p>

                    @elseif($cuti->status_approver_3 == 'skipped')
                        <div class="st-placeholder">( Dilewati )</div>
                    @else
                        <div class="st-placeholder">( Menunggu Persetujuan )</div>
                    @endif
                </td>
            </tr>
        </table>

        {{-- IV. CATATAN APPROVER --}}
        @if($cuti->catatan_approver_1 || $cuti->catatan_approver_2 || $cuti->catatan_approver_3)
            <div class="section-title">IV. CATATAN APPROVER</div>
            <table class="data-table">
                @if($cuti->catatan_approver_1)
                    <tr><td width="25%">Catatan Approver 1</td><td><div class="catatan">{{ $cuti->catatan_approver_1 }}</div></td></tr>
                @endif
                @if($cuti->catatan_approver_2)
                    <tr><td width="25%">Catatan Approver 2</td><td><div class="catatan">{{ $cuti->catatan_approver_2 }}</div></td></tr>
                @endif
                @if($cuti->catatan_approver_3)
                    <tr><td width="25%">Catatan Approver 3</td><td><div class="catatan">{{ $cuti->catatan_approver_3 }}</div></td></tr>
                @endif
            </table>
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