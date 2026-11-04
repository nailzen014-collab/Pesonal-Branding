<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Sinkron otomatis repository GitHub ke halaman portofolio (FR-14).
// Jalankan `php artisan schedule:run` setiap menit lewat cron/Task Scheduler,
// atau biarkan `php artisan serve` + `php artisan schedule:work` saat dev.
Schedule::command('portfolio:import-github --publish --include-forks')
    ->hourly()
    ->withoutOverlapping()
    ->onOneServer()
    ->name('portfolio:sync-github');

// Artikel blog juga bisa ditulis sebagai Markdown di folder `blog` pada
// repository GitHub. Dijalankan bergeser 5 menit agar tidak menumpuk request.
Schedule::command('blog:import-github')
    ->hourlyAt(5)
    ->withoutOverlapping()
    ->onOneServer()
    ->name('blog:sync-github');
