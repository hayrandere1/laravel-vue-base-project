<?php

namespace App\Jobs;

use App\Events\ArchiveEvent;
use App\Events\NotificationEvent;
use App\Libraries\Helper;
use App\Models\Archive;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateCsvJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $limit = 1;
    private Archive $archive;

    /**
     * Create a new job instance.
     */
    public function __construct(Archive $archive)
    {
        $this->archive = $archive;
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::withContext(['archive_id' => $this->archive->id]);
        try {

            app()->setLocale($this->archive->language);
            $fileName = 'public/list_csv/' . $this->archive->unique_id . '.csv';

            $realCount = $this->dataProcess($fileName);

            $this->archive->update(['status' => 'Finished', 'total_count' => $realCount]); //total_count = REAL_COUNT
            broadcast(new ArchiveEvent($this->archive, 'Finished'));

            $notification = Notification::create([
                'user_id' => $this->archive->user_id,
                'title' => 'Download Completed',
                'content' => 'FileName:' . $this->archive->file_name .
                    ' Date:' . $this->archive->created_at->format('Y-m-d'),
                'link' => 'archive/' . $this->archive->id,
                'is_read' => false,
                'type' => $this->archive->type
            ]);
            broadcast(new NotificationEvent($notification));
        } catch (\Throwable $throwable) {
            logger()->error("Generate Csv Throwable", [
                'error_code' => $throwable->getCode(),
                'error_message' => $throwable->getMessage()
            ]);
            report($throwable);
            try {
                if (isset($fileName) && file_exists(Storage::path($fileName))) {
                    unlink(Storage::path($fileName));
                }
                $this->archive->update(['completed_count' => 0, 'status' => 'Pending']);
                $this->release(300);
            } catch (\Throwable $t) {
                report($t);
                $this->fail($t);
            }
        }
    }

    /**
     * @param $fileName
     * @return int
     */
    private function dataProcess($fileName): int
    {
        $realCount = 0;
        $skip = 0;
        $columns = json_decode($this->archive->columns, true);
        $queryAll = $this->archive->sql;
        while (true) {
            $query = $queryAll . ' limit ' . ($skip * $this->limit) . ',' . $this->limit;
            $results = DB::select($query, json_decode($this->archive->parameters, true));
            $resultCount = count($results);
            $realCount += $resultCount;

            if ($resultCount) {
                if ($skip == 0) {
                    $this->writeHeader($columns, $fileName);
                }

                foreach ($results as $result) {
                    $result = (array)$result;
                    $row = [];
                    foreach ($columns as $column) {
                        $row[] = str_replace(';', ' ', $result[$column]);
                    }
                    file_put_contents(Storage::path($fileName), implode(';', $row) . PHP_EOL, FILE_APPEND);
                }
            }
            $this->archive->update(['completed_count' => $realCount]);
            broadcast(new ArchiveEvent($this->archive, 'Processing'));
            sleep(1);
            if ($resultCount < $this->limit) {
                break;
            }
            $skip++;
        }
        return $realCount;
    }

    /**
     * @param $columns
     * @param $fileName
     */
    private function writeHeader($columns, $fileName)
    {
        $this->archive->update(['status' => 'Processing']);
        broadcast(new ArchiveEvent($this->archive, 'Processing'));
        $row = [];
        foreach ($columns as $key => $value) {
            $row[] = Str::upper(__($key));
        }
        file_put_contents(
            Storage::path($fileName),
            chr(0xEF) . chr(0xBB) . chr(0xBF) . implode(';', $row) . PHP_EOL
        );
    }

}
