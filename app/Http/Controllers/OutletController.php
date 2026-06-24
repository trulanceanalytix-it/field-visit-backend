<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OutletMaster;
use App\Models\DistributorMaster;
use App\Models\Employee;
use App\Models\EmployeeBeatOutletMap;
use Illuminate\Support\Facades\Cache; // Add this import
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Psy\TabCompletion\Matcher\AbstractDefaultParametersMatcher;

class OutletController extends Controller
{
    // public function index()
    // {
    //     $outlets = OutletMaster::all();
    //     return view('admin.outlets.index', compact('outlets'));
    // }
    public function index()
    {
        $distributors = DistributorMaster::where('status', 'ACTIVE')
            ->with(['beats' => function ($q) {
                $q->where('status', 'ACTIVE');
            }])
            ->get();
        return view('admin.outlets.index', compact('distributors'));
    }


    public function create()
    {
        return view('admin.outlets.create');
    }
    public function data(Request $request)
    {
        $outletName  = $request->input('outlet_name');
        $beatName    = $request->input('beat_name');
        $distributor = $request->input('distributor');
        $status      = $request->input('status');
        $tse      = $request->input('tse');
        $cm       = $request->input('cm');
        $district = $request->input('district');
        $state    = $request->input('state');


        $columns = [
            0 => 'id',
            1 => 'outlet_name',
            2 => 'beat_id',
            3 => 'distributor_id',
            4 => 'status',
        ];

        $totalData = OutletMaster::count();

        $limit  = $request->input('length', 10);
        $start  = $request->input('start', 0);
        $order  = $columns[$request->input('order.0.column')] ?? 'id';
        $dir    = $request->input('order.0.dir') ?? 'asc';
        $search = $request->input('search.value');

        $query = OutletMaster::with(['beat.distributor', 'employeeMaps.employee']);


        /* 🔍 COLUMN FILTERS */
        if (!empty($outletName)) {
            $query->whereRaw('LOWER(outlet_name) LIKE ?', ['%' . strtolower($outletName) . '%']);
        }

        if (!empty($beatName)) {
            $query->whereHas('beat', function ($q) use ($beatName) {
                $q->whereRaw('LOWER(beat_name) LIKE ?', ['%' . strtolower($beatName) . '%']);
            });
        }

        if (!empty($distributor)) {
            $query->whereHas('beat.distributor', function ($q) use ($distributor) {
                $q->whereRaw('LOWER(distributor_name) LIKE ?', ['%' . strtolower($distributor) . '%']);
            });
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $search = strtolower(trim($search));

            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(outlet_name) LIKE ?', ["%{$search}%"])
                    ->orWhereHas('beat', function ($b) use ($search) {
                        $b->whereRaw('LOWER(beat_name) LIKE ?', ["%{$search}%"])
                            ->orWhereHas('distributor', function ($d) use ($search) {
                                $d->whereRaw('LOWER(distributor_name) LIKE ?', ["%{$search}%"]);
                            });
                    });
            });
        }
        if (!empty($tse)) {
            $query->whereHas('employeeMaps.employee', function ($q) use ($tse) {
                $q->whereRaw('LOWER(emp_name) LIKE ?', ['%' . strtolower($tse) . '%'])
                    ->where('is_admin', false);
            });
        }

        if (!empty($cm)) {
            $query->whereHas('employeeMaps.employee', function ($q) use ($cm) {
                $q->whereRaw('LOWER(emp_name) LIKE ?', ['%' . strtolower($cm) . '%'])
                    ->where('is_admin', true);
            });
        }

        if (!empty($district)) {
            $query->whereHas('beat.distributor', function ($q) use ($district) {
                $q->whereRaw('LOWER(district) LIKE ?', ['%' . strtolower($district) . '%']);
            });
        }

        if (!empty($state)) {
            $query->whereHas('beat.distributor', function ($q) use ($state) {
                $q->whereRaw('LOWER(state) LIKE ?', ['%' . strtolower($state) . '%']);
            });
        }
        $totalFiltered = $query->count();

        $outlets = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();


        $data = [];
        $i = $start + 1;

        foreach ($outlets as $outlet) {

            $tse = $outlet->employeeMaps
                ->pluck('employee.emp_name')
                ->unique()
                ->implode(', ') ?: '-';

            $cm = $outlet->employeeMaps
                ->pluck('cm_name')
                ->unique()
                ->implode(', ') ?: '-';

            // Distributor
            $distributor = $outlet->beat?->distributor;

            // District & State
            $district = $distributor?->district ?? '-';
            $state    = $distributor?->state ?? '-';
            $data[] = [
                $i++,
                $outlet->outlet_name,
                $tse,
                $cm,
                $outlet->beat?->beat_name ?? '-',
                $outlet->beat?->distributor?->distributor_name ?? '-',
                $district,
                $state,
                $outlet->status ?? 'ACTIVE',
                view('admin.outlets.partials.actions', compact('outlet'))->render(),
            ];
        }

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $data,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'outlet_name'     => 'required|string|max:255',
            'distributor_id'  => 'required|exists:distributor_master,id',
            'beat_id'         => 'required|exists:beat_master,id',
            'status'          => 'required|in:ACTIVE,INACTIVE',
        ]);

        OutletMaster::create([
            'outlet_name' => $request->outlet_name,
            'beat_id'     => $request->beat_id,
            'status'      => $request->status,
        ]);

        return redirect()
            ->route('admin.outlets.index')
            ->with('success', 'Outlet added successfully');
    }
     public function storeMobile(Request $request)
    {
        $requestId = $request->header('X-Request-ID') ?? uniqid();

        if (Cache::has('outlet_request_' . $requestId)) {
            return response()->json([
                'success' => false,
                'message' => 'Duplicate request detected'
            ], 429);
        }

        Cache::put('outlet_request_' . $requestId, true, 5);

        $validator = Validator::make($request->all(), [
            'outlet_name'     => 'required|string|max:255',
            'beat_id'         => 'required|exists:beat_master,id',
            'distributor_id'  => 'required|exists:distributor_master,id',

            'owner_name'      => 'nullable|string|max:255',
            'outlet_mobile'   => 'nullable|string|max:15',
            'outlet_whatsapp' => 'nullable|string|max:15',
            'address'         => 'nullable|string',
            'gstin'           => 'nullable|string|max:15',
            'signage'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'visiting_card'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'shop_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized user'
            ], 401);
        }

        DB::beginTransaction();

        try {
            // Store uploaded images to outlet_images/ folder in public disk
            $signagePath = null;
            if ($request->hasFile('signage')) {
                $signagePath = $request->file('signage')->store('outlet_images', 'public');
            }

            $visitingCardPath = null;
            if ($request->hasFile('visiting_card')) {
                $visitingCardPath = $request->file('visiting_card')->store('outlet_images', 'public');
            }

            $shopImagePath = null;
            if ($request->hasFile('shop_image')) {
                $shopImagePath = $request->file('shop_image')->store('outlet_images', 'public');
            }

            // 1?? Create Outlet
            $outlet = OutletMaster::create([
                'outlet_name'     => $request->outlet_name,
                'beat_id'         => $request->beat_id,
                'owner_name'      => $request->owner_name,
                'outlet_mobile'   => $request->outlet_mobile,
                'outlet_whatsapp' => $request->outlet_whatsapp,
                'address'         => $request->address,
                'gstin'           => $request->gstin,
                'signage_image'   => $signagePath,
                'visiting_card'   => $visitingCardPath,
                'shop_image'      => $shopImagePath,
                'status'          => 'ACTIVE',
            ]);

            // 2?? Find the CM for this employee & beat
            $cmRecord = EmployeeBeatOutletMap::where('beat_id', $request->beat_id)
                ->where('employee_id', $user->emp_id)
                ->whereNotNull('cm_id')
                ->first();

            // ?? If CM found use it, else fallback to logged-in user as CM
            $cmId   = $cmRecord->cm_id   ?? $user->emp_id;
            $cmName = $cmRecord->cm_name ?? $user->emp_name;

            // 3?? Find employees mapped to this beat under the resolved CM
            $employees = EmployeeBeatOutletMap::where('beat_id', $request->beat_id)
                ->where('cm_id', $cmId)
                ->pluck('employee_id')
                ->unique();

            // If no employees found, just map to logged-in user
            if ($employees->isEmpty()) {
                $employees = collect([$user->emp_id]);
            }

            foreach ($employees as $empId) {
                $employee = Employee::where('emp_id', $empId)->first();

                EmployeeBeatOutletMap::updateOrCreate(
                    [
                        'employee_id' => $empId,
                        'beat_id'     => $request->beat_id,
                        'outlet_id'   => $outlet->id,
                    ],
                    [
                        'employee_name'  => $employee->emp_name ?? null,
                        'cm_id'          => $cmId,        // ?? always correct CM
                        'cm_name'        => $cmName,      // ?? always correct CM name
                        'distributor_id' => $request->distributor_id,
                        'district'       => $employee->district ?? null,
                        'town_name'      => $employee->town_name ?? null,
                        'status'         => 'ACTIVE',
                        'assigned_from'  => now(),
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Outlet added & mapped successfully',
                'outlet'  => $outlet,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function edit($id)
    {
        $outlet = OutletMaster::findOrFail($id);
        return view('admin.outlets.edit', compact('outlet'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'outlet_name' => 'required|string|max:150|unique:outlet_master,outlet_name,' . $id,
            'beat_id'     => 'required|exists:beat_master,id',
            'status'      => 'required|in:ACTIVE,INACTIVE',
        ]);

        $outlet = OutletMaster::findOrFail($id);

        $outlet->update([
            'outlet_name' => $request->outlet_name,
            'beat_id'     => $request->beat_id,
            'status'      => $request->status,
        ]);

        return redirect()
            ->route('admin.outlets.index')
            ->with('success', 'Outlet updated successfully');
    }

    public function destroy($id)
    {
        $outlet = OutletMaster::findOrFail($id);

        $outlet->update([
            'status' => 'INACTIVE',
        ]);
        $outlet->save();

        return redirect()
            ->route('admin.outlets.index')
            ->with('success', 'Outlet deactivated successfully');
    }
}
