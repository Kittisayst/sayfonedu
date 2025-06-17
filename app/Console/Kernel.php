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
        // ລຶບ info logs ທີ່ເກົ່າກວ່າ 30 ວັນ ທຸກໆອາທິດ
        $schedule->command('logs:clean --only-info --days=30')
            ->weekly()
            ->at('01:00');

        // ລຶບ logs ທຸກລະດັບທີ່ເກົ່າກວ່າ 90 ວັນ ທຸກໆເດືອນ
        $schedule->command('logs:clean --days=90')
            ->monthly()
            ->at('01:30');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
