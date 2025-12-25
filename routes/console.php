<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule appointment reminders
Schedule::command('appointments:send-reminders 24h')
    ->hourly()
    ->description('Send 24-hour appointment reminders');

Schedule::command('appointments:send-reminders 1h')
    ->everyFifteenMinutes()
    ->description('Send 1-hour appointment reminders');
