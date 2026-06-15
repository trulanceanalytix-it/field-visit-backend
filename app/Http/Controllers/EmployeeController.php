<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('admin.employees.index');
    }

    public function data()
    {
        $query = Employee::select([
            'id',
            'emp_id',
            'emp_name',
            'emp_designation',
            'assigned_region',
            'is_admin',
            'status'
        ]);

        return DataTables::of($query)
            ->addIndexColumn()

            ->addColumn('admin', function ($row) {
                return $row->is_admin ? 'YES' : 'NO';
            })

            /* COLUMN SEARCH */
            ->filterColumn('emp_id', function ($query, $keyword) {
                $query->where('emp_id', 'ILIKE', "%{$keyword}%");
            })

            ->filterColumn('emp_name', function ($query, $keyword) {
                $query->where('emp_name', 'ILIKE', "%{$keyword}%");
            })

            ->filterColumn('emp_designation', function ($query, $keyword) {
                $query->where('emp_designation', 'ILIKE', "%{$keyword}%");
            })

            ->filterColumn('assigned_region', function ($query, $keyword) {
                $query->where('assigned_region', 'ILIKE', "%{$keyword}%");
            })

            ->filterColumn('status', function ($query, $keyword) {
                if ($keyword !== '') {
                    $query->where('status', $keyword);
                }
            })

            ->filterColumn('admin', function ($query, $keyword) {
                if ($keyword !== '') {
                    $query->where('is_admin', $keyword);
                }
            })

            /* GLOBAL SEARCH */
            ->filter(function ($query) {
                if (request()->filled('search.value')) {
                    $search = request('search.value');

                    $query->where(function ($q) use ($search) {
                        $q->where('emp_id', 'ILIKE', "%{$search}%")
                            ->orWhere('emp_name', 'ILIKE', "%{$search}%")
                            ->orWhere('emp_designation', 'ILIKE', "%{$search}%")
                            ->orWhere('assigned_region', 'ILIKE', "%{$search}%")
                            ->orWhere('status', 'ILIKE', "%{$search}%")
                            ->orWhereRaw(
                                "CASE WHEN is_admin = true THEN 'YES' ELSE 'NO' END ILIKE ?",
                                ["%{$search}%"]
                            );
                    });
                }
            })

            ->addColumn('actions', function ($row) {
                return view('admin.employees.partials.actions', compact('row'))->render();
            })

            ->rawColumns(['actions'])
            ->make(true);
    }


    public function store(Request $request)
    {
        $request->validate([
            'emp_id'           => 'required|string|max:20|unique:employee_masters,emp_id',
            'emp_name'         => 'required|string|max:150',
            'emp_designation'  => 'nullable|string|max:100',
            'assigned_region'  => 'nullable|string|max:100',
            'is_admin'         => 'required|boolean',
            'password'         => 'required|string',
            'status'           => 'required|string|in:ACTIVE,INACTIVE',
        ]);

        Employee::create([
            'emp_id'          => $request->emp_id,
            'emp_name'        => $request->emp_name,
            'emp_designation' => $request->emp_designation,
            'assigned_region' => $request->assigned_region,
            'is_admin'        => $request->is_admin,
            'status'          => $request->status,
            'password'        => bcrypt($request->password),
        ]);

        return redirect()
            ->route('admin.employees.index')
            ->with('success', 'Employee added successfully');
    }

    public function update(Request $request, $emp_id)
    {
        $employee = Employee::where('emp_id', $emp_id)->firstOrFail();

        $rules = [
            'emp_id'          => 'required|string|max:20|unique:employee_masters,emp_id,' . $employee->id,
            'emp_name'        => 'required|string|max:150',
            'emp_designation' => 'nullable|string|max:100',
            'assigned_region' => 'nullable|string|max:100',
            'is_admin'        => 'required|boolean',
            'password'        => 'nullable|string|min:6', // nullable - only update if provided
            'status'          => 'required|string|in:ACTIVE,INACTIVE',
        ];

        $request->validate($rules);

        $data = [
            'emp_id'          => $request->emp_id,
            'emp_name'        => $request->emp_name,
            'emp_designation' => $request->emp_designation,
            'assigned_region' => $request->assigned_region,
            'is_admin'        => $request->is_admin,
            'status'          => $request->status,
        ];

        // Only update password if a new one was provided
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $employee->update($data);

        return redirect()
            ->route('admin.employees.index')
            ->with('success', 'Employee updated successfully');
    }

    public function destroy($id)
    {
        Employee::findOrFail($id)->delete();

        return redirect()
            ->route('admin.employees.index')
            ->with('success', 'Employee deleted successfully');
    }
}
