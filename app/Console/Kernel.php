<?php

namespace App\Console;

use App\Jobs\Ebonds\AfterDueDateJob;
use App\Jobs\Ebonds\BeforeDueDateJob;
use App\Jobs\Ebonds\DueDateJob;
use App\Jobs\Ebonds\PeriodicUpdateEbondsJob;
use App\Jobs\Products\TriggerPlansUpdate;
use App\Jobs\UpdateBankInstallmentsJob;
use App\Models\VerificationCode;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            VerificationCode::whereDate('created_at', '<=', now()->subHours(1))->delete();
        })->hourly();

        $schedule->call(function () {
            dispatch(new UpdateBankInstallmentsJob);
        })->dailyAt('06:30');

        $schedule->call(function () {
            dispatch(new TriggerPlansUpdate);
        })->dailyAt('06:00');

        $schedule->command('app:zip-logs')->weekly();

        $schedule->command('app:generate-sitemap')->dailyAt("06:00");

        $schedule->call(function () {
            dispatch(new BeforeDueDateJob);
            dispatch(new DueDateJob);
            dispatch(new AfterDueDateJob);
        })->dailyAt('09:00');

        $schedule->call(function () {
            dispatch(new PeriodicUpdateEbondsJob);
        })->dailyAt('20:00');
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
