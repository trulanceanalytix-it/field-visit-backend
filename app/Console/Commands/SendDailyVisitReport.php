<?php

namespace app\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\DailyVisitReportMail;

class SendDailyVisitReport extends Command
{
    protected $signature   = 'report:send-daily-visit';
    protected $description = 'Send previous day daily visit report to MIS Team Lead';
protected $commands = [
    BackfillFieldVisitEntries::class,
    \App\Console\Commands\SendDailyVisitReport::class, // ? add this
];

    public function handle(): void
    {
        $today = Carbon::today();

        $reportDate = match ((int) $today->dayOfWeek) {
            Carbon::MONDAY => $today->copy()->subDays(2),
            Carbon::SUNDAY => $today->copy()->subDays(2),
            default        => $today->copy()->subDay(),
        };

        $dateLabel  = $reportDate->format('l, d M Y');

        // ✅ Split and trim each email into a clean array
        $recipients = collect(explode(',', config('mail.mis_lead_email')))
            ->map(fn($email) => trim($email))
            ->filter()
            ->values()
            ->toArray();

        Mail::to($recipients)->send(
            new DailyVisitReportMail($reportDate->toDateString(), $dateLabel)
        );

        $this->info("✅ Report for {$dateLabel} sent to " . implode(', ', $recipients));
    }
}
