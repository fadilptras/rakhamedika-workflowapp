<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pengajuan Barang - {{ str_pad($pengajuanBarang->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; line-height: 1.5; }
        .container { width: 95%; margin: 15px auto; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h1 { margin: 0; font-size: 20px; color: #003366; font-weight: bold; } 
        .header p { margin: 4px 0; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ddd; padding: 7px; text-align: left; vertical-align: top; word-wrap: break-word; } 
        th { background-color: #f2f2f2; font-weight: bold; font-size: 11px; }
        .section-title { background-color: #eaf2f8; padding: 9px; font-size: 13px; font-weight: bold; margin-top: 15px; margin-bottom: 10px; border-left: 4px solid #3498db; color: #003366; }
        .status-box { padding: 10px; margin-top: 20px; border-radius: 4px; text-align: center; font-weight: bold; font-size: 16px; }
        .status-disetujui { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status-ditolak { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .status-diproses { background-color: #cce5ff; color: #004085; border: 1px solid #b8daff; }
        .status-dibatalkan { background-color: #e2e3e5; color: #383d41; border: 1px solid #d6d8db; }
        .detail-table td:first-child { width: 22%; font-weight: bold; background-color: #fafafa; }
        .rincian-table th { text-align: center; }
        .rincian-table th:first-child { width: 5%; }
        .rincian-table th:nth-child(3) { width: 15%; }
        .rincian-table th:last-child { width: 15%; }
        .rincian-table td:first-child, .rincian-table td:nth-child(3), .rincian-table td:last-child { text-align: center; }
        .rincian-table .total-row { font-weight: bold; background-color: #f5f5f5; }
        .signatures { width: 100%; margin-top: 20px; border: none; table-layout: fixed; }
        .signatures td { width: 50%; border: none; text-align: center; vertical-align: top; padding: 5px; }
        .signatures p { margin: 0; line-height: 1.3; }
        .signatures .placeholder { height: 50px; margin: 8px 0 4px 0; color: #aaa; font-style: italic; font-size: 9px; line-height: 50px; border-bottom: 1px dotted #ccc; }
        .signatures .nama { font-weight: bold; margin-top: 4px; font-size: 11px; text-decoration: underline; }
        .signatures .jabatan { font-size: 10px; color: #555; }
        .signatures .tanggal { font-size: 10px; color: #555; margin-top: 4px; font-weight: bold; }
        .catatan { background: #f9f9f9; border-left: 3px solid #ccc; padding: 8px; margin-top: 5px; font-style: italic; font-size: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PT RAKHA NUSANTARA MEDIKA</h1>
            <p style="font-weight: bold;">FORMULIR PENGAJUAN BARANG</p>
            <p style="font-size: 10px; font-weight: bold;">ID Pengajuan: BRG-{{ str_pad($pengajuanBarang->id, 4, '0', STR_PAD_LEFT) }}</p>
        </div>

        <div class="content">
            <div class="section-title">I. DETAIL PENGAJUAN</div>
            <table class="detail-table">
                <tr><td>Tanggal Pengajuan</td><td>{{ $pengajuanBarang->created_at->translatedFormat('l, d F Y') }}</td></tr>
                <tr><td>Pemohon</td><td>{{ $pengajuanBarang->user->name }}</td></tr>
                <tr><td>Divisi</td><td>{{ $pengajuanBarang->divisi }}</td></tr>
                <tr><td>Jabatan</td><td>{{ $pengajuanBarang->user->jabatan ?? '-' }}</td></tr>
                <tr><td>Judul Pengajuan</td><td>{{ $pengajuanBarang->judul_pengajuan }}</td></tr>
            </table>

            <div class="section-title">II. RINCIAN BARANG</div>
            <table class="rincian-table">
                <thead>
                    <tr><th>No</th><th>Deskripsi Barang</th><th>Satuan</th><th>Jumlah</th></tr>
                </thead>
                <tbody>
                    @forelse ($pengajuanBarang->rincian_barang as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['deskripsi'] ?? '-' }}</td>
                            <td>{{ $item['satuan'] ?? '-' }}</td>
                            <td>{{ $item['jumlah'] ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align: center;">Tidak ada rincian barang.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="total-row"><td colspan="3" style="text-align: right; padding-right: 10px;">TOTAL ITEM</td><td style="text-align: center;">{{ count($pengajuanBarang->rincian_barang ?? []) }}</td></tr>
                </tfoot>
            </table>
            
            <div class="section-title">III. PERSETUJUAN</div>
            <table class="signatures">
                <tr>
                    {{-- KOLOM 1: ATASAN --}}
                    <td>
                        <p>Disetujui oleh (Atasan Langsung),</p>
                        @if($pengajuanBarang->status_atasan == 'disetujui')
                            <div class="placeholder" style="color:#28a745; border-bottom:none;">
                                <img src="" alt="Digital Signature" style="display:none;"> [ DISETUJUI ]
                            </div>
                            {{-- MENAMPILKAN NAMA ATASAN YANG SEBENARNYA --}}
                            <p class="nama">{{ $pengajuanBarang->approverAtasan->name ?? $pengajuanBarang->user->name . ' (Data Lama)' }}</p>
                            <p class="jabatan">{{ $pengajuanBarang->approverAtasan->jabatan ?? 'Atasan Divisi' }}</p>
                            @if($pengajuanBarang->atasan_approved_at)
                                <p class="tanggal">{{ \Carbon\Carbon::parse($pengajuanBarang->atasan_approved_at)->translatedFormat('l, d F Y H:i') }} WIB</p>
                            @endif
                        @elseif($pengajuanBarang->status_atasan == 'skipped')
                            <div class="placeholder">(Dilewati / Auto-Approve)</div>
                            <p class="nama">{{ $pengajuanBarang->user->name }}</p>
                        @elseif($pengajuanBarang->status_atasan == 'ditolak')
                             <div class="placeholder" style="color:red; font-weight:bold;">DITOLAK</div>
                        @else
                            <div class="placeholder">({{ ucfirst($pengajuanBarang->status_atasan) }})</div>
                            <p class="nama">(Menunggu Atasan)</p>
                        @endif
                    </td>

                    {{-- KOLOM 2: GUDANG --}}
                    <td>
                        <p>Disetujui oleh (Gudang/Logistik),</p>
                        @if($pengajuanBarang->status_gudang == 'disetujui')
                            <div class="placeholder" style="color:#28a745; border-bottom:none;"> [ DISETUJUI ] </div>
                            
                            {{-- MENAMPILKAN NAMA GUDANG YANG SEBENARNYA --}}
                            <p class="nama">{{ $pengajuanBarang->approverGudang->name ?? 'Admin Gudang (Data Lama)' }}</p>
                            <p class="jabatan">{{ $pengajuanBarang->approverGudang->jabatan ?? 'Logistik & Gudang' }}</p>
                             @if($pengajuanBarang->gudang_approved_at)
                                <p class="tanggal">{{ \Carbon\Carbon::parse($pengajuanBarang->gudang_approved_at)->translatedFormat('l, d F Y H:i') }} WIB</p>
                            @endif
                        @elseif($pengajuanBarang->status_gudang == 'ditolak')
                            <div class="placeholder" style="color:red; font-weight:bold;">DITOLAK</div>
                        @else
                            <div class="placeholder">({{ ucfirst($pengajuanBarang->status_gudang) }})</div>
                            <p class="nama">(Bagian Gudang)</p>
                        @endif
                    </td>
                </tr>
            </table>

            @if($pengajuanBarang->catatan_atasan || $pengajuanBarang->catatan_gudang)
                <div class="section-title">IV. CATATAN</div>
                <table class="detail-table">
                    @if($pengajuanBarang->catatan_atasan)
                        <tr><td>Catatan Atasan</td><td><div class="catatan">{{ $pengajuanBarang->catatan_atasan }}</div></td></tr>
                    @endif
                    @if($pengajuanBarang->catatan_gudang)
                        <tr><td>Catatan Gudang</td><td><div class="catatan">{{ $pengajuanBarang->catatan_gudang }}</div></td></tr>
                    @endif
                </table>
            @endif
        </div>

        @php
            $statusFinal = strtolower($pengajuanBarang->status ?? 'diproses');
            $finalStatusClass = 'status-diproses'; 
            $statusLabel = 'DIPROSES';
            if($statusFinal == 'selesai' || $statusFinal == 'disetujui') { $finalStatusClass = 'status-disetujui'; $statusLabel = 'SELESAI'; } 
            else if($statusFinal == 'ditolak') { $finalStatusClass = 'status-ditolak'; $statusLabel = 'DITOLAK'; } 
            else if($statusFinal == 'dibatalkan') { $finalStatusClass = 'status-dibatalkan'; $statusLabel = 'DIBATALKAN'; }
        @endphp
        <div class="status-box {{ $finalStatusClass }}">Status Final: {{ strtoupper($statusLabel) }}</div>
    </div>
</body>
</html>