<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientAnnualExport implements FromView, ShouldAutoSize, WithColumnFormatting, WithStyles
{
    protected $client;
    protected $recap;
    protected $year;
    protected $totals;

    public function __construct($client, $recap, $year, $totals)
    {
        $this->client = $client;
        $this->recap = $recap;
        $this->year = $year;
        $this->totals = $totals;
    }

    public function view(): View
    {
        return view('exports.client_recap', [
            'client' => $this->client,
            'recap' => $this->recap,
            'year' => $this->year,
            'yearlyTotals' => $this->totals
        ]);
    }

    /**
     * 1. FORMAT ANGKA (PENTING!)
     * Tetap biarkan ini seperti semula (Format Global Kolom)
     */
    public function columnFormats(): array
    {
        return [
            'B' => '#,##0', // Sales (In)
            'D' => '#,##0', // Value (Net)
            'E' => '#,##0', // Usage (Out) -> Ini yang bikin No Rekening ada koma
            'F' => '#,##0', // Saldo
        ];
    }

    /**
     * 2. STYLING & OVERRIDE
     * Kita tambahkan perbaikan No. Rekening di sini.
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // --- FIX KHUSUS NO REKENING (SEL E6) ---
            // Kita paksa sel E6 jadi TEXT agar format '#,##0' dari columnFormats diabaikan.
            'E6' => ['numberFormat' => ['formatCode' => NumberFormat::FORMAT_TEXT]],

            // --- STYLE LAINNYA TETAP SAMA SEPERTI KODE ANDA ---
            
            // Baris 1-2 (Judul) Bold
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true, 'size' => 12]],
            
            // Baris 4 (Header Section Info) Bold
            4 => ['font' => ['bold' => true, 'color' => ['rgb' => '0000FF']]], 
            
            // Baris 13 (Header Tabel Data) Bold & Background Abu
            13 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'EEEEEE'],
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                ],
            ],
        ];
    }
}