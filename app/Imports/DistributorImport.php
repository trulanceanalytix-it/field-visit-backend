<?php

namespace App\Imports;

use App\Models\DistributorMaster;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DistributorImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Trim ALL string values in the row
        $row = array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $row);

        // Mandatory field guard
        if (empty($row['customername'])) {
            return null; // skip bad rows safely
        }

        DistributorMaster::updateOrCreate(
            ['cid' => $row['cid']],
            [
                'distributor_name' => $row['customername'],

                'address1' => $row['address1'] ?? null,
                'address2' => $row['address2'] ?? null,
                'address3' => $row['address3'] ?? null,
                'district' => $row['district'] ?? null,

                'state'      => $row['state'] ?? null,
                'state_code' => $row['statecode'] ?? null,
                'pincode'    => $row['pincode'] ?? null,

                'phone'  => $row['phone'] ?? null,
                'mobile' => $row['mobile'] ?? null,
                'email'  => $row['email'] ?? null,

                'gstin'     => $row['gstin'] ?? null,
                'is_active' => filter_var($row['isactive'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ]
        );

        return null;
    }
}
