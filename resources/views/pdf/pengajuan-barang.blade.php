<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pengajuan Barang - {{ str_pad($pengajuanBarang->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        /* Gaya Inti */
        body { font-family: 'Helvetica', sans-serif; font-size: 9px; color: #333; line-height: 1.2; }
        .container { width: 95%; margin: 15px auto; }
        
        /* Header */
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 20px; color: #003366; font-weight: bold; } 
        .header p { margin: 4px 0; font-size: 12px; }

        /* Tabel Data */
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table.data-table th, table.data-table td { border: 1px solid #ddd; padding: 7px; text-align: left; vertical-align: top; }
        table.data-table th { background-color: #f2f2f2; font-weight: bold; }

        /* Judul Bagian */
        .section-title {
            background-color: #eaf2f8;
            padding: 9px;
            font-size: 11px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 10px;
            border-left: 4px solid #3498db;
            color: #003366;
        }

        /* --- STYLE TANDA TANGAN (ADAPTASI DARI PENGAJUAN DANA) --- */
        .signatures { 
            width: 100%; 
            margin-top: 10px; 
            border: none; 
            table-layout: fixed; 
        }
        .signatures td { 
            width: 33.33%; 
            border: none; 
            text-align: center; 
            vertical-align: top; 
            padding: 10px; 
        }

        .ttd-header { margin-bottom: 25px; font-size: 11px; color: #333; }

        .st-approved { 
            color: #28a745; 
            font-weight: bold; 
            font-style: italic; 
            font-size: 12px; 
            margin-bottom: 20px;
        }
        .st-rejected { 
            color: #dc3545; 
            font-weight: bold; 
            font-style: italic; 
            font-size: 12px; 
            margin-bottom: 20px;
        }
        .st-placeholder {
            margin: 20px 0;
            border-bottom: 1px dotted #aaa;
            color: #aaa;
            font-style: italic;
            font-size: 10px;
            padding-bottom: 5px;
        }

        .ttd-nama { 
            font-weight: bold; 
            text-decoration: underline; 
            font-size: 12px; 
            color: #000;
        }
        .ttd-jabatan { 
            font-size: 10px; 
            color: #444; 
            margin-top: 2px;
        }
        .ttd-tanggal { 
            font-weight: bold; 
            color: #555; 
            font-size: 10px; 
            margin-top: 5px; 
        }

        /* Status Box Final */
        .status-box { padding: 10px; margin-top: 20px; border-radius: 4px; text-align: center; font-weight: bold; font-size: 16px; border: 1px solid #ccc; }
        .status-disetujui { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
        .status-ditolak { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
        .status-diproses { background-color: #cce5ff; color: #004085; border-color: #b8daff; }
        .status-dibatalkan { background-color: #e2e3e5; color: #383d41; border-color: #d6d8db; }
        
        .catatan { background: #f9f9f9; border-left: 3px solid #ccc; padding: 8px; margin-top: 5px; font-style: italic; font-size: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PT RAKHA NUSANTARA MEDIKA</h1>
            <p style="font-weight: bold;">FORMULIR PENGAJUAN BARANG</p>
            <p style="font-size: 10px; font-weight: bold;">ID Pengajuan: {{ str_pad($pengajuanBarang->id, 4, '0', STR_PAD_LEFT) }}</p>
        </div>

        {{-- I. DETAIL PENGAJUAN --}}
        <div class="section-title">I. DETAIL PENGAJUAN</div>
        <table class="data-table">
            <tr>
                <th width="15%">Tanggal</th>
                <td width="35%">{{ $pengajuanBarang->created_at ? $pengajuanBarang->created_at->translatedFormat('l, d F Y') : '-' }}</td>
                <th width="15%">Pemohon</th>
                <td width="35%">{{ $pengajuanBarang->user->name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Divisi</th>
                <td>{{ $pengajuanBarang->divisi }}</td>
                <th>Jabatan</th>
                <td>{{ $pengajuanBarang->user->jabatan ?? '-' }}</td>
            </tr>
            <tr>
                <th>Judul</th>
                <td colspan="3">{{ $pengajuanBarang->judul_pengajuan }}</td>
            </tr>
        </table>

        {{-- II. RINCIAN BARANG --}}
        <div class="section-title">II. RINCIAN BARANG</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="text-align: center; width: 5%;">No</th>
                    <th>Deskripsi Barang</th>
                    <th style="text-align: center; width: 15%;">Satuan</th>
                    <th style="text-align: center; width: 15%;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($pengajuanBarang->rincian_barang) && (is_array($pengajuanBarang->rincian_barang) || is_object($pengajuanBarang->rincian_barang)))
                    @foreach ($pengajuanBarang->rincian_barang as $index => $item)
                        <tr>
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td>{{ $item['deskripsi'] ?? '-' }}</td>
                            <td style="text-align: center;">{{ $item['satuan'] ?? '-' }}</td>
                            <td style="text-align: center;">{{ $item['jumlah'] ?? 0 }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr><td colspan="4" style="text-align: center;">Tidak ada rincian barang.</td></tr>
                @endif
            </tbody>
        </table>
        
        {{-- III. PERSETUJUAN (MODEL PENGAJUAN DANA) --}}
        <div class="section-title">III. LEMBAR PERSETUJUAN</div>
        <table class="signatures">
            <tr>
                {{-- APPROVER 1 --}}
                <td>
                    <div class="ttd-header">Disetujui oleh (Approver 1),</div>
                    @if($pengajuanBarang->status_appr_1 == 'disetujui')
                        <div class="st-approved">[ DISETUJUI ]</div>
                        <div class="ttd-nama">{{ $pengajuanBarang->approver1->name ?? 'Approver 1' }}</div>
                        <div class="ttd-jabatan">{{ $pengajuanBarang->approver1->jabatan ?? 'Atasan' }}</div>
                        <div class="ttd-tanggal">{{ $pengajuanBarang->tanggal_approved_1?->translatedFormat('d/m/Y H:i') }} WIB</div>
                    @elseif($pengajuanBarang->status_appr_1 == 'ditolak')
                        <div class="st-rejected">[ DITOLAK ]</div>
                        <div class="ttd-nama">{{ $pengajuanBarang->approver1->name ?? 'Approver 1' }}</div>
                        <div class="ttd-tanggal">{{ $pengajuanBarang->tanggal_approved_1?->translatedFormat('d/m/Y H:i') }} WIB</div>
                    @elseif($pengajuanBarang->status_appr_1 == 'skipped')
                        <div class="st-placeholder">(Dilewati)</div>
                    @else
                        <div class="st-placeholder">( Menunggu Persetujuan )</div>
                    @endif
                </td>

                {{-- APPROVER 2 --}}
                <td>
                    <div class="ttd-header">Disetujui oleh (Approver 2),</div>
                    @if($pengajuanBarang->status_appr_2 == 'disetujui')
                        <div class="st-approved">[ DISETUJUI ]</div>
                        <div class="ttd-nama">{{ $pengajuanBarang->approver2->name ?? 'Admin Gudang' }}</div>
                        <div class="ttd-jabatan">{{ $pengajuanBarang->approver2->jabatan ?? 'Gudang/Logistik' }}</div>
                        <div class="ttd-tanggal">{{ $pengajuanBarang->tanggal_approved_2?->translatedFormat('d/m/Y H:i') }} WIB</div>
                    @elseif($pengajuanBarang->status_appr_2 == 'ditolak')
                        <div class="st-rejected">[ DITOLAK ]</div>
                        <div class="ttd-nama">{{ $pengajuanBarang->approver2->name ?? 'Admin Gudang' }}</div>
                        <div class="ttd-tanggal">{{ $pengajuanBarang->tanggal_approved_2?->translatedFormat('d/m/Y H:i') }} WIB</div>
                    @elseif($pengajuanBarang->status_appr_2 == 'skipped')
                        <div class="st-placeholder">(Dilewati)</div>
                    @else
                        <div class="st-placeholder">( Menunggu Persetujuan )</div>
                    @endif
                </td>

                {{-- APPROVER 3 --}}
                <td>
                    <div class="ttd-header">Disetujui oleh (Manager Keuangan),</div>
                    @if($pengajuanBarang->status_appr_3 == 'disetujui')
                        <div class="st-approved">[ DISETUJUI ]</div>
                        <div class="ttd-nama">{{ $pengajuanBarang->approver3->name ?? 'Manager' }}</div>
                        <div class="ttd-jabatan">{{ $pengajuanBarang->approver3->jabatan ?? 'Keuangan/Direksi' }}</div>
                        <div class="ttd-tanggal">{{ $pengajuanBarang->tanggal_approved_3?->translatedFormat('d/m/Y H:i') }} WIB</div>
                    @elseif($pengajuanBarang->status_appr_3 == 'ditolak')
                        <div class="st-rejected">[ DITOLAK ]</div>
                        <div class="ttd-nama">{{ $pengajuanBarang->approver3->name ?? 'Manager' }}</div>
                        <div class="ttd-tanggal">{{ $pengajuanBarang->tanggal_approved_3?->translatedFormat('d/m/Y H:i') }} WIB</div>
                    @elseif($pengajuanBarang->status_appr_3 == 'skipped')
                        <div class="st-placeholder">(Dilewati)</div>
                    @else
                        <div class="st-placeholder">( Menunggu Persetujuan )</div>
                    @endif
                </td>
            </tr>
        </table>

        {{-- IV. CATATAN --}}
        @if($pengajuanBarang->catatan_approver_1 || $pengajuanBarang->catatan_approver_2 || $pengajuanBarang->catatan_approver_3)
            <div class="section-title">IV. CATATAN APPROVER</div>
            <table class="data-table">
                @if($pengajuanBarang->catatan_approver_1)
                    <tr><td width="25%">Catatan Approver 1</td><td><div class="catatan">{{ $pengajuanBarang->catatan_approver_1 }}</div></td></tr>
                @endif
                @if($pengajuanBarang->catatan_approver_2)
                    <tr><td width="25%">Catatan Approver 2</td><td><div class="catatan">{{ $pengajuanBarang->catatan_approver_2 }}</div></td></tr>
                @endif
                @if($pengajuanBarang->catatan_approver_3)
                    <tr><td width="25%">Catatan Approver 3</td><td><div class="catatan">{{ $pengajuanBarang->catatan_approver_3 }}</div></td></tr>
                @endif
            </table>
        @endif                                                              

        {{-- STATUS FINAL --}}
        @php
            $statusFinal = strtolower($pengajuanBarang->status ?? 'diproses');
            $finalStatusClass = 'status-diproses'; 
            $statusLabel = 'DIPROSES';
            if($statusFinal == 'selesai' || $statusFinal == 'disetujui') { $finalStatusClass = 'status-selesai'; $statusLabel = 'SELESAI'; } 
            else if($statusFinal == 'ditolak') { $finalStatusClass = 'status-ditolak'; $statusLabel = 'DITOLAK'; } 
            else if($statusFinal == 'dibatalkan') { $finalStatusClass = 'status-dibatalkan'; $statusLabel = 'DIBATALKAN'; }
        @endphp
        <div class="status-box {{ $finalStatusClass }}">Status Final: {{ strtoupper($statusLabel) }}</div>
    </div>
</body>
</html>