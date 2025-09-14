<?php

namespace App\Jobs;

use App\Concerns\BackupDatabase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessDbBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels,  BackupDatabase;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public int $userId)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $backup_file = $this->performBackup($this->userId);

            Log::info('Database backup created successfully', [
                'user_id' => $this->userId,
                'file' => $backup_file
            ]);
        } catch (\Exception $e) {
            Log::error('Database backup failed', [
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
