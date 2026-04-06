<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Absensi;
use App\Http\Controllers\Web\SkorController;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SkorExport implements FromArray, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    protected int $bulan;
    protected int $tahun;
    protected ?string $unit;

    public function __construct(int $bulan, int $tahun, ?string $unit = null)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->unit  = $unit;
    }

    public function array(): array
    {
        $skorController = new SkorController();

        $users = User::where('role', 'pegawai')
            ->when($this->unit, fn($q) => $q->where('unit', $this->unit))
            ->orderBy('unit')->orderBy('name')
            ->get();

        // Eager load all absensis for relevant users in one query
        $allAbsensis = Absensi::with('shift')
            ->whereIn('user_id', $users->pluck('id'))
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->get()
            ->groupBy('user_id');

        $rows = [];
        foreach ($users as $i => $user) {
            $userAbsensis = $allAbsensis->get($user->id, collect());
            $skor = $skorController->hitungSkor($user, $this->bulan, $this->tahun, $userAbsensis);

            $rows[] = [
                $i + 1,
                $user->name,
                $user->nip ?? '-',
                $user->unit ?? '-',
                $skor['total_hadir'],
                $skor['detail']['KT1']['kali'],
                $skor['detail']['KT2']['kali'],
                $skor['detail']['KT3']['kali'],
                $skor['detail']['KT4']['kali'],
                $skor['detail']['KT5']['kali'],
                $skor['detail']['KT6']['kali'],
                $skor['total_potongan'] . '%',
                $skor['skor_akhir'],
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        $namaBulan = Carbon::create($this->tahun, $this->bulan)->locale('id')->isoFormat('MMMM YYYY');
        return [
            ['REKAPITULASI SKOR KEHADIRAN PEGAWAI'],
            [($this->unit ? 'Unit: ' . $this->unit . ' | ' : '') . 'Periode: ' . $namaBulan],
            [],
            [
                'No',
                'Nama Pegawai',
                'NIP',
                'Unit',
                'Hari Hadir',
                'KT1 (1-30 mnt)',
                'KT2 (31-60 mnt)',
                'KT3 (61-90 mnt)',
                'KT4 (>90 mnt)',
                'KT5 (Tdk Checkout)',
                'KT6 (Alpha)',
                'Total Potongan',
                'Skor Akhir',
            ],
        ];
    }

    public function title(): string
    {
        return 'Rekap Skor ' . Carbon::create($this->tahun, $this->bulan)->format('M Y');
    }

    public function styles(Worksheet $sheet): array
    {
        // Merge title rows
        $sheet->mergeCells('A1:M1');
        $sheet->mergeCells('A2:M2');
        $sheet->mergeCells('A3:M3');

        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            2 => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            4 => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 30,
            'C' => 20,
            'D' => 20,
            'E' => 10,
            'F' => 16,
            'G' => 16,
            'H' => 16,
            'I' => 16,
            'J' => 16,
            'K' => 10,
            'L' => 15,
            'M' => 12,
        ];
    }
}
