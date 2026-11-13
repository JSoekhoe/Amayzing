<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Schedule::command('newsletter:send-halloween --send')
    ->dailyAt('09:00')
    ->timezone('Europe/Amsterdam')
    ->withoutOverlapping();
