<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        // Auto load all console commands
        $this->load(__DIR__ . '/Commands');

        // Load closure based commands
        require base_path('routes/console.php');
    }

    protected function schedule(Schedule $schedule): void
{
    // No scheduled tasks
}
}
