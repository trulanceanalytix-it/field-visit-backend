<?php

namespace App\Imports;

set_time_limit(0);
ini_set('memory_limit', '512M');

use App\Models\DistributorMaster;
use App\Models\BeatMaster;
use App\Models\OutletMaster;
use App\Models\EmployeeBeatOutletMap;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class BeatOutletImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            // Trim all strings
            $row = collect($row)->map(function ($v) {
                return is_string($v) ? trim($v) : $v;
            });

            $distributor = DistributorMaster::firstOrCreate(
                [
                    'distributor_name' => $row['distributor_name'],
                    'state'            => $row['state'],
                ]
            );

            $beat = BeatMaster::firstOrCreate(
                [
                    'distributor_id' => $distributor->id,
                    'beat_name'      => $row['beat_name'],
                ]
            );

            $outlet = OutletMaster::firstOrCreate(
                [
                    'beat_id'     => $beat->id,
                    'outlet_name' => $row['outlet_name'],
                ]
            );

            EmployeeBeatOutletMap::updateOrCreate(
                [
                    'employee_id' => $row['tse_id'],
                    'beat_id'     => $beat->id,
                    'outlet_id'   => $outlet->id,
                ],
                [
                    'distributor_id' => $distributor->id,

                    'cm_id'          => $row['cm_id'],
                    'cm_name'        => $row['cm_name'],
                    'employee_name'  => $row['em_name'],
                    'district'       => $row['district'],
                    'town_name'      => $row['town_name'],
                    's_gr'           => $row['s_gr'],
                    'status'         => $row['status'],

                    'assigned_from'  => now()->toDateString(),
                    'assigned_to'    => null,
                ]
            );
        }
    }
}
