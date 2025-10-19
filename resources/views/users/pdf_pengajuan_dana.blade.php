<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengajuan Dana - #{{ $pengajuanDana->id }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .container { width: 100%; margin: 0 auto; }
        .header, .footer { text-align: center; }
        .header h1 { margin: 0; font-size: 24px; color: #003366; }
        .header p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .section-title {
            background-color: #eaf2f8;
            padding: 10px;
            font-size: 14px;
            font-weight: bold;
            margin-top: 20px;
            border-left: 4px solid #3498db;
        }
        .total-row td { font-weight: bold; font-size: 14px; }
        .total-amount { text-align: right; color: #e74c3c; }
        .status-box {
            padding: 10px;
            margin-top: 20px;
            border-radius: 4px;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
        }
        .status-disetujui { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status-ditolak { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .status-diproses { background-color: #cce5ff; color: #004085; border: 1px solid #b8daff; }
        .catatan {
            background: #f9f9f9;
            border-left: 3px solid #ccc;
            padding: 8px;
            margin-top: 5px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PT RAKHA NUSANTARA MEDIKA</h1>
            <p>Detail Laporan Pengajuan Dana</p>
            <p style="font-size: 10px;">Nomor Pengajuan: {{ str_pad($pengajuanDana->id, 4, '0', STR_PAD_LEFT) }}</p>
        </div>

        <div class="section-title">Informasi Pengajuan</div>
        <table>
            <tr>
                <th style="width: 30%;">Judul Pengajuan</th>
                <td>{{ $pengajuanDana->judul_pengajuan }}</td>
            </tr>
            <tr>
                <th>Diajukan Oleh</th>
                <td>{{ $pengajuanDana->user->name }} (Divisi: {{ $pengajuanDana->user->divisi }})</td>
            </tr>
            <tr>
                <th>Tanggal Pengajuan</th>
                <td>{{ $pengajuanDana->created_at->translatedFormat('l, d F Y - H:i') . ' WIB'}}</td>
            </tr>
             <tr>
                <th>Informasi Transfer</th>
                <td>{{ $pengajuanDana->nama_bank }} - {{ $pengajuanDana->no_rekening }}</td>
            </tr>
        </table>

        <div class="section-title">Rincian Dana</div>
        <table>
            <thead>
                <tr>
                    <th>Deskripsi</th>
                    <th style="width: 30%; text-align: right;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuanDana->rincian_dana as $rincian)
                <tr>
                    <td>{{ $rincian['deskripsi'] ?? 'N/A' }}</td>
                    <td style="text-align: right;">Rp {{ number_format($rincian['jumlah'] ?? 0, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="2">Tidak ada rincian.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td>TOTAL</td>
                    <td class="total-amount">Rp {{ number_format($pengajuanDana->total_dana, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="section-title">Alur Persetujuan</div>
        <table>
            @php
                $role = $pengajuanDana->user->is_kepala_divisi ? 'Direktur' : 'Atasan';
                $status_atasan = $pengajuanDana->user->is_kepala_divisi ? $pengajuanDana->status_direktur : $pengajuanDana->status_atasan;
                $catatan_atasan = $pengajuanDana->user->is_kepala_divisi ? $pengajuanDana->catatan_direktur : $pengajuanDana->catatan_atasan;
                $approver_atasan = $pengajuanDana->user->is_kepala_divisi ? $pengajuanDana->direkturApprover : $pengajuanDana->atasanApprover;
                $tanggal_atasan = $pengajuanDana->user->is_kepala_divisi ? $pengajuanDana->direktur_approved_at : $pengajuanDana->atasan_approved_at; // TAMBAHAN
            @endphp
            <tr>
                <th style="width: 30%;">Persetujuan {{ $role }}</th>
                <td>
                    <strong>Status: {{ ucfirst($status_atasan) }}</strong>
                    @if($approver_atasan)
                        <br><small>Oleh: {{ $approver_atasan->name }}</small>
                    @endif
                    {{-- TAMBAHAN: Tampilkan Tanggal --}}
                    @if($tanggal_atasan)
                        <br><small>Tanggal: {{ \Carbon\Carbon::parse($tanggal_atasan)->translatedFormat('d F Y, H:i'). ' WIB'}}</small>
                    @endif
                    @if($catatan_atasan)
                        <div class="catatan">"{{ $catatan_atasan }}"</div>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Persetujuan Finance</th>
                <td>
                    <strong>Status: {{ ucfirst($pengajuanDana->status_finance) }}</strong>
                     @if($pengajuanDana->financeApprover)
                        <br><small>Oleh: {{ $pengajuanDana->financeApprover->name }}</small>
                    @endif
                    {{-- TAMBAHAN: Tampilkan Tanggal --}}
                    @if($pengajuanDana->finance_approved_at)
                        <br><small>Tanggal: {{ \Carbon\Carbon::parse($pengajuanDana->finance_approved_at)->translatedFormat('d F Y, H:i') . ' WIB' }}</small>
                    @endif
                    @if($pengajuanDana->catatan_finance)
                        <div class="catatan">"{{ $pengajuanDana->catatan_finance }}"</div>
                    @endif
                </td>
            </tr>
        </table>
        
        @php
            $finalStatusClass = '';
            if($pengajuanDana->status == 'disetujui') $finalStatusClass = 'status-disetujui';
            else if($pengajuanDana->status == 'ditolak') $finalStatusClass = 'status-ditolak';
            else $finalStatusClass = 'status-diproses';
        @endphp
        <div class="status-box {{ $finalStatusClass }}">
            Status Final: {{ strtoupper($pengajuanDana->status) }}
        </div>

    </div>
</body>
</html>