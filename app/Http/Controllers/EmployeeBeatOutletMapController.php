<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployeeBeatOutletMap;
use App\Models\Employee;
use App\Models\BeatMaster;
use App\Models\OutletMaster;
use App\Models\DistributorMaster;
use Yajra\DataTables\DataTables;

class EmployeeBeatOutletMapController extends Controller
{
    /**
     * Display all mappings (index page)
     */
    public function index()
    {
        $employees = Employee::select('emp_id', 'emp_name')->get();

        $distributors = DistributorMaster::select('id', 'distributor_name')->get();

        $beats = BeatMaster::select('id', 'beat_name', 'distributor_id')->get();

        $outlets = OutletMaster::select('id', 'outlet_name', 'beat_id')
            ->where('status', 'ACTIVE')
            ->get();

        $mappings = EmployeeBeatOutletMap::with([
            'employee:emp_id,emp_name',
            'beat:id,beat_name',
            'outlet:id,outlet_name',
            'distributor:id,distributor_name'
        ])->where('status', 'ACTIVE')->get();

        return view('admin.employee-maps.index', compact(
            'mappings',
            'employees',
            'distributors',
            'beats',
            'outlets'
        ));
    }


    /**
     * Store a new mapping (from modal)
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id'   => 'required|exists:employee_masters,emp_id',
            'beat_id'       => 'required|exists:beat_master,id',
            'outlet_id'     => 'required|exists:outlet_master,id',
            'distributor_id' => 'required|exists:distributor_master,id',
            'assigned_from' => 'nullable|date',
            'assigned_to'   => 'nullable|date',
            'status'        => 'nullable|string|in:ACTIVE,INACTIVE'
        ]);

        EmployeeBeatOutletMap::create($request->all());

        return redirect()->route('admin.employee-maps.index')
            ->with('success', 'Mapping created successfully');
    }
    // public function edit($id)
    // {
    //     $map = EmployeeBeatOutletMap::with([
    //         'beat:id,beat_name,distributor_id',
    //         'outlet:id,outlet_name,beat_id'
    //     ])->findOrFail($id);

    //     return response()->json($map);
    // }

    /**
     * Update an existing mapping (from modal)
     */
    public function update(Request $request, $id)
    {
        $map = EmployeeBeatOutletMap::findOrFail($id);

        $request->validate([
            'employee_id'   => 'required|exists:employee_masters,emp_id',
            'beat_id'       => 'required|exists:beat_master,id',
            'outlet_id'     => 'required|exists:outlet_master,id',
            'distributor_id' => 'required|exists:distributor_master,id',
            'assigned_from' => 'nullable|date',
            'assigned_to'   => 'nullable|date',
            'status'        => 'nullable|string|in:ACTIVE,INACTIVE'
        ]);

        $map->update($request->all());

        return redirect()->route('admin.employee-maps.index')
            ->with('success', 'Mapping updated successfully');
    }

    /**
     * Delete a mapping
     */
    public function destroy($id)
    {
        $map = EmployeeBeatOutletMap::findOrFail($id);

        $map->update([
            'status' => 'INACTIVE',
            'assigned_to' => now()->toDateString(),
        ]);

        return redirect()
            ->route('admin.employee-maps.index')
            ->with('success', 'Mapping deactivated successfully');
    }

    public function data()
    {
        $query = EmployeeBeatOutletMap::with([
            'employee:emp_id,emp_name',
            'beat:id,beat_name',
            'outlet:id,outlet_name',
            'distributor:id,distributor_name'
        ])->select('employee_beat_outlet_map.*');

        return DataTables::of($query)
            ->addIndexColumn()

            ->addColumn('employee', function ($row) {
                return $row->employee
                    ? $row->employee->emp_name . ' (' . $row->employee->emp_id . ')'
                    : 'Unknown';
            })

            ->addColumn(
                'beat',
                fn($row) =>
                $row->beat->beat_name ?? 'Unknown'
            )

            ->addColumn(
                'outlet',
                fn($row) =>
                $row->outlet->outlet_name ?? 'Unknown'
            )

            ->addColumn(
                'distributor',
                fn($row) =>
                $row->distributor->distributor_name ?? 'Unknown'
            )

            ->addColumn(
                'status',
                fn($row) =>
                $row->status ?? 'ACTIVE'
            )

            ->addColumn('actions', function ($row) {
                return view('admin.employee-maps.partials.actions', compact('row'))->render();
            })

            /* 🔎 SEARCH FIXES */

            ->filterColumn('employee', function ($query, $keyword) {
                $query->whereHas('employee', function ($q) use ($keyword) {
                    $q->where('emp_name', 'ILIKE', "%{$keyword}%")
                        ->orWhere('emp_id', 'ILIKE', "%{$keyword}%");
                });
            })

            ->filterColumn('beat', function ($query, $keyword) {
                $query->whereHas('beat', function ($q) use ($keyword) {
                    $q->where('beat_name', 'ILIKE', "%{$keyword}%");
                });
            })

            ->filterColumn('outlet', function ($query, $keyword) {
                $query->whereHas('outlet', function ($q) use ($keyword) {
                    $q->where('outlet_name', 'ILIKE', "%{$keyword}%");
                });
            })

            ->filterColumn('distributor', function ($query, $keyword) {
                $query->whereHas('distributor', function ($q) use ($keyword) {
                    $q->where('distributor_name', 'ILIKE', "%{$keyword}%");
                });
            })
            ->filter(function ($query) {

                if ($emp = request('employee')) {
                    $query->whereHas('employee', function ($q) use ($emp) {
                        $q->where('emp_name', 'ILIKE', "%{$emp}%")
                            ->orWhere('emp_id', 'ILIKE', "%{$emp}%");
                    });
                }

                if ($beat = request('beat')) {
                    $query->whereHas('beat', function ($q) use ($beat) {
                        $q->where('beat_name', 'ILIKE', "%{$beat}%");
                    });
                }

                if ($outlet = request('outlet')) {
                    $query->whereHas('outlet', function ($q) use ($outlet) {
                        $q->where('outlet_name', 'ILIKE', "%{$outlet}%");
                    });
                }

                if ($dist = request('distributor')) {
                    $query->whereHas('distributor', function ($q) use ($dist) {
                        $q->where('distributor_name', 'ILIKE', "%{$dist}%");
                    });
                }

                if ($status = request('status')) {
                    $query->where('status', $status);
                }
            })

            ->rawColumns(['actions'])
            ->make(true);
    }
}
