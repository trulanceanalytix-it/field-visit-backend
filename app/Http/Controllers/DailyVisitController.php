<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FieldVisitEntry;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DailyVisitsExport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\DailyVisitReportMail;
use Illuminate\Http\RedirectResponse;


class DailyVisitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function sendReport(): RedirectResponse
    {
        $today = Carbon::today();

        $reportDate = match ((int) $today->dayOfWeek) {
            Carbon::MONDAY => $today->copy()->subDays(2),
            Carbon::SUNDAY => $today->copy()->subDays(2),
            default => $today->copy()->subDay(),
        };

        $dateLabel = $reportDate->format('l, d M Y');
        $recipients = array_map('trim', explode(',', config('mail.mis_lead_email')));

        Mail::to($recipients)->send(
            new DailyVisitReportMail($reportDate->toDateString(), $dateLabel)
        );

        $emailList = implode(', ', $recipients);

        return back()->with('report_sent', "✅ Report for {$dateLabel} sent to {$emailList} successfully!");
    }
    public function index()
    {
        // ✅ Just return the view — NO data fetch here
        return view('admin.daily-visits.index');
    }

    // ✅ New AJAX endpoint for DataTables
    public function datatable(Request $request)
    {
        $query = FieldVisitEntry::with([
            'distributor:id,distributor_name',
            'beat:id,beat_name',
            'outlet:id,outlet_name'
        ]);

        // 🔍 Filters
        if ($request->filled('emp_id')) {
            $query->where('emp_id', 'ilike', '%' . $request->emp_id . '%');
        }

        if ($request->filled('emp_name')) {
            $query->where('emp_name', 'ilike', '%' . $request->emp_name . '%');
        }

        if ($request->filled('distributor')) {
            $query->whereHas(
                'distributor',
                fn($q) =>
                $q->where('distributor_name', 'ilike', '%' . $request->distributor . '%')
            );
        }

        if ($request->filled('beat')) {
            $query->whereHas(
                'beat',
                fn($q) =>
                $q->where('beat_name', 'ilike', '%' . $request->beat . '%')
            );
        }

        if ($request->filled('outlet')) {
            $query->whereHas(
                'outlet',
                fn($q) =>
                $q->where('outlet_name', 'ilike', '%' . $request->outlet . '%')
            );
        }

        if ($request->filled('date_from')) {
            $query->whereDate('visited_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('visited_date', '<=', $request->date_to);
        }

        // 📊 Counts
        $total = FieldVisitEntry::count();
        $filtered = $query->count();

        // ✅ Sorting — match column index to actual DB column
        $columns = [
            0 => 'id',
            1 => 'emp_id',
            2 => 'emp_name',
            3 => 'leggings_qty',
            4 => 'non_leggings_qty',
            5 => 'innerwear_qty',
            6 => 'total_pcs',
            7 => 'stock_leggings',
            8 => 'stock_non_leggings',
            9 => 'stock_innerwear',
            10 => 'stock_leggings',   // total stock (computed, sort by leggings)
            11 => 'visited_date',
            12 => 'visited_at',
            13 => 'distributor_id',   // relation — sort by id only
            14 => 'beat_id',
            15 => 'outlet_id',
        ];

        $orderColIndex = $request->input('order.0.column', 11);
        $orderCol = $columns[$orderColIndex] ?? 'visited_at';
        $orderDir = $request->input('order.0.dir', 'desc');

        $query->orderBy($orderCol, $orderDir);

        // 📄 Pagination
        $rows = $query
            ->offset($request->input('start', 0))
            ->limit($request->input('length', 10))
            ->get();

        // 🗂 Format rows
        $data = $rows->map(function ($visit, $index) use ($request) {
            $stockTotal = ($visit->stock_leggings ?? 0)
                + ($visit->stock_non_leggings ?? 0)
                + ($visit->stock_innerwear ?? 0);

            return [
                $request->input('start', 0) + $index + 1,           // 0  S.No
                $visit->emp_id,                                       // 1
                $visit->emp_name,                                     // 2
                $visit->leggings_qty ?? 0,                        // 3
                $visit->non_leggings_qty ?? 0,                        // 4
                $visit->innerwear_qty ?? 0,                        // 5
                $visit->total_pcs ?? 0,                        // 6
                $visit->stock_leggings ?? 0,                        // 7
                $visit->stock_non_leggings ?? 0,                      // 8
                $visit->stock_innerwear ?? 0,                        // 9
                $stockTotal,                                          // 10
                \Carbon\Carbon::parse($visit->visited_date)->format('d/m/y'), // 11
                \Carbon\Carbon::parse($visit->visited_at)->format('h:i A'),   // 12
                $visit->distributor->distributor_name ?? '--',        // 13
                $visit->beat->beat_name ?? '--',        // 14
                $visit->outlet->outlet_name ?? '--',        // 15
            ];
        });

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ]);
    }
    public function export(Request $request)
    {
        $filters = [
            'emp_id' => $request->get('emp_id'),
            'emp_name' => $request->get('emp_name'),
            'distributor' => $request->get('distributor'),
            'beat' => $request->get('beat'),
            '   ' => $request->get('outlet'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        $fileName = 'VisitEntries_' . now()->format('d-m-Y_H-i-s') . '.xlsx';

        $response = Excel::download(new DailyVisitsExport($filters), $fileName); // ← pass filters

        $response->headers->setCookie(
            cookie('fileDownload', 'true', 1, '/', null, false, false, false, 'Lax')
        );

        return $response;
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
