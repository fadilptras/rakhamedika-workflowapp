<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class RekapAbsensiExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithEvents
{
    protected $rekapData;
    protected $allDates;
    protected $startDate;
    protected $endDate;
    private $summaryHeadings = ['H', 'S', 'I', 'C', 'A', 'L']; // Untuk kemudahan perhitungan

    public function __construct(array $rekapData, CarbonPeriod $allDates, string $startDate, string $endDate)
    {
        $this->rekapData = collect($rekapData);
        $this->allDates = $allDates;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return $this->rekapData;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($this->headings()));
                $totalRows = count($this->rekapData) + 4; // 4 adalah jumlah baris header

                // === 1. MEMBUAT HEADER UTAMA ===
                $sheet->insertNewRowBefore(1, 3);
                $sheet->mergeCells('A1:'.$lastColumn.'1')->setCellValue('A1', 'REKAP ABSENSI KARYAWAN');
                $sheet->mergeCells('A2:'.$lastColumn.'2')->setCellValue('A2', 'PT RAKHA MEDIKA NUSANTARA');
                $periode = Carbon::parse($this->startDate)->isoFormat('D MMMM YYYY') . ' - ' . Carbon::parse($this->endDate)->isoFormat('D MMMM YYYY');
                $sheet->mergeCells('A3:'.$lastColumn.'3')->setCellValue('A3', 'Periode: ' . $periode);
                $sheet->getStyle('A1:A3')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getRowDimension('1')->setRowHeight(20);

                // === 2. STYLE HEADER TABEL (sekarang di baris ke-4) ===
                $headerRange = 'A4:' . $lastColumn . '4';
                $sheet->getStyle($headerRange)->getFont()->setBold(true);
                $sheet->getStyle($headerRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('E2E8F0');
                $sheet->getStyle($headerRange)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);

                // === 3. STYLE SELURUH TABEL DATA (dari A4 sampai akhir) ===
                $fullTableRange = 'A4:' . $lastColumn . $totalRows;
                $sheet->getStyle($fullTableRange)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle($fullTableRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B5:D'.$totalRows)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle($fullTableRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // === 4. WARNAI HARI LIBUR (WEEKEND) ===
                $startColumnIndex = 5; // Kolom tanggal dimulai dari E
                foreach ($this->allDates as $index => $date) {
                    if ($date->isWeekend()) {
                        $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startColumnIndex + $index);
                        $sheet->getStyle($columnLetter . '4:' . $columnLetter . $totalRows)->getFill()
                              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                              ->getStartColor()->setARGB('F3F4F6');
                    }
                }
            },
        ];
    }

    public function headings(): array
    {
        $dateHeadings = [];
        foreach ($this->allDates as $date) {
            $dateHeadings[] = $date->day;
        }

        return array_merge(
            ['No.', 'Nama Karyawan', 'Posisi', 'Divisi'],
            $dateHeadings,
            $this->summaryHeadings,
            ['Total Terlambat']
        );
    }

    /**
     * Memetakan data ke setiap baris dengan jaminan angka 0.
     */
    public function map($row): array
    {
        static $index = 0;
        $index++;

        $dailyData = [];
        foreach ($this->allDates as $date) {
            $statusString = $row['daily'][$date->toDateString()] ?? '-';
            $mainStatus = trim(str_replace('L', '', $statusString));
            $dailyData[] = $date->isWeekend() ? '' : $mainStatus;
        }

        return array_merge(
            [
                $index,
                $row['user']->name ?? 'User Dihapus',
                $row['user']->jabatan ?? '-',
                $row['user']->divisi ?? '-',
            ],
            $dailyData,
            [
                (int) ($row['summary']['H'] ?? 0),
                (int) ($row['summary']['S'] ?? 0),
                (int) ($row['summary']['I'] ?? 0),
                (int) ($row['summary']['C'] ?? 0),
                (int) ($row['summary']['A'] ?? 0),
                (int) ($row['summary']['L'] ?? 0),
                $row['summary']['terlambat_formatted'] ?? '0 Jam 0 Menit',
            ]
        );
    }

    public function columnWidths(): array
    {
        $dateColumns = [];
        $startColumnIndex = 5;
        for ($i = 0; $i < $this->allDates->count(); $i++) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startColumnIndex + $i);
            $dateColumns[$columnLetter] = 5;
        }

        $summaryColumns = [];
        $startSummaryIndex = $startColumnIndex + $this->allDates->count();
        for ($i = 0; $i < count($this->summaryHeadings); $i++) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startSummaryIndex + $i);
            $summaryColumns[$columnLetter] = 8;
        }
        
        $lastColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startSummaryIndex + count($this->summaryHeadings));
        $summaryColumns[$lastColumnLetter] = 20;

        return array_merge([
            'A' => 5,
            'B' => 35,
            'C' => 25,
            'D' => 20,
        ], $dateColumns, $summaryColumns);
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }
}