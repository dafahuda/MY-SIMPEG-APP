<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Reminder KGB jatuh tempo (setiap hari jam 07:00) ke admin & superadmin
        $schedule->command('kgb:reminder')->dailyAt('07:00');

        // Backup database harian (jam 02:00) + retensi 30 hari
        $schedule->command('backup:database --days=30')->dailyAt('02:00');

        // Bersihkan notifikasi lebih tua dari 90 hari (mingguan, Minggu jam 03:00)
        $schedule->command('notifikasi:prune --days=90')->weeklyOn(0, '03:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
