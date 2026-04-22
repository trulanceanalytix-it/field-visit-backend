<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillFieldVisitEntries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backfill-field-visit-entries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::transaction(function () {

            $entries = DB::table('field_visit_entries')
                ->whereNull('distributor_id')
                ->get();

            foreach ($entries as $entry) {

                $distributorId = DB::table('distributor_master')
                    ->where('name', $entry->distributor_name)
                    ->value('id');

                $beatId = DB::table('beat_master')
                    ->where('name', $entry->beat_name)
                    ->value('id');

                $outletId = DB::table('outlet_master')
                    ->where('name', $entry->outlet_name)
                    ->value('id');

                DB::table('field_visit_entries')
                    ->where('id', $entry->id)
                    ->update([
                        'distributor_id' => $distributorId,
                        'beat_id'        => $beatId,
                        'outlet_id'      => $outletId,
                    ]);
            }
        });

        $this->info('✅ Field visit entries backfilled successfully');
    }
}
