<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:send-birthday-notifications')
        ->dailyAt('06:00')
        ->timezone('Asia/Jakarta');

Schedule::command('app:send-holiday-info')
        ->dailyAt('06:00')
        ->timezone('Asia/Jakarta');

Schedule::command('app:send-client-birthday')
        ->dailyAt('07:00')
        ->timezone('Asia/Jakarta');