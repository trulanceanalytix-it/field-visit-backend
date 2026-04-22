<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('report:send-daily-visit')
    ->days([1, 2, 3, 4, 5, 6]) // 1=Mon, 2=Tue, 3=Wed, 4=Thu, 5=Fri, 6=Sat
    ->at('08:30');
