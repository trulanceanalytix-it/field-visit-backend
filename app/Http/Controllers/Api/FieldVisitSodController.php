<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FieldVisitSodLog;

class FieldVisitSodController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'emp_id' => 'required',
            'emp_name' => 'required',
            'sod_time' => 'required',
            'sod_date' => 'required',
        ]);

        // Prevent duplicate SOD per day
        $alreadyExists = FieldVisitSodLog::where('emp_id', $request->emp_id)
            ->where('sod_date', $request->sod_date)
            ->exists();

        if ($alreadyExists) {
            return response()->json([
                'status' => false,
                'message' => 'SOD already marked today'
            ], 409);
        }

       $selfiePath = null;

        if ($request->hasFile('selfie_image')) {

            $selfiePath = $request
                ->file('selfie_image')
                ->store('visit_selfies/sod', 'public');
        }

        $sod = FieldVisitSodLog::create([
            'emp_id' => $request->emp_id,
            'emp_name' => $request->emp_name,

            'sod_date' => $request->sod_date,
            'sod_time' => $request->sod_time,

            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,

            'location_accuracy' => $request->location_accuracy,

            'selfie_image' => $selfiePath,

            'device_name' => $request->device_name,
            'app_version' => $request->app_version,

            'is_mock_location' => $request->is_mock_location ?? false,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'SOD saved successfully',
            'data' => $sod
        ]);
    }
    public function checkSod(Request $request)
    {
        $request->validate([
            'emp_id' => 'required',
            'date'   => 'required|date',
        ]);

        $sod = FieldVisitSodLog::where(
                'emp_id',
                $request->emp_id
            )
            ->whereDate(
                'sod_time',
                $request->date
            )
            ->latest()
            ->first();

        if ($sod) {

            return response()->json([
                'status'   => true,
                'sod_done' => true,
                'sod_time' => \Carbon\Carbon::parse(
                    $sod->sod_time
                )->format('h:i A'),
            ]);
        }

        return response()->json([
            'status'   => true,
            'sod_done' => false,
        ]);
    }
}