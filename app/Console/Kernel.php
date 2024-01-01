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
        $port = env('PUSHER_PORT', 6001);
        $path = str_replace(DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Console', '', __DIR__);

        $schedule->call(function () use ($path) {
            shell_exec("php " . $path . "/artisan websockets:restart &");
        })->dailyAt('03:00');

        $schedule->call(function () use ($path) {
            $runningPids = shell_exec("pgrep -fl \"" . $path . "/artisan queue:work\"");
            $explode = explode(' php', $runningPids);
            if (count($explode) < 2) {
//                $schedule->command('queue:work --daemon');
                shell_exec("php " . $path . DIRECTORY_SEPARATOR . "artisan queue:work > " . $path . "/storage/logs/laravel_queue.log &");
            }
        })->everyFiveMinutes();

        $schedule->call(function () use ($path) {
            $runningPids = shell_exec("pgrep -fl \"" . $path . DIRECTORY_SEPARATOR . "artisan queue:work --queue=csv_generate\"");
            $explode = explode(' php', $runningPids);
            if (count($explode) < 2) {
                shell_exec("php " . $path . DIRECTORY_SEPARATOR . "artisan queue:work --queue=csv_generate > " . $path . "/storage/logs/laravel_queue.log &");
            }
        })->everyFiveMinutes();
        $schedule->call(function () use ($path, $port) {
            $running_pids = shell_exec("pgrep -fl \"" . $path . DIRECTORY_SEPARATOR . "artisan websocket:serve\"");
            $explode = explode(' php', $running_pids);
            if (count($explode) < 2) {
                shell_exec("php " . $path . DIRECTORY_SEPARATOR . "artisan websocket:serve --host=0.0.0.0 --port=" . $port . " > " . $path . "/storage/logs/laravel_websocket.log &");
            }
        })->everyFiveMinutes();
        $schedule->command('telescope:prune')->daily();
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
