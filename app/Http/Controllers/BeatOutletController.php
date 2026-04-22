<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Models\BeatOutletMaster;
use App\Imports\BeatOutletImport;
use Illuminate\Http\Request;

class BeatOutletController extends Controller
{
    public function store(Request $request)
    {
        // 🔹 1. Validation
        $validated = $request->validate([
            'tse_id'                => 'required|exists:employee_masters,emp_id',
            'tse_name'              => 'required|string',
            'cluster_manager_id'    => 'required|string',
            'cluster_manager_name'  => 'required|string',
            'beat_name'             => 'required|string',
            'distributor_name'      => 'required|string',
            'outlet_name'           => 'required|string|max:255',

            // New fields
            'floors'        => 'nullable|integer|min:0',
            'total_sft'     => 'nullable|numeric|min:0',
            'nearby_shops'  => 'nullable|string|max:255',

            // Images
            'signage'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'image_1'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'image_2'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        // 🔒 2. Ensure employee context exists
        $contextExists = BeatOutletMaster::where('tse_id', $validated['tse_id'])
            ->where('beat_name', $validated['beat_name'])
            ->where('distributor_name', $validated['distributor_name'])
            ->exists();

        if (!$contextExists) {
            return response()->json([
                'message' => 'Invalid employee / beat / distributor context'
            ], 422);
        }

        // ❌ 3. Prevent duplicate outlet
        $duplicate = BeatOutletMaster::where('tse_id', $validated['tse_id'])
            ->where('beat_name', $validated['beat_name'])
            ->where('outlet_name', $validated['outlet_name'])
            ->exists();

        if ($duplicate) {
            return response()->json([
                'message' => 'Outlet already exists for this beat'
            ], 409);
        }

        // 📁 4. Handle image uploads
        $imagePaths = [];

        foreach (['signage', 'image_1', 'image_2'] as $field) {
            if ($request->hasFile($field)) {
                $imagePaths[$field] = $request->file($field)
                    ->store('outlets/' . $validated['tse_id'], 'public');
            }
        }

        // 💾 5. Store outlet
        BeatOutletMaster::create([
            'tse_id'                => $validated['tse_id'],
            'tse_name'              => $validated['tse_name'],
            'cluster_manager_id'    => $validated['cluster_manager_id'],
            'cluster_manager_name'  => $validated['cluster_manager_name'],
            'outlet_name'           => $validated['outlet_name'],
            'beat_name'             => $validated['beat_name'],
            'distributor_name'      => $validated['distributor_name'],

            // New fields
            'floors'        => $validated['floors'] ?? null,
            'total_sft'     => $validated['total_sft'] ?? null,
            'nearby_shops'  => $validated['nearby_shops'] ?? null,

            // Images
            'signage_image' => $imagePaths['signage'] ?? null,
            'image_1'       => $imagePaths['image_1'] ?? null,
            'image_2'       => $imagePaths['image_2'] ?? null,

            'status'           => 'ACTIVE',
            'beat_name_change' => null,
            'remarks'          => 'New outlet added via field visit',
        ]);

        return response()->json([
            'message' => 'Outlet added successfully'
        ]);
    }

    public function importBeatOutlet(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new BeatOutletImport, $request->file('file'));

        return back()->with('success', 'Excel imported successfully');
    }
}
