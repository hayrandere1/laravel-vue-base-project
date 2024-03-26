<?php

namespace App\Console\Commands;

use App\Events\AdminReportEvent;
use App\Events\NotificationEvent;
use App\Models\Notification;
use App\Models\Session;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
            //  date_default_timezone_set('UTC');
            $sessions = Session::all();
            broadcast(new AdminReportEvent($sessions->toArray()));
            $now = time();
            foreach ($sessions as $session) {
                $lastActivity = $session->last_activity;
                if ($lastActivity < ($now - (5 * 60)) && $session->process == 0 && $lastActivity !== strtotime($session->created_at)) {
                    $session->update([
                        'process' => 1,
                    ]);
                }
            }
            Session::where('process', 1)->limit(100)->update([
                'process' => 2
            ]);
            $sessions = Session::where('process', 2)->orderBy('created_at')->get();
            $sessionDetail = [];
            $sessionDaily = [];
            $sessionHourly = [];
            foreach ($sessions as $session) {
                $duration = 0;
                if ($session->created_at) {
                    $duration = $session->last_activity - strtotime($session->created_at);
                }
                $sessionDetail[] = [
                    'date' => date('Y-m-d H:m:s', $session->last_activity),
                    'created_at' => $session->created_at,
                    'user_type' => $session->user_type,
                    'user_id' => $session->user_id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'duration' => $duration
                ];
                if (empty($session->created_at)) {
                    $session->created_at=date('Y-m-d H:m:s', $session->last_activity);
                }
                if (!empty($session->created_at)) {

                    $interval = date_diff(date_create(date_create(date('Y-m-d H:m:s', $session->last_activity))->format('Y-m-d H:00:00')), date_create(date_create($session->created_at)->format('Y-m-d H:00:00')));

                    $difference = ($interval->days * 24) + $interval->h;
                    $d2 = "";
                    for ($i = 0; $i <= $difference; $i++) {
                        if ($difference == 0) {
                            $d1 = date_create($session->created_at);
                            $d2 = date_create(date('Y-m-d H:m:s', $session->last_activity));
                        } else if ($i == 0) {
                            $d1 = date_create($session->created_at);
                            $d2 = date_create(date_create($session->created_at)->format('Y-m-d H:00:00'));
                            $d2->add(new \DateInterval('PT1H'));
                        } else {
                            $d1 = clone($d2);
                            $d2 = $d2->add(new \DateInterval('PT1H'));
                            if ($d2 > date_create(date('Y-m-d H:m:s', $session->last_activity))) {
                                $d2 = date_create(date('Y-m-d H:m:s', $session->last_activity));
                            }
                        }
                        $d = $d1->format('Y-m-d');
                        $h = $d1->format('H');

                        if (!isset($sessionDaily[$d][$session->user_type][$session->user_id][$session->ip_address])) {
                            $sessionDaily[$d][$session->user_type][$session->user_id][$session->ip_address]['duration'] = 0;
                            $sessionDaily[$d][$session->user_type][$session->user_id][$session->ip_address]['count'] = 0;
                        }
                        if (!isset($sessionHourly[$d][$h][$session->user_type][$session->user_id][$session->ip_address])) {
                            $sessionHourly[$d][$h][$session->user_type][$session->user_id][$session->ip_address]['duration'] = 0;
                            $sessionHourly[$d][$h][$session->user_type][$session->user_id][$session->ip_address]['count'] = 0;
                        }
                        $interval = date_diff($d2, $d1);
                        $sessionDaily[$d][$session->user_type][$session->user_id][$session->ip_address]['count']++;
                        $sessionHourly[$d][$h][$session->user_type][$session->user_id][$session->ip_address]['count']++;
                        $sessionDaily[$d][$session->user_type][$session->user_id][$session->ip_address]['duration'] += ($interval->h * 60 * 60) + ($interval->i * 60) + $interval->s;
                        $sessionHourly[$d][$h][$session->user_type][$session->user_id][$session->ip_address]['duration'] += ($interval->h * 60 * 60) + ($interval->i * 60) + $interval->s;
                    }

                }
            }

            $query = [];
            foreach ($sessionDetail as $data) {
                $query[] = '("'
                    . $data['date'] . '",'
                    . '"' . $data['created_at'] . '",'
                    . '"' . $data['user_type'] . '",'
                    . '"' . $data['user_id'] . '",'
                    . '"' . $data['ip_address'] . '",'
                    . '"' . $data['user_agent'] . '",'
                    . $data['duration'] . ')';

            }
            if (count($query) > 0) {
                $query = 'insert into report_session_details (`date`,`created_at`,`user_type`,`user_id`,`ip_address`,`user_agent`,`duration`) values ' . implode(',' . PHP_EOL, $query);

                DB::insert($query);
            }
            $query = [];
            foreach ($sessionDaily as $date => $userTypes) {
                foreach ($userTypes as $userType => $users) {
                    foreach ($users as $user => $ips) {
                        foreach ($ips as $ip => $data) {
                            $query[] = '("' . $date . '",' . '"' . $userType . '",' . '"' . $user . '",' . '"' . $ip . '",' . $data['duration'] . ',' . $data['count'] . ')';
                        }
                    }
                }

            }
            if (count($query) > 0) {
                $query = 'insert into report_session_dailies (`date`,`user_type`,`user_id`,`ip_address`,`duration`,`count`) values ' . implode(',' . PHP_EOL, $query) .
                    'ON DUPLICATE KEY UPDATE
                    `duration` = `duration` + VALUES(`duration`),
                    `count` = `count` + VALUES(`count`)';

                DB::insert($query);
            }

            $query = [];
            foreach ($sessionHourly as $date => $hours) {
                foreach ($hours as $hour => $userTypes) {
                    foreach ($userTypes as $userType => $users) {
                        foreach ($users as $user => $ips) {
                            foreach ($ips as $ip => $data) {
                                $query[] = '("' . $date . '","' . $hour . '",' . '"' . $userType . '",' . '"' . $user . '",' . '"' . $ip . '",' . $data['duration'] . ',' . $data['count'] . ')';
                            }
                        }
                    }
                }
            }


            if (count($query) > 0) {
                $query = 'insert into report_session_hourlies (`date`,`hour`,`user_type`,`user_id`,`ip_address`,`duration`,`count`) values ' . implode(',' . PHP_EOL, $query) .
                    'ON DUPLICATE KEY UPDATE
                    `duration` = `duration` + VALUES(`duration`),
                    `count` = `count` + VALUES(`count`)';

                DB::insert($query);
            }
            Session::where('process', 2)->delete();
            sleep(10);

        }
    }
}
