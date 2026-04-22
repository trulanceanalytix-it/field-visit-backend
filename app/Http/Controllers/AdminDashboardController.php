<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\FieldVisitEntry;
use App\Models\EmployeeBeatOutletMap;

class AdminDashboardController extends Controller
{
    public function dashboard($empId)
    {
        $user = Employee::where('emp_id', $empId)->firstOrFail();

        // 🔹 Admin → all visits
        if ($user->is_admin) {
            $visits = FieldVisitEntry::latest('visited_at')->get();

            $employees = Employee::select('emp_id', 'emp_name')->get();
        }
        // 🔹 Manager → only team visits
        else {
            $teamEmpIds = Employee::where('reporting_mgr_id', $empId)
                ->pluck('emp_id');

            $visits = FieldVisitEntry::whereIn('emp_id', $teamEmpIds)
                ->latest('visited_at')
                ->get();

            $employees = Employee::whereIn('emp_id', $teamEmpIds)
                ->select('emp_id', 'emp_name')
                ->get();
        }

        return response()->json([
            'status' => true,
            'employees' => $employees,
            'visits' => $visits
        ]);
    }
    public function employeeBeats($empId)
    {
        $maps = EmployeeBeatOutletMap::select(
            'beat_master.id as beat_id',
            'beat_master.beat_name',
            'outlet_master.id as outlet_id',
            'outlet_master.outlet_name'
        )
            ->join('beat_master', 'beat_master.id', '=', 'employee_beat_outlet_map.beat_id')
            ->join('outlet_master', 'outlet_master.id', '=', 'employee_beat_outlet_map.outlet_id')
            ->where('employee_beat_outlet_map.employee_id', $empId)
            ->get();

        $beats = [];

        foreach ($maps as $m) {

            if (!isset($beats[$m->beat_id])) {
                $beats[$m->beat_id] = [
                    'beat_id' => $m->beat_id,
                    'beat_name' => $m->beat_name,
                    'outlets' => []
                ];
            }

            $beats[$m->beat_id]['outlets'][] = [
                'outlet_id' => $m->outlet_id,
                'outlet_name' => $m->outlet_name
            ];
        }

        return response()->json([
            'status' => true,
            'data' => array_values($beats)
        ]);
    }
}
