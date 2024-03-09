<?php

namespace App\Console\Commands;

use App\Events\AdminReportEvent;
use App\Events\NotificationEvent;
use App\Models\Notification;
use App\Models\Session;
use Illuminate\Console\Command;

class ReportAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'report all';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        if (file_exists(__DIR__ . '/report_all_stop.txt')) {
            return 0;
        }

        while (true) {
            if (file_exists(__DIR__ . '/report_all_stop.txt')) {
                return 0;
            }

            $sessions = Session::all()->toArray();
            broadcast(new AdminReportEvent($sessions));
            sleep(1);

        }
    }
}
