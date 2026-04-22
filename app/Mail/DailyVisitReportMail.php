<?php
// app/Mail/DailyVisitReportMail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DailyVisitsExport;

class DailyVisitReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $reportDate;
    public string $dateLabel;

    public function __construct(string $reportDate, string $dateLabel)
    {
        $this->reportDate = $reportDate;   // Y-m-d  → used for query filter
        $this->dateLabel  = $dateLabel;    // "Saturday, 12 Apr 2025" → used in subject/body
    }

    public function build(): self
    {
        $fileName = 'daily_visit_report_' . $this->reportDate . '.xlsx';

        return $this
            ->subject('Daily Visit Report — ' . $this->dateLabel)
            ->view('emails.daily_visit_report')
            ->attachData(
                // ✅ Passes date into your existing DailyVisitsExport via filters array
                Excel::raw(
                    new DailyVisitsExport(['date' => $this->reportDate]),
                    \Maatwebsite\Excel\Excel::XLSX
                ),
                $fileName,
                ['mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
            );
    }
}
