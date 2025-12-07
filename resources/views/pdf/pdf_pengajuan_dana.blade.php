<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pengajuan Dana - {{ str_pad($pengajuanDana->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        /* Gaya Inti dari File Referensi (Pola) */
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; line-height: 1.5; }
        .container { width: 95%; margin: 15px auto; }
        
        .header { text-align: center; margin-bottom: 15px; }
        .header h1 { margin: 0; font-size: 20px; color: #003366; font-weight: bold; } /* Ukuran disesuaikan sedikit */
        .header p { margin: 4px 0; font-size: 12px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ddd; padding: 7px; text-align: left; vertical-align: top; word-wrap: break-word; } /* Border #ddd, padding sedikit disesuaikan */
        
        th { background-color: #f2f2f2; font-weight: bold; font-size: 11px; }

        /* Judul Bagian (Section Title) Biru dari File Referensi */
        .section-title {
            background-color: #eaf2f8;
            padding: 9px;
            font-size: 13px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 10px;
            border-left: 4px solid #3498db;
            color: #003366;
        }

        /* Status Box Final dari File Referensi */
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
        
        /* Gaya Spesifik dari File Formulir (Target) yang Dipertahankan */
        .detail-table td:first-child { width: 22%; font-weight: bold; background-color: #fafafa; }
        .rincian-table th:last-child, .rincian-table td:last-child { text-align: right; width: 25%; }
        .rincian-table .total-row { font-weight: bold; background-color: #f5f5f5; }
        .rincian-table .total-row td { font-size: 12px; }

        /* Tanda Tangan (Signatures) - Disesuaikan jadi 3 kolom */
        .signatures { width: 100%; margin-top: 20px; border: none; table-layout: fixed; }
        .signatures td { 
            width: 33.33%; /* Diubah dari 25% menjadi 33.33% */
            border: none; 
            text-align: center; 
            vertical-align: top; 
            padding: 5px; 
        }
        .signatures p { margin: 0; line-height: 1.3; }
        .signatures .placeholder { 
            height: 50px; 
            margin: 8px 0 4px 0; 
            color: #aaa; 
            font-style: italic; 
            font-size: 9px; 
            line-height: 50px; 
            border-bottom: 1px dotted #ccc; 
        }
        .signatures .nama { font-weight: bold; margin-top: 4px; font-size: 11px; }
        .signatures .jabatan { font-size: 10px; color: #555; }
        .signatures .tanggal { font-size: 9px; color: #777; margin-top: 2px; }

        /* Catatan (jika ada) */
        .catatan {
            background: #f9f9f9;
            border-left: 3px solid #ccc;
            padding: 8px;
            margin-top: 5px;
            font-style: italic;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="container">

        <div class="header">
            <h1>PT RAKHA NUSANTARA MEDIKA</h1>
            <p style="font-weight: bold;">FORMULIR PENGAJUAN DANA</p>
            <p style="font-size: 10px; font-weight: bold;">Nomor Pengajuan: {{ str_pad($pengajuanDana->id, 4, '0', STR_PAD_LEFT) }}</p>
        </div>

        <div class="content">
            
            <div class="section-title">I. DETAIL PENGAJUAN</div>
            <table class="detail-table">
                {{-- PERBAIKAN: Menambahkan format translatedFormat 'l, d F Y' --}}
                <tr><td>Tanggal Pengajuan</td><td>{{ $pengajuanDana->created_at->translatedFormat('l, d F Y') }}</td></tr>
                <tr><td>Pemohon</td><td>{{ $pengajuanDana->user?->name ?? '-' }}</td></tr>
                <tr><td>Divisi</td><td>{{ $pengajuanDana->divisi ?? '-' }}</td></tr>
                <tr><td>Jabatan</td><td>{{ $pengajuanDana->user?->jabatan ?? '-' }}</td></tr>
                <tr><td>Judul Pengajuan</td><td>{{ $pengajuanDana->judul_pengajuan }}</td></tr>
            </table>

            <div class="section-title">II. INFORMASI REKENING & DANA</div>
            <table class="detail-table">
                <tr><td>Bank Tujuan</td><td>{{ $pengajuanDana->nama_bank }}</td></tr>
                <tr><td>Nomor Rekening</td><td>{{ $pengajuanDana->no_rekening }}</td></tr>
            </table>

            <table class="rincian-table">
                <thead><tr><th>Rincian Kebutuhan</th><th>Jumlah (Rp)</th></tr></thead>
                <tbody>
                    @if($pengajuanDana->rincian_dana && is_array($pengajuanDana->rincian_dana))
                        @forelse ($pengajuanDana->rincian_dana as $item)
                            <tr><td>{{ $item['deskripsi'] ?? '-' }}</td><td>{{ number_format($item['jumlah'] ?? 0, 0, ',', '.') }}</td></tr>
                        @empty
                            <tr><td colspan="2" style="text-align: center;">Tidak ada rincian.</td></tr>
                        @endforelse
                    @else
                        <tr><td colspan="2" style="text-align: center;">Data rincian tidak valid.</td></tr>
                    @endif
                </tbody>
                <tfoot><tr class="total-row"><td style="text-align: right; padding-right: 10px;">TOTAL DANA DIAJUKAN</td><td>Rp {{ number_format($pengajuanDana->total_dana, 0, ',', '.') }}</td></tr></tfoot>
            </table>
            
            <div class="section-title">III. PERSETUJUAN</div>
            <table class="signatures">
                <tr>
                    <td>
                        <p>Disetujui oleh (Approver 1),</p>
                        @if($pengajuanDana->approver_1_status == 'disetujui')
                            <div class="placeholder">Disetujui via Sistem</div>
                            <p class="nama">{{ $pengajuanDana->approver1?->name ?? '-' }}</p>
                            <p class="jabatan">{{ $pengajuanDana->approver1?->jabatan ?? '-' }}</p>
                            <p class="tanggal">{{ $pengajuanDana->approver_1_approved_at?->format('d M Y H:i') }}</p>
                        @else
                            <div class="placeholder">({{ ucfirst($pengajuanDana->approver_1_status) }})</div>
                            <p class="nama">{{ $pengajuanDana->approver1?->name ?? '-' }}</p>
                            <p class="jabatan">{{ $pengajuanDana->approver1?->jabatan ?? '-' }}</p>
                        @endif
                    </td>
                    <td>
                        <p>Disetujui oleh (Approver 2),</p>
                        @if($pengajuanDana->approver_2_status == 'disetujui')
                            <div class="placeholder">Disetujui via Sistem</div>
                            <p class="nama">{{ $pengajuanDana->approver2?->name ?? '-' }}</p>
                            <p class="jabatan">{{ $pengajuanDana->approver2?->jabatan ?? '-' }}</p>
                            <p class="tanggal">{{ $pengajuanDana->approver_2_approved_at?->format('d M Y H:i') }}</p>
                        @else
                            <div class="placeholder">({{ ucfirst($pengajuanDana->approver_2_status) }})</div>
                            <p class="nama">{{ $pengajuanDana->approver2?->name ?? '-' }}</p>
                            <p class="jabatan">{{ $pengajuanDana->approver2?->jabatan ?? '-' }}</p>
                        @endif
                    </td>
                    <td>
                        <p>Diproses oleh (Finance),</p>
                        @if($pengajuanDana->payment_status == 'diproses' || $pengajuanDana->payment_status == 'selesai')
                             <div class="placeholder">
                                 {{ $pengajuanDana->payment_status == 'selesai' ? 'Dibayarkan via Sistem' : 'Diproses via Sistem' }}
                             </div>
                            <p class="nama">{{ $pengajuanDana->financeProcessor?->name ?? '-' }}</p>
                            <p class="jabatan">{{ $pengajuanDana->financeProcessor?->jabatan ?? 'Finance' }}</p>
                            <p class="tanggal">{{ ($pengajuanDana->payment_status == 'selesai' ? $pengajuanDana->updated_at : $pengajuanDana->finance_processed_at)?->format('d M Y H:i') }}</p>
                        @else
                            <div class="placeholder">({{ ucfirst($pengajuanDana->payment_status) }})</div>
                            <p class="nama">({{ $pengajuanDana->user->managerKeuangan?->name ?? 'Finance Belum Ditugaskan' }})</p>
                            <p class="jabatan">{{ $pengajuanDana->user->managerKeuangan?->jabatan ?? 'Finance' }}</p>
                        @endif
                    </td>
                </tr>
            </table>

            {{-- Catatan Tambahan --}}
             @if($pengajuanDana->approver_1_catatan || $pengajuanDana->approver_2_catatan || $pengajuanDana->catatan_finance)
                <div class="section-title">IV. CATATAN</div>
                <table class="detail-table">
                    @if($pengajuanDana->approver_1_catatan)
                        <tr><td>Catatan Approver 1</td><td><div class="catatan">{{ $pengajuanDana->approver_1_catatan }}</div></td></tr>
                    @endif
                    @if($pengajuanDana->approver_2_catatan)
                        <tr><td>Catatan Approver 2</td><td><div class="catatan">{{ $pengajuanDana->approver_2_catatan }}</div></td></tr>
                    @endif
                    @if($pengajuanDana->catatan_finance)
                        <tr><td>Catatan Finance</td><td><div class="catatan">{{ $pengajuanDana->catatan_finance }}</div></td></tr>
                    @endif
                </table>
            @endif
        </div>

        @php
            // PERBAIKAN: Menambahkan strtolower() untuk memastikan perbandingan
            $statusFinal = strtolower($pengajuanDana->status ?? 'diproses');
            
            $finalStatusClass = 'status-diproses'; // Default (Biru/Abu)
            if($statusFinal == 'disetujui') {
                $finalStatusClass = 'status-disetujui'; // Hijau
            } else if($statusFinal == 'ditolak') {
                $finalStatusClass = 'status-ditolak'; // Merah
            }
        @endphp
        <div class="status-box {{ $finalStatusClass }}">
            Status Final: {{ strtoupper($statusFinal) }}
        </div>

    </div>
</body>
</html>