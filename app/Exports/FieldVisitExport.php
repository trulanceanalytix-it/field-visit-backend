<?php

namespace App\Exports;

use App\Models\FieldVisitEntry;
use App\Models\Remark;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;


class FieldVisitExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    protected $date;

    public function __construct($date = null)
    {
        $this->date = $date;
    }

    public function collection()
    {
        $empId = Auth::user()->emp_id;
        $remarksMap = Remark::pluck('remark', 'id')->toArray();

        $query = FieldVisitEntry::where('emp_id', $empId);

        if ($this->date) {
            $query->whereDate('visited_at', $this->date);
        }

        return $query
            ->orderBy('visited_at', 'desc')
            ->get()
            ->map(function ($row) use ($remarksMap) {

                $remarkIds = is_string($row->remark)
                    ? json_decode($row->remark, true)
                    : $row->remark;

                $remarkNames = collect($remarkIds ?? [])
                    ->map(fn($id) => $remarksMap[$id] ?? '')
                    ->filter()
                    ->implode(', ');

                return [
                    Carbon::parse($row->visited_at)->format('d-m-Y'),
                    $row->beat_name,
                    $row->distributor_name,
                    $row->outlet_name,
                    $row->leggings_qty,
                    $row->non_leggings_qty,
                    $row->innerwear_qty,
                    $row->total_pcs,
                    $remarkNames,
                    $row->observation,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Visited Date',
            'Beat',
            'Distributor',
            'Outlet',
            'L',
            'NL',
            'IW',
            'TOT',
            'Remarks',
            'Observation',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'E7F1FF'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ],

            // Table borders for all cells
            "A1:{$highestColumn}{$highestRow}" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Freeze header row
                $event->sheet->freezePane('A2');
            },
        ];
    }
}
