<?php

namespace App\Console;

use App\Console\Commands\SendNoticeRegisterTrademarkCommand;
use App\Console\Commands\UpdateAppTrademarkConfirm;
use App\Console\Commands\AutoRegisterTrademarkCommand;
use App\Console\Commands\SyncRegistrationTrademarkCommand;
use App\Console\Commands\SendRenewalDeadlineNoticeMorningCommand;
use App\Console\Commands\SendRenewalDeadlineNoticeAfternoonCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('command:request-user-pay')->daily()->withoutOverlapping();
        $schedule->command('command:delete-folder-temp')->weekly();
        $schedule->command('sync-notice:update-send-notice')->daily();
        $schedule->command(UpdateAppTrademarkConfirm::class)->everyMinute();
        $schedule->command('mail:send-before-deadline')->hourly();

        $schedule->command(SyncRegistrationTrademarkCommand::class)->dailyAt('01:00');
        $schedule->command(AutoRegisterTrademarkCommand::class)->dailyAt('01:00');

        // send notice extension
        $schedule->command(SendRenewalDeadlineNoticeMorningCommand::class)->dailyAt('01:00');
        $schedule->command(SendRenewalDeadlineNoticeAfternoonCommand::class)->dailyAt('12:00');

        //send notice to register trademark
        $schedule->command(SendNoticeRegisterTrademarkCommand::class)->dailyAt('22:00');

        // Send mail remind daily
        $schedule->command('send-mail:remind')->dailyAt('01:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
