<?php

namespace App\Jobs;

use App\Models\DatabaseBackup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;
use Illuminate\Support\Facades\Log;
class ProcessFullBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $userId) {}

    public function handle()
    {
        try {
            $backupPath = $this->performStorageBackup($this->userId);

            Log::info('Storage backup job completed successfully for user ' . $this->userId, [
                'backup_file' => $backupPath,
            ]);

            return $backupPath;
        } catch (\Exception $e) {
            Log::error('Storage backup failed for user ' . $this->userId, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    protected function performStorageBackup(int $user_id): string
    {
        Log::info('Starting storage backup for user ' . $user_id);

        $backupDir = storage_path('app/public/backups');

        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupFileName = 'storage_backup_' . $timestamp . '_' . Str::random(8) . '.zip';
        $backupFilePath = $backupDir . '/' . $backupFileName;

        $tempPath = tempnam(sys_get_temp_dir(), 'storage_backup_');

        try {
            $zip = new ZipArchive();
            if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \Exception("Cannot create zip file");
            }

            // Define exclusions for storage subdirectories we don't want to backup
            $exclusions = [
                'app/backups',    // Don't backup existing backups
                'debugbar',      // Debugbar files
                'logs',           // Log files
                'framework/cache', // Framework cache
                'framework/sessions', // Session files
                'framework/testing',  // Testing files
                'framework/views',    // Compiled views
            ];

            // Only backup the storage directory
            $storagePath = storage_path();
            $this->addStorageDirectoryToZip($storagePath, $zip, $exclusions);

            if (!$zip->close()) {
                throw new \Exception("Failed to save zip file");
            }

            // Move zip from temp path to backups directory
            rename($tempPath, $backupFilePath);

            // Save backup record in database
            DatabaseBackup::create([
                'user_id' => $user_id,
                'name'    => $backupFileName,
                'path'    => 'backups/' . $backupFileName,
                'type'    => 'storage' // Optional: Add this field to distinguish backup types
            ]);

            Log::info('Storage backup completed successfully', [
                'user_id' => $user_id,
                'backup_file' => $backupFilePath,
            ]);

            return $backupFilePath;
        } finally {
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
        }
    }

    protected function addStorageDirectoryToZip(string $directory, ZipArchive $zip, array $exclusions = [])
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativeFilePath = substr($filePath, strlen($directory) + 1);

                $shouldExclude = false;
                foreach ($exclusions as $exclusion) {
                    if (strpos($relativeFilePath, $exclusion) === 0) {
                        $shouldExclude = true;
                        break;
                    }
                }
                
                // Only include files from the storage directory
                if (!$shouldExclude) {
                    $zip->addFile($filePath, 'storage/' . $relativeFilePath);
                }
            }
        }
    }
}