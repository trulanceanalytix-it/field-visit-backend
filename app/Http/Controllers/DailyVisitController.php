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
            default        => $today->copy()->subDay(),
        };

        $dateLabel  = $reportDate->format('l, d M Y');
        $recipients = array_map('trim', explode(',', config('mail.mis_lead_email')));

        Mail::to($recipients)->send(
            new DailyVisitReportMail($reportDate->toDateString(), $dateLabel)
        );

        $emailList = implode(', ', $recipients);

        return back()->with('report_sent', "✅ Report for {$dateLabel} sent to {$emailList} successfully!");
    }
    public function index()
    {
        $visits = FieldVisitEntry::with([
            'distributor:id,distributor_name',
            'beat:id,beat_name',
            'outlet:id,outlet_name'
        ])
            ->orderBy('visited_at', 'desc')
            ->get();

        return view('admin.daily-visits.index', compact('visits'));
    }
    public function export(Request $request)
    {
        $filters = [
            'emp_id'      => $request->get('emp_id'),
            'emp_name'    => $request->get('emp_name'),
            'distributor' => $request->get('distributor'),
            'beat'        => $request->get('beat'),
            'outlet'      => $request->get('outlet'),
            'date'        => $request->get('date'),
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
