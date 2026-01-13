<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class RekapAbsensiExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles, WithEvents
{
    protected $rekapData;
    protected $allDates;
    protected $startDate;
    protected $endDate;
    protected $totalDays;

    // Konfigurasi Start Kolom Tanggal (Karena ada No, Nama, Divisi, Jabatan => 4 kolom awal)
    // Kolom ke-5 adalah E
    protected $startDateColIndex = 5; 

    public function __construct($rekapData, $allDates, $startDate, $endDate)
    {
        $this->rekapData = collect($rekapData);
        $this->allDates = $allDates;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->totalDays = iterator_count($allDates);
    }

    public function collection()
    {
        return $this->rekapData;
    }

    /**
     * Mapping Data per Baris
     */
    public function map($row): array
    {
        static $no = 1;

        // 1. Data Harian (Looping Tanggal)
        $dailyData = [];
        foreach ($this->allDates as $date) {
            $val = $row['daily'][$date->toDateString()] ?? '-';
            $dailyData[] = ($val === '' || $val === null) ? '-' : $val;
        }

        // 2. Data Summary
        $summaryData = [
            $this->formatZero($row['summary']['H'] ?? 0),
            $this->formatZero($row['summary']['S'] ?? 0),
            $this->formatZero($row['summary']['I'] ?? 0),
            $this->formatZero($row['summary']['C'] ?? 0),
            $this->formatZero($row['summary']['A'] ?? 0),
            $this->formatZero($row['summary']['L'] ?? 0),
        ];

        // 3. Data Terlambat
        $terlambat = $row['summary']['terlambat_formatted'] ?? '-';
        if ($terlambat === '0 Jam 0 Menit') {
            $terlambat = '-';
        }

        return array_merge(
            [
                $no++,                          // A: No
                $row['user']->name ?? '-',      // B: Nama
                $row['user']->divisi ?? '-',    // C: Divisi
                $row['user']->jabatan ?? '-',   // D: Jabatan
            ],
            $dailyData,                         // E s/d ... : Tanggal
            $summaryData,                       // Summary
            [$terlambat]                        // Evaluasi
        );
    }

    private function formatZero($value)
    {
        return ($value === 0 || $value === '0') ? '-' : $value;
    }

    /**
     * Header Excel
     */
    public function headings(): array
    {
        // Baris 1: Nama PT
        $companyName = "PT RAKHA NUSANTARA MEDIKA";

        // Baris 2: Judul Laporan
        $title = "REKAP ABSENSI KARYAWAN - " . Carbon::parse($this->startDate)->isoFormat('MMMM YYYY');
        
        // Baris 3: Periode
        $period = "Periode: " . Carbon::parse($this->startDate)->format('d M Y') . " s/d " . Carbon::parse($this->endDate)->format('d M Y');

        // --- BARIS 5 (HEADER UTAMA / ATAS) ---
        // [PERBAIKAN] Isi judul kolom di Baris 5 agar tidak hilang saat di-merge
        $headerGroup = ['No', 'Nama Karyawan', 'Divisi', 'Jabatan']; 
        
        // Header Tanggal (Bulan)
        for ($i = 0; $i < $this->totalDays; $i++) {
            $headerGroup[] = ($i === 0) ? Carbon::parse($this->startDate)->isoFormat('MMMM YYYY') : '';
        }
        
        // Header Summary & Evaluasi
        // Tambahkan 'Rekap Kehadiran' + 5 sel kosong untuk merge
        $headerGroup = array_merge($headerGroup, ['Rekap Kehadiran', '', '', '', '', '']);
        $headerGroup[] = 'Evaluasi'; // Kolom terakhir baris 5

        // --- BARIS 6 (HEADER SUB / BAWAH) ---
        // Kosongkan 4 kolom pertama (karena sudah ada judul di baris 5 dan akan di-merge vertikal)
        $headerColumns = ['', '', '', ''];
        
        // Angka Tanggal (1, 2, 3...)
        foreach ($this->allDates as $date) {
            $headerColumns[] = $date->day;
        }
        
        // Kode Summary (H, S, I...)
        $headerColumns = array_merge($headerColumns, ['H', 'S', 'I', 'C', 'A', 'L']);
        
        // Sub-header Evaluasi
        $headerColumns[] = 'Terlambat';

        return [
            [$companyName], // Row 1
            [$title],       // Row 2
            [$period],      // Row 3
            [''],           // Row 4 (Spacer)
            $headerGroup,   // Row 5
            $headerColumns  // Row 6
        ];
    }

    /**
     * Atur Lebar Kolom
     */
    public function columnWidths(): array
    {
        $columns = [
            'A' => 5,  // No
            'B' => 30, // Nama
            'C' => 20, // Divisi
            'D' => 20, // Jabatan
        ];

        $currentColIndex = $this->startDateColIndex; // Mulai dari 5 (E)

        // Kolom Tanggal (Lebar 4)
        for ($i = 0; $i < $this->totalDays; $i++) {
            $colString = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentColIndex);
            $columns[$colString] = 4;
            $currentColIndex++;
        }

        // Kolom Summary (Lebar 5)
        for ($j = 0; $j < 6; $j++) {
            $colString = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentColIndex);
            $columns[$colString] = 5;
            $currentColIndex++;
        }

        // Kolom Terlambat
        $colString = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentColIndex);
        $columns[$colString] = 25;

        return $columns;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]], // PT Name
            2 => ['font' => ['bold' => true, 'size' => 12]], // Judul Laporan
            3 => ['font' => ['bold' => true, 'size' => 10]], // Periode
            5 => ['font' => ['bold' => true]], // Header Group
            6 => ['font' => ['bold' => true]], // Header Columns
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Hitung Kolom Terakhir
                // A-D (4) + Dates + Summary (6) + Terlambat (1)
                $totalCols = 4 + $this->totalDays + 6 + 1;
                $lastColStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalCols);
                $lastRow = $sheet->getHighestRow();

                // 1. Merge Header Atas
                $sheet->mergeCells("A1:{$lastColStr}1"); // PT
                $sheet->mergeCells("A2:{$lastColStr}2"); // Judul
                $sheet->mergeCells("A3:{$lastColStr}3"); // Periode

                // Center Align Header Atas
                $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // 2. Merge Header Tabel (Row 5 & 6)
                // Merge Vertikal untuk No, Nama, Divisi, Jabatan
                // Karena judulnya ada di Row 5, maka Row 5 akan tampil (benar)
                $sheet->mergeCells('A5:A6');
                $sheet->mergeCells('B5:B6');
                $sheet->mergeCells('C5:C6');
                $sheet->mergeCells('D5:D6');

                // Merge Horizontal "Bulan ..."
                $startDateCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($this->startDateColIndex);
                $endDateCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($this->startDateColIndex + $this->totalDays - 1);
                $sheet->mergeCells("{$startDateCol}5:{$endDateCol}5");

                // Merge Horizontal "Rekap Kehadiran"
                $startSumColIndex = $this->startDateColIndex + $this->totalDays;
                $startSumCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startSumColIndex);
                $endSumCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startSumColIndex + 5);
                $sheet->mergeCells("{$startSumCol}5:{$endSumCol}5");

                // 3. Border Table (Mulai Row 5)
                $styleBorder = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ];
                $sheet->getStyle("A5:{$lastColStr}{$lastRow}")->applyFromArray($styleBorder);

                // 4. Alignment & Styling Header Tabel
                $sheet->getStyle("A5:{$lastColStr}6")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A5:{$lastColStr}6")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFEEEEEE'); // Abu-abu muda

                // 5. Alignment Data
                // Nama & Divisi & Jabatan Align Left (Mulai B7)
                $sheet->getStyle("B7:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                // Sisanya Center (No, Tanggal, Summary)
                $sheet->getStyle("A7:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("{$startDateCol}7:{$lastColStr}{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // 6. Coloring Status (S, I, C, A, L)
                for ($row = 7; $row <= $lastRow; $row++) {
                    for ($col = $this->startDateColIndex; $col < $this->startDateColIndex + $this->totalDays; $col++) {
                        $colStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                        $cellValue = $sheet->getCell("{$colStr}{$row}")->getValue();

                        $color = null;
                        if ($cellValue === 'S') $color = 'FFFF0000'; // Merah
                        elseif ($cellValue === 'I') $color = 'FFFFA500'; // Orange
                        elseif ($cellValue === 'C') $color = 'FF0000FF'; // Biru
                        elseif ($cellValue === 'A') $color = 'FF000000'; // Hitam
                        elseif ($cellValue === 'L') $color = 'FF800080'; // Ungu

                        if ($color) {
                            $sheet->getStyle("{$colStr}{$row}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color($color));
                            $sheet->getStyle("{$colStr}{$row}")->getFont()->setBold(true);
                        }
                    }
                }
            },
        ];
    }
}