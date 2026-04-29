<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FieldVisitExport;
use App\Models\Employee;
// use App\Models\Beat;
use App\Models\EmployeeBeatOutletMap;
use App\Models\BeatMaster;
use App\Models\OutletMaster;
use App\Models\DistributorMaster;
use App\Models\BeatOutletMaster;
use App\Models\Store;
use App\Models\Remark;
use App\Models\FieldVisitEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TCPDF;
use app\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FieldVisitController extends Controller
{

    // public function create()
    // {
    //     $employees = Employee::orderBy('emp_id')->get(['emp_id', 'emp_name']);
    //     // $beats = Beat::orderBy('beat_name')->get(['beat_id', 'beat_name']);
    //     $stores = Store::orderBy('store_name')->get(['store_id', 'store_name']);
    //     $remarks = Remark::orderBy('remark')->get(); // all remarks

    //     return view('field-visit-entry', compact(
    //         'employees',
    //         // 'beats',
    //         'stores',
    //         'remarks'
    //     ));
    // }
    public function create()
    {
        // ✅ Logged-in user (for auto-fill & security)
        $authUser = Auth::user();

        // ✅ MTD Achieved Pcs (1st of month → today)
        $startOfMonth = Carbon::now()->startOfMonth();
        $today = Carbon::now()->endOfDay();

        $mtdAchievedPcs = FieldVisitEntry::where('emp_id', $authUser->emp_id)
            ->whereBetween('created_at', [$startOfMonth, $today])
            ->sum('total_pcs');
        $mtdDistributorPcs = FieldVisitEntry::where('emp_id', $authUser->emp_id)
            ->whereBetween('created_at', [$startOfMonth, $today])
            ->get()
            ->groupBy('distributor_name')
            ->map(fn($rows) => [
                'distributor_name' => $rows->first()->distributor_name,
                'total_pcs' => $rows->sum('total_pcs')
            ]);

        // ✅ Keep Employee model (for other logic / future use)
        $employees = Employee::orderBy('emp_id')
            ->get(['emp_id', 'emp_name']);

        $stores = Store::orderBy('store_name')
            ->get(['store_id', 'store_name']);

        $remarks = Remark::orderBy('remark')->get();

        return view('field-visit-entry', compact(
            'authUser',
            'employees',
            'stores',
            'remarks',
            'mtdAchievedPcs',
            'mtdDistributorPcs'
        ));
    }
    // public function getBeatOutletByEmp($empId)
    // {
    //     return BeatOutletMaster::where('tse_id', $empId)
    //         ->select(
    //             'beat_name',
    //             'distributor_name',
    //             'outlet_name',
    //             'cluster_manager_id',
    //             'cluster_manager_name',

    //             // New fields
    //             'floors',
    //             'total_sft',
    //             'nearby_shops',
    //             'signage_image',
    //             'image_1',
    //             'image_2'
    //         )
    //         ->orderBy('beat_name')
    //         ->get();
    // }
    public function getBeatOutletByEmp($empId)
    {
        $data = EmployeeBeatOutletMap::query()
            ->where(function ($q) use ($empId) {
                $q->where('employee_beat_outlet_map.employee_id', $empId)
                    ->orWhere('employee_beat_outlet_map.cm_id', $empId);
            })->where('employee_beat_outlet_map.status', 'ACTIVE')
            ->join('beat_master', 'beat_master.id', '=', 'employee_beat_outlet_map.beat_id')
            ->join('outlet_master', 'outlet_master.id', '=', 'employee_beat_outlet_map.outlet_id')
            ->join('distributor_master', 'distributor_master.id', '=', 'employee_beat_outlet_map.distributor_id')
            ->select([
                'employee_beat_outlet_map.employee_id',
                'employee_beat_outlet_map.employee_name',
                'beat_master.id as beat_id',
                'beat_master.beat_name',
                'distributor_master.id as distributor_id',
                'distributor_master.distributor_name',
                'outlet_master.id as outlet_id',
                'outlet_master.outlet_name',

                // optional outlet fields (if exist)
                'outlet_master.floors',
                'outlet_master.total_sft',
                'outlet_master.nearby_shops',
                'outlet_master.signage_image',
                'outlet_master.image_1',
                'outlet_master.image_2',
            ])
            ->orderBy('beat_master.beat_name')
            ->get();

        return response()->json($data);
    }


    public function getRemarks($fieldVisitId)
    {
        $remarks = Remark::where('field_visit_entry_id', $fieldVisitId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($remarks);
    }


    public function preview(Request $request)
    {
        // dd($request->all());

        $data = $request->validate([
            'emp_id'    => 'required',
            'emp_name'  => 'required|string',

            'distributor_id' => 'required|exists:distributor_master,id',
            'beat_id'        => 'required|exists:beat_master,id',
            'outlet_id'      => 'required|exists:outlet_master,id',

            'visited_date' => 'required|date',

            'opening_soh' => 'nullable|integer|min:0',
            'closing_soh' => 'nullable|integer|min:0',
            // FSU
            'fsu_type_1' => 'nullable|digits:1',
            'fsu_type_2' => 'nullable|digits:1',
            'fsu_type_3' => 'nullable|digits:1',
            'fsu_image' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:5120',

            // Instore branding
            'instore_branding' => 'nullable|in:Yes,No',
            'branding_image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            // Competitor brands
            'competitor_brands' => 'nullable|array',
            'competitor_brands.*' => 'string',
            'other_brand_name' => 'nullable|string',

            // Store grade
            'store_grade' => 'nullable|in:A+,A,B+,B,C',

            'leggings_qty'     => 'nullable|integer|min:0',
            'non_leggings_qty' => 'nullable|integer|min:0',
            'innerwear_qty'    => 'nullable|integer|min:0',
            'total_sales_qty'  => 'nullable|integer|min:0',

            'remarks' => 'nullable|array',
            'remarks.*' => 'integer|exists:remarks,id',

            'observation' => 'nullable|string',

            'latitude'   => 'nullable|numeric',
            'longitude'  => 'nullable|numeric',
            'address'    => 'nullable|string',
            'location_accuracy' => 'nullable|integer',
        ]);

        if ($request->hasFile('fsu_image')) {
            $data['fsu_image'] = $request->file('fsu_image')->store('fsu_images', 'public');
        }

        if ($request->hasFile('branding_image')) {
            $data['branding_image'] = $request->file('branding_image')->store('branding_images', 'public');
        }

        $beat = BeatMaster::find($data['beat_id']);
        $outlet = OutletMaster::find($data['outlet_id']);
        $distributor = DistributorMaster::find($data['distributor_id']);

        $data['beat_name'] = $beat?->beat_name;
        $data['outlet_name'] = $outlet?->outlet_name;
        $data['distributor_name'] = $distributor?->distributor_name;
        $data['instore_branding'] = ($request->instore_branding === 'Yes');

        session(['field_visit_preview' => $data]);

        $remarks = Remark::select('id', 'remark')->get();

        return view('field-visit.preview', compact('data', 'remarks'));
    }


    // public function preview(Request $request)
    // {

    //     // dd($request->all());
    //     $data = $request->validate([
    //         'emp_id' => 'required',
    //         'emp_name' => 'required',
    //         // 'beat_id' => 'required',
    //         'beat_name' => 'required',
    //         'outlet_name' => 'required',
    //         'visited_date' => 'required',
    //         // 'store_id' => 'required',
    //         // 'store_name' => 'required',

    //         'opening_soh' => 'nullable|integer|min:0',
    //         'closing_soh' => 'nullable|integer|min:0',
    //         'leggings_qty' => 'nullable|integer|min:0',
    //         'non_leggings_qty' => 'nullable|integer|min:0',
    //         'innerwear_qty' => 'nullable|integer|min:0',
    //         'total_sales_qty' => 'nullable|integer|min:0',
    //         'distributor_name' => 'required|string',

    //         'remarks' => 'nullable|array',
    //         'remarks.*' => 'integer|exists:remarks,id',
    //         'observation' => 'nullable|string',

    //         'latitude' => 'nullable|numeric',
    //         'longitude' => 'nullable|numeric',
    //         'address' => 'nullable|string',
    //         'location_accuracy' => 'nullable|integer',
    //     ]);

    //     // Store temporarily
    //     session(['field_visit_preview' => $data]);
    //     $remarks = Remark::select('id', 'remark')->get();

    //     return view('field-visit.preview', compact('data', 'remarks'));
    // }

    public function confirm(Request $request)
    {
        $data = session('field_visit_preview');
        if (!$data) {
            return redirect('/field-visit-entry')
                ->withErrors('Session expired. Please re-enter data.');
        }
        // 🔥 CONVERT HERE
        $data['total_pcs'] = $data['total_sales_qty'] ?? 0;
        unset($data['total_sales_qty']);

        FieldVisitEntry::create([
            ...$data,
            'remark' => $data['remarks'] ?? [], // map array
            'visited_at' => now(),
            'location_captured_at' => now(),
        ]);

        session()->forget('field_visit_preview');

        return redirect('/five')
            ->with('success', 'Field visit saved successfully');
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'emp_id'    => 'required|exists:employee_masters,emp_id',
    //         'emp_name'  => 'required|string',

    //         'distributor_id' => 'required|exists:distributor_master,id',
    //         'beat_id'        => 'required|exists:beat_master,id',
    //         'outlet_id'      => 'required|exists:outlet_master,id',

    //         'visited_date' => 'required|date',
    //         'visited_at' => 'required|date', // Add this validation

    //         'opening_soh' => 'nullable|integer|min:0',
    //         'closing_soh' => 'nullable|integer|min:0',
    //         // FSU
    //         'fsu_type_1' => 'nullable|digits:1',
    //         'fsu_type_2' => 'nullable|digits:1',
    //         'fsu_type_3' => 'nullable|digits:1',
    //         'fsu_image' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:5120',

    //         // Instore branding
    //         'instore_branding' => 'nullable|in:Yes,No',
    //         'branding_image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

    //         // ✅ NEW (for brand IDs)
    //         'competitor_brands'   => 'nullable|array',
    //         'competitor_brands.*' => 'integer|exists:competitor_brands,id',

    //         // Store grade
    //         'store_grade' => 'nullable|in:A+,A,B+,B,C',

    //         'leggings_qty'     => 'nullable|integer|min:0',
    //         'non_leggings_qty' => 'nullable|integer|min:0',
    //         'innerwear_qty'    => 'nullable|integer|min:0',

    //         'remark'   => 'nullable|array',
    //         'remark.*' => 'integer|exists:remarks,id',
    //         'observation' => 'nullable|string',

    //         'latitude'   => 'nullable|numeric',
    //         'longitude'  => 'nullable|numeric',
    //         'address'    => 'nullable|string',
    //         'location_accuracy' => 'nullable|integer',
    //     ]);

    //     if ($request->hasFile('fsu_image')) {
    //         $validated['fsu_image'] = $request->file('fsu_image')
    //             ->store('fsu_images', 'public');
    //     }

    //     if ($request->hasFile('branding_image')) {
    //         $validated['branding_image'] = $request->file('branding_image')
    //             ->store('branding_images', 'public');
    //     }

    //     $validated['instore_branding'] = ($request->instore_branding === 'Yes');

    //     // Calculate total pcs
    //     $validated['total_pcs'] =
    //         ($validated['leggings_qty'] ?? 0) +
    //         ($validated['non_leggings_qty'] ?? 0) +
    //         ($validated['innerwear_qty'] ?? 0);

    //     $validated['remark'] = $request->remark ?? [];
    //     $validated['competitor_brands'] = $request->competitor_brands ?? [];

    //     // Get the datetime values from request
    //     $visitedAt = $request->input('visited_at');
    //     $locationCapturedAt = $request->input('location_captured_at');

    //     FieldVisitEntry::create([
    //         ...$validated,
    //         'visited_at' => $visitedAt, // Use from request
    //         'location_captured_at' => $locationCapturedAt, // Use from request
    //         'created_at' => $visitedAt, // Optionally use same as visited_at
    //     ]);

    //     if ($request->expectsJson()) {
    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Field visit saved successfully',
    //         ], 201);
    //     }

    //     return back()->with('success', 'Field visit saved successfully');
    // }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'emp_id'    => 'required|exists:employee_masters,emp_id',
            'emp_name'  => 'required|string',

            'distributor_id' => 'required|exists:distributor_master,id',
            'beat_id'        => 'required|exists:beat_master,id',
            'outlet_id'      => 'required|exists:outlet_master,id',

            'visited_date' => 'required|date',
            'visited_at'   => 'required|date',

            // ?? STOCK
            'stock_leggings'      => 'nullable|integer|min:0',
            'stock_non_leggings'  => 'nullable|integer|min:0',
            'stock_innerwear'     => 'nullable|integer|min:0',

            // FSU
            'fsu_type_1' => 'nullable|digits:1',
            'fsu_type_2' => 'nullable|digits:1',
            'fsu_type_3' => 'nullable|digits:1',
            'fsu_image'  => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:5120',

            // Instore branding
            'instore_branding' => 'nullable|array',
            'branding_image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            // Competitor brands
            'competitor_brands'   => 'nullable|array',
            'competitor_brands.*' => 'numeric|exists:competitor_brands,id',

            // Store grade
            'store_grade' => 'nullable|in:A+,A,B+,B,C',

            // Sales
            'is_phone_order' => 'nullable|boolean',
            'leggings_qty'     => 'nullable|integer|min:0',
            'non_leggings_qty' => 'nullable|integer|min:0',
            'innerwear_qty'    => 'nullable|integer|min:0',

            'remark'   => 'nullable|array',
            'remark.*' => 'numeric|exists:remarks,id',
            'observation' => 'nullable|string',

            'latitude'   => 'nullable|numeric',
            'longitude'  => 'nullable|numeric',
            'address'    => 'nullable|string',
            'location_accuracy' => 'nullable|integer',
            'selfie_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        // Upload FSU image
        if ($request->hasFile('fsu_image')) {
            $validated['fsu_image'] = $request->file('fsu_image')
                ->store('fsu_images', 'public');
        }

        // Upload branding image
        if ($request->hasFile('branding_image')) {
            $validated['branding_image'] = $request->file('branding_image')
                ->store('branding_images', 'public');
        }

        // ? Store instore_branding as JSON
        $validated['instore_branding'] = $request->instore_branding
            ? $request->instore_branding
            : null;

        // Calculate total pcs
        $validated['total_pcs'] =
            ($validated['leggings_qty'] ?? 0) +
            ($validated['non_leggings_qty'] ?? 0) +
            ($validated['innerwear_qty'] ?? 0);
        $validated['is_phone_order'] = $request->is_phone_order ?? 0;
        $validated['remark'] = $request->remark ?? [];
        $validated['competitor_brands'] = $request->competitor_brands ?? [];

        // Datetime values
        $validated['location_captured_at'] = $request->location_captured_at;
        $validated['created_at'] = $request->visited_at;

        $entry = FieldVisitEntry::create($validated);
        if ($request->hasFile('selfie_image')) {
            $path = $request->file('selfie_image')
                ->store('visit_selfies', 'public');

            $entry->selfie()->create([
                'image_path' => $path,
                'image_url'  => Storage::url($path),
            ]);
        }
        if ($request->expectsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Field visit saved successfully',
                'data'    => $entry->load('selfie'),
            ], 201);
        }

        return back()->with('success', 'Field visit saved successfully');
    }
    public function edit()
    {
        $authUser = Auth::user();
        $data = session('field_visit_preview');
        // dd($data);
        if (!$data) {
            return redirect('/five')
                ->withErrors('Session expired. Please re-enter data.');
        }
        // ✅ MTD Achieved Pcs (1st of month → today)
        $startOfMonth = Carbon::now()->startOfMonth();
        $today = Carbon::now()->endOfDay();

        $mtdAchievedPcs = FieldVisitEntry::where('emp_id', $authUser->emp_id)
            ->whereBetween('created_at', [$startOfMonth, $today])
            ->sum('total_pcs');
        $mtdDistributorPcs = FieldVisitEntry::where('emp_id', $authUser->emp_id)
            ->whereBetween('created_at', [$startOfMonth, $today])
            ->get()
            ->groupBy('distributor_name')
            ->map(fn($rows) => [
                'distributor_name' => $rows->first()->distributor_name,
                'total_pcs' => $rows->sum('total_pcs')
            ]);

        $employees = Employee::orderBy('emp_id')->get(['emp_id', 'emp_name']);
        // $beats = Beat::orderBy('beat_name')->get(['beat_id', 'beat_name']);
        $stores = Store::orderBy('store_name')->get(['store_name']);
        $remarks = Remark::select('id', 'remark')->get();
        return view('field-visit-entry', compact(
            'employees',
            // 'beats',
            'stores',
            'data',
            'remarks',
            'mtdAchievedPcs',
            'mtdDistributorPcs'
        ));
    }
    public function history(Request $request)
    {
        $authUser = Auth::user();

        if (!$authUser || empty($authUser->emp_id)) {
            abort(403, 'Employee not identified');
        }

        $empId = $authUser->emp_id;
        $date = $request->get('date');

        $query = FieldVisitEntry::query()
            ->where('field_visit_entries.emp_id', $empId)
            ->leftJoin('beat_master', 'beat_master.id', '=', 'field_visit_entries.beat_id')
            ->leftJoin('distributor_master', 'distributor_master.id', '=', 'field_visit_entries.distributor_id')
            ->leftJoin('outlet_master', 'outlet_master.id', '=', 'field_visit_entries.outlet_id')
            ->select(
                'field_visit_entries.*',
                'beat_master.beat_name',
                'distributor_master.distributor_name',
                'outlet_master.outlet_name'
            );

        // Optional date filter
        if (!empty($date)) {
            $query->whereDate('field_visit_entries.visited_at', $date);
        }

        $visits = $query
            ->orderBy('field_visit_entries.visited_at', 'desc')
            ->get();

        $remarksMap = Remark::pluck('remark', 'id');

        return view(
            'field-visit.history',
            compact('visits', 'date', 'remarksMap')
        );
    }

    public function map(Request $request)
    {
        // Date filter (default = today)
        $date = $request->filled('date')
            ? $request->date
            : now()->toDateString();

        $visits = FieldVisitEntry::whereDate('visited_at', $date)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('visited_at', 'desc')
            ->get([
                'emp_name',
                'outlet_name',
                'latitude',
                'longitude',
                'address',
                'visited_at',
                'location_accuracy',
            ]);

        return view('field-visit.map', compact('visits', 'date'));
    }
    public function exportExcel(Request $request)
    {
        $date = $request->get('date');

        return Excel::download(
            new FieldVisitExport($date),
            'field_visit_entries.xlsx'
        );
    }
    public function outletHistory(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|integer|exists:outlet_master,id'
        ]);

        $records = FieldVisitEntry::where('outlet_id', $request->outlet_id)
            ->orderByDesc('id')
            ->get([
                'visited_date',
                'leggings_qty',
                'non_leggings_qty',
                'innerwear_qty',
                'total_pcs'
            ]);

        return response()->json([
            'last_visit' => optional($records->first())->visited_date,
            'records'    => $records
        ]);
    }

    // public function previewPdf()
    // {
    //     $data = session('field_visit_preview'); // or fetch from DB
    //     $remarks = Remark::all();

    //     $html = View::make(
    //         'field-visit.preview',   // EXISTING preview blade
    //         compact('data', 'remarks')
    //     )->render();

    //     return Pdf::loadHTML($html)
    //         ->setPaper('A4', 'portrait')
    //         ->download('field-visit-data.pdf');
    // }

    public function previewPdf()
    {
        $data = session('field_visit_preview');

        /* ---------- File Name ---------- */
        $employeeName = preg_replace('/[^A-Za-z0-9_-]/', '_', $data['emp_name'] ?? 'employee');
        $outletName   = preg_replace('/[^A-Za-z0-9_-]/', '_', $data['outlet_name'] ?? 'outlet');
        $fileName     = $employeeName . '_' . $outletName . '.pdf';

        /* ---------- Remarks ---------- */
        $remarksMaster = Remark::pluck('remark', 'id');

        $pdf = new \TCPDF('P', 'mm', 'A4');
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        /* ---------- Title ---------- */
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'FIELD VISIT REPORT', 0, 1, 'C');
        $pdf->Ln(4);

        /* ---------- Info Table ---------- */
        $pdf->SetFont('helvetica', '', 11);
        $labelWidth = 60;
        $valueWidth = 115;

        $pdf->SetFillColor(240, 240, 240);

        $pdf->Cell($labelWidth, 8, 'Employee Name', 1, 0, 'L', true);
        $pdf->Cell($valueWidth, 8, $data['emp_name'] ?? '-', 1, 1);

        $pdf->Cell($labelWidth, 8, 'Beat Name', 1, 0, 'L', true);
        $pdf->Cell($valueWidth, 8, $data['beat_name'] ?? '-', 1, 1);

        $pdf->Cell($labelWidth, 8, 'Distributor', 1, 0, 'L', true);
        $pdf->Cell($valueWidth, 8, $data['distributor_name'] ?? '-', 1, 1);

        $pdf->Cell($labelWidth, 8, 'Outlet', 1, 0, 'L', true);
        $pdf->Cell($valueWidth, 8, $data['outlet_name'] ?? '-', 1, 1);

        $pdf->Ln(6);

        /* ---------- Sales Section ---------- */
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, 'SALES SUMMARY', 1, 1, 'C', true);

        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(45, 8, 'L', 1, 0, 'C');
        $pdf->Cell(45, 8, 'NL', 1, 0, 'C');
        $pdf->Cell(45, 8, 'IW', 1, 0, 'C');
        $pdf->Cell(0, 8, 'TOTAL', 1, 1, 'C');

        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(45, 8, $data['leggings_qty'] ?? 0, 1, 0, 'C');
        $pdf->Cell(45, 8, $data['non_leggings_qty'] ?? 0, 1, 0, 'C');
        $pdf->Cell(45, 8, $data['innerwear_qty'] ?? 0, 1, 0, 'C');
        $pdf->Cell(0, 8, $data['total_sales_qty'] ?? 0, 1, 1, 'C');

        $pdf->Ln(6);

        /* ---------- Remarks ---------- */
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 8, 'REMARKS', 1, 1, 'L', true);

        $pdf->SetFont('helvetica', '', 10);

        if (!empty($data['remarks'])) {
            foreach ($data['remarks'] as $remarkId) {
                if (isset($remarksMaster[$remarkId])) {
                    $pdf->MultiCell(0, 6, '• ' . $remarksMaster[$remarkId], 0, 'L');
                }
            }
        } else {
            $pdf->Cell(0, 8, 'No remarks', 1, 1);
        }

        $pdf->Ln(4);

        /* ---------- Observation ---------- */
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 8, 'OBSERVATION', 1, 1, 'L', true);

        $pdf->SetFont('helvetica', '', 11);
        $pdf->MultiCell(0, 10, $data['observation'] ?? '-', 1);

        /* ---------- Footer ---------- */
        $pdf->SetY(-20);
        $pdf->SetFont('helvetica', 'I', 9);
        $pdf->Cell(0, 8, 'Generated on: ' . now()->format('d-m-Y H:i'), 0, 0, 'R');

        /* ---------- Force Download ---------- */
        return response($pdf->Output($fileName, 'D'))
            ->header('Content-Type', 'application/pdf');
    }
    // public function historyApi(Request $request)
    // {
    //     $empId = $request->emp_id;
    //     $date  = $request->date;

    //     $query = FieldVisitEntry::query()
    //         ->where('field_visit_entries.emp_id', $empId)
    //         ->leftJoin('beat_master', 'beat_master.id', '=', 'field_visit_entries.beat_id')
    //         ->leftJoin('distributor_master', 'distributor_master.id', '=', 'field_visit_entries.distributor_id')
    //         ->leftJoin('outlet_master', 'outlet_master.id', '=', 'field_visit_entries.outlet_id')
    //         ->select(
    //             'field_visit_entries.*',
    //             'beat_master.beat_name',
    //             'distributor_master.distributor_name',
    //             'outlet_master.outlet_name'
    //         );

    //     if (!empty($date)) {
    //         $query->whereDate('field_visit_entries.visited_at', $date);
    //     }

    //     $visits = $query
    //         ->orderBy('field_visit_entries.visited_at', 'desc')
    //         ->get();

    //     return response()->json([
    //         'status' => true,
    //         'data' => $visits
    //     ]);
    // }

    public function historyApi(Request $request)
    {
        $empId = $request->emp_id;
        $date  = $request->date;

        // 🔹 Base query (NO joins for count)
        $baseQuery = FieldVisitEntry::query()
            ->where('emp_id', $empId);

        if (!empty($date)) {
            $baseQuery->whereDate('visited_at', $date);
        }

        // ✅ If only count is requested → return fast
        if ($request->only_count) {
            return response()->json([
                'status' => true,
                'count'  => $baseQuery->count()
            ]);
        }

        // 🔹 Full query WITH joins (only when needed)
        $query = $baseQuery
            ->leftJoin('beat_master', 'beat_master.id', '=', 'field_visit_entries.beat_id')
            ->leftJoin('distributor_master', 'distributor_master.id', '=', 'field_visit_entries.distributor_id')
            ->leftJoin('outlet_master', 'outlet_master.id', '=', 'field_visit_entries.outlet_id');

        $visits = $query
            ->select(
                'field_visit_entries.*',
                'beat_master.beat_name',
                'distributor_master.distributor_name',
                'outlet_master.outlet_name'
            )
            ->orderBy('field_visit_entries.visited_at', 'desc')
            ->get();

        $cmInfo = DB::table('employee_beat_outlet_map')
            ->where('employee_id', $empId)
            ->select('cm_id', 'cm_name')
            ->first();

        return response()->json([
            'status' => true,
            'cm_id'   => $cmInfo->cm_id ?? null,
            'cm_name' => $cmInfo->cm_name ?? null,
            'count'   => $visits->count(), // optional, keeps consistency
            'data'    => $visits
        ]);
    }
    public function apiCreate()
    {
        $authUser = Auth::user();

        $startOfMonth = Carbon::now()->startOfMonth();
        $today = Carbon::now()->endOfDay();

        $mtdAchievedPcs = FieldVisitEntry::where('emp_id', $authUser->emp_id)
            ->whereBetween('created_at', [$startOfMonth, $today])
            ->sum('total_pcs');

        $mtdDistributorPcs = FieldVisitEntry::where('emp_id', $authUser->emp_id)
            ->whereBetween('created_at', [$startOfMonth, $today])
            ->get()
            ->groupBy('distributor_name')
            ->map(fn($rows) => [
                'distributor_name' => $rows->first()->distributor_name,
                'total_pcs' => $rows->sum('total_pcs')
            ])->values();

        $employees = Employee::orderBy('emp_id')
            ->get(['emp_id', 'emp_name']);

        $stores = Store::orderBy('store_name')
            ->get(['store_id', 'store_name']);

        $remarks = Remark::active()->orderBy('id')->get(['id', 'remark']);

        return response()->json([
            'authUser' => $authUser,
            'employees' => $employees,
            'stores' => $stores,
            'remarks' => $remarks,
            'mtdAchievedPcs' => $mtdAchievedPcs,
            'mtdDistributorPcs' => $mtdDistributorPcs,
        ]);
    }
    // public function adminVisitsByDate(Request $request, $empId)
    // {
    //     $date = $request->date;

    //     $user = Employee::where('emp_id', $empId)->firstOrFail();


    //     $query = FieldVisitEntry::select(
    //         'field_visit_entries.*',
    //         'outlet_master.outlet_name',
    //         'distributor_master.distributor_name',
    //         'beat_master.beat_name'
    //     )
    //         ->leftJoin('outlet_master', 'outlet_master.id', '=', 'field_visit_entries.outlet_id')
    //         ->leftJoin('distributor_master', 'distributor_master.id', '=', 'field_visit_entries.distributor_id')
    //         ->leftJoin('beat_master', 'beat_master.id', '=', 'field_visit_entries.beat_id')
    //         ->whereDate('field_visit_entries.visited_date', $date);
    //     $teamEmpIds = [];

    //     if (!$user->is_admin) {

    //         $teamEmpIds = EmployeeBeatOutletMap::where('cm_id', $empId)
    //             ->pluck('employee_id')
    //             ->toArray();

    //         $query->whereIn('field_visit_entries.emp_id', $teamEmpIds);
    //         $teamEmployees = Employee::whereIn('emp_id', $teamEmpIds)
    //             ->select('emp_id', 'emp_name')
    //             ->get()
    //             ->map(function ($emp) {

    //                 $beatsCount = EmployeeBeatOutletMap::where('employee_id', $emp->emp_id)
    //                     ->distinct('beat_id')
    //                     ->count('beat_id');

    //                 $outletsCount = EmployeeBeatOutletMap::where('employee_id', $emp->emp_id)
    //                     ->distinct('outlet_id')
    //                     ->count('outlet_id');

    //                 return [
    //                     'emp_id' => $emp->emp_id,
    //                     'emp_name' => $emp->emp_name,
    //                     'beats_count' => $beatsCount,
    //                     'outlets_count' => $outletsCount
    //                 ];
    //             });
    //     }

    //     $visits = $query->latest('field_visit_entries.visited_at')->get();

    //     return response()->json([
    //         'status' => true,
    //         'data'   => $visits,
    //         'teams'   => $teamEmployees
    //     ]);
    // }
    public function adminVisitsByDate(Request $request, $empId)
    {
        $date = $request->date;

        $user = Employee::where('emp_id', $empId)->firstOrFail();

        $query = FieldVisitEntry::select(
            'field_visit_entries.*',
            'outlet_master.outlet_name',
            'distributor_master.distributor_name',
            'beat_master.beat_name'
        )
            ->leftJoin('outlet_master', 'outlet_master.id', '=', 'field_visit_entries.outlet_id')
            ->leftJoin('distributor_master', 'distributor_master.id', '=', 'field_visit_entries.distributor_id')
            ->leftJoin('beat_master', 'beat_master.id', '=', 'field_visit_entries.beat_id')
            ->whereDate('field_visit_entries.visited_date', $date);

        if (!$user->is_admin) {

            $teamEmpIds = EmployeeBeatOutletMap::where('cm_id', $empId)
                ->pluck('employee_id')
                ->toArray();

            $query->whereIn('field_visit_entries.emp_id', $teamEmpIds);

            $employees = Employee::whereIn('emp_id', $teamEmpIds)
                ->select('emp_id', 'emp_name')
                ->get();
        } else {

            $employees = Employee::select('emp_id', 'emp_name')->get();
        }

        $teamEmployees = $employees->map(function ($emp) {

            $beatsCount = EmployeeBeatOutletMap::where('employee_id', $emp->emp_id)
                ->distinct('beat_id')
                ->count('beat_id');

            $outletsCount = EmployeeBeatOutletMap::where('employee_id', $emp->emp_id)
                ->distinct('outlet_id')
                ->count('outlet_id');

            return [
                'emp_id' => $emp->emp_id,
                'emp_name' => $emp->emp_name,
                'beats_count' => $beatsCount,
                'outlets_count' => $outletsCount
            ];
        });

        $visits = $query->latest('field_visit_entries.visited_at')->get();

        // ? ADD THIS BLOCK (replace SQL logic)
        $visits->map(function ($visit) {

            $remarkIds = is_array($visit->remark)
                ? $visit->remark
                : json_decode($visit->remark, true) ?? [];
            $remarks = Remark::whereIn('id', $remarkIds)
                ->pluck('remark')
                ->toArray();

            $visit->remarks_text = $remarks; // ?? return as array (best for Flutter)

            return $visit;
        });

        return response()->json([
            'status' => true,
            'data'   => $visits,
            'teams'  => $teamEmployees
        ]);
    }
    public function employeeVisitMap(Request $request, $empId)
    {
        $date = $request->date;

        $visits = FieldVisitEntry::select(
            'field_visit_entries.*',
            'outlet_master.outlet_name',
            'distributor_master.distributor_name',
            'beat_master.beat_name',
            'employee_masters.emp_name'
        )
            ->leftJoin('outlet_master', 'outlet_master.id', '=', 'field_visit_entries.outlet_id')
            ->leftJoin('distributor_master', 'distributor_master.id', '=', 'field_visit_entries.distributor_id')
            ->leftJoin('beat_master', 'beat_master.id', '=', 'field_visit_entries.beat_id')
            ->leftJoin('employee_masters', 'employee_masters.emp_id', '=', 'field_visit_entries.emp_id')
            ->where('field_visit_entries.emp_id', $empId) // ? IMPORTANT
            ->whereDate('field_visit_entries.visited_date', $date)
            ->orderBy('field_visit_entries.visited_at', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $visits
        ]);
    }
    // public function employeeVisitMapWeb(Request $request, $empId)
    // {
    //     $date = $request->date;

    //     $visits = FieldVisitEntry::select(
    //         'field_visit_entries.*',
    //         'outlet_master.outlet_name',
    //         'employee_masters.emp_name'
    //     )
    //         ->leftJoin('outlet_master', 'outlet_master.id', '=', 'field_visit_entries.outlet_id')
    //         ->leftJoin('employee_masters', 'employee_masters.emp_id', '=', 'field_visit_entries.emp_id')
    //         ->where('field_visit_entries.emp_id', $empId)
    //         ->whereDate('field_visit_entries.visited_date', $date)
    //         ->orderBy('field_visit_entries.visited_at', 'asc')
    //         ->get();

    //     $totalDistance = 0;
    //     $formatted = [];

    //     foreach ($visits as $i => $v) {
    //         $leggings    = (int)($v->leggings_qty ?? 0);
    //         $nonLeggings = (int)($v->non_leggings_qty ?? 0);
    //         $innerwear   = (int)($v->innerwear_qty ?? 0);
    //         $pcs         = $leggings + $nonLeggings + $innerwear;

    //         // Calculate distance from previous point
    //         if ($i > 0) {
    //             $prev = $formatted[$i - 1];
    //             $totalDistance += $this->haversineDistance(
    //                 $prev['lat'],
    //                 $prev['lng'],
    //                 (float)$v->latitude,
    //                 (float)$v->longitude
    //             );
    //         }

    //         $formatted[] = [
    //             'lat'      => (float)$v->latitude,
    //             'lng'      => (float)$v->longitude,
    //             'name'     => $v->outlet_name ?? 'No Outlet',
    //             'emp_name' => $v->emp_name ?? '',
    //             'time'     => $v->visited_at,
    //             'pcs'      => $pcs,
    //             'leggings'    => $leggings,
    //             'non_leggings' => $nonLeggings,
    //             'innerwear'   => $innerwear,
    //         ];
    //     }

    //     return response()->json([
    //         'visits'       => $formatted,
    //         'total_visits' => count($formatted),
    //         'total_km'     => round($totalDistance, 2),
    //         'total_pcs'    => array_sum(array_column($formatted, 'pcs'))
    //     ]);
    // }
    public function employeeVisitMapWeb(Request $request, $empId)
    {
        $date = $request->date;

        $visits = FieldVisitEntry::select(
            'field_visit_entries.*',
            'outlet_master.outlet_name',
            'employee_masters.emp_name'
        )
            ->leftJoin('outlet_master', 'outlet_master.id', '=', 'field_visit_entries.outlet_id')
            ->leftJoin('employee_masters', 'employee_masters.emp_id', '=', 'field_visit_entries.emp_id')
            ->where('field_visit_entries.emp_id', $empId)
            ->whereDate('field_visit_entries.visited_date', $date)
            ->orderBy('field_visit_entries.visited_at', 'asc')
            ->with('selfie') // 👈 eager load selfie
            ->get();

        $totalDistance = 0;
        $formatted = [];

        foreach ($visits as $i => $v) {
            $leggings    = (int)($v->leggings_qty ?? 0);
            $nonLeggings = (int)($v->non_leggings_qty ?? 0);
            $innerwear   = (int)($v->innerwear_qty ?? 0);
            $pcs         = $leggings + $nonLeggings + $innerwear;

            if ($i > 0) {
                $prev = $formatted[$i - 1];
                $totalDistance += $this->haversineDistance(
                    $prev['lat'],
                    $prev['lng'],
                    (float)$v->latitude,
                    (float)$v->longitude
                );
            }

            $formatted[] = [
                'lat'          => (float)$v->latitude,
                'lng'          => (float)$v->longitude,
                'name'         => $v->outlet_name ?? 'No Outlet',
                'emp_name'     => $v->emp_name ?? '',
                'time'         => $v->visited_at,
                'pcs'          => $pcs,
                'leggings'     => $leggings,
                'non_leggings' => $nonLeggings,
                'innerwear'    => $innerwear,
                'selfie_url'   => $v->selfie?->image_url ?? null, // 👈 add selfie URL
            ];
        }

        return response()->json([
            'visits'       => $formatted,
            'total_visits' => count($formatted),
            'total_km'     => round($totalDistance, 2),
            'total_pcs'    => array_sum(array_column($formatted, 'pcs')),
        ]);
    }

    // Add this helper in the same controller
    private function haversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $R = 6371; // Earth radius in km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
    public function visitMapPage()
    {
        $employees = Employee::select('emp_id', 'emp_name')
            ->orderBy('emp_name')
            ->get();

        return view('admin.map.VisitMap', compact('employees'));
    }
}
