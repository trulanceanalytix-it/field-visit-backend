<?php

namespace App\Exports;

use App\Models\FieldVisitEntry;
use App\Models\Remark;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize
};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DailyVisitsExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithColumnFormatting
{
    private $remarksMap;
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->remarksMap = Remark::pluck('remark', 'id')->toArray();
    }

    public function collection()
    {
        return FieldVisitEntry::with(['distributor', 'beat', 'outlet'])

            ->when(!empty($this->filters['emp_id']), function ($q) {
                $q->where(
                    'emp_id',
                    'ilike',
                    '%' . trim($this->filters['emp_id']) . '%'
                );
            })

            ->when(!empty($this->filters['emp_name']), function ($q) {
                $q->where(
                    'emp_name',
                    'ilike',
                    '%' . trim($this->filters['emp_name']) . '%'
                );
            })

            ->when(!empty($this->filters['date_from']), function ($q) {
                $q->whereDate(
                    'visited_date',
                    '>=',
                    $this->filters['date_from']
                );
            })

            ->when(!empty($this->filters['date_to']), function ($q) {
                $q->whereDate(
                    'visited_date',
                    '<=',
                    $this->filters['date_to']
                );
            })

            ->when(!empty($this->filters['distributor']), function ($q) {
                $q->whereHas('distributor', function ($dq) {
                    $dq->where(
                        'distributor_name',
                        'ilike',
                        '%' . trim($this->filters['distributor']) . '%'
                    );
                });
            })

            ->when(!empty($this->filters['beat']), function ($q) {
                $q->whereHas('beat', function ($bq) {
                    $bq->where(
                        'beat_name',
                        'ilike',
                        '%' . trim($this->filters['beat']) . '%'
                    );
                });
            })

            ->when(!empty($this->filters['outlet']), function ($q) {
                $q->whereHas('outlet', function ($oq) {
                    $oq->where(
                        'outlet_name',
                        'ilike',
                        '%' . trim($this->filters['outlet']) . '%'
                    );
                });
            })

            ->orderBy('visited_date', 'desc')
            ->orderBy('visited_at', 'desc')

            ->get();
    }
    public function headings(): array
    {
        return [
            'Timestamp',
            'Emp ID',
            'Emp Name',
            'Distributor Name',
            'Beat Visited',
            'Outlet Name Visited',
            'New Outlet',
            'Grade',
            'Leggings Qty',
            'Non Leggings Qty',
            'InnerWear Qty',
            'Total Qty Sold',
            'Remark',
            'Observation'
        ];
    }

    public function map($visit): array
    {
        $remarkText = '';

        if (is_array($visit->remark)) {
            $texts = [];
            foreach ($visit->remark as $id) {
                if (isset($this->remarksMap[$id])) {
                    $texts[] = $this->remarksMap[$id];
                }
            }
            $remarkText = implode(', ', $texts);
        } elseif (!empty($visit->remark)) {
            $remarkText = $this->remarksMap[$visit->remark] ?? $visit->remark;
        }

        return [
            $visit->visited_at
            ? Date::dateTimeToExcel($visit->visited_at)
            : null,

            $visit->emp_id,
            $visit->emp_name,

            $visit->distributor->distributor_name ?? '',
            $visit->beat->beat_name ?? '',
            $visit->outlet->outlet_name ?? '',

            '',
            '',

            $visit->leggings_qty ?? 0,
            $visit->non_leggings_qty ?? 0,
            $visit->innerwear_qty ?? 0,
            $visit->total_pcs ?? 0,

            $remarkText,
            $visit->observation ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            \Maatwebsite\Excel\Events\AfterSheet::class => function ($event) {
                $event->sheet->freezePane('A2');
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_DATETIME,
        ];
    }
}
