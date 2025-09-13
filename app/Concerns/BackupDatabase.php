<?php

namespace App\Concerns;

use App\Models\DatabaseBackup;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\DbDumper\Databases\MySql;

trait BackupDatabase
{
    protected function performBackup($user_id)
    {
        try {
            $database  = config('database.connections.mysql.database');
            $username  = config('database.connections.mysql.username');
            $password  = config('database.connections.mysql.password');
            $host      = config('database.connections.mysql.host');
            $port      = config('database.connections.mysql.port');

            // Add .txt at the end to bypass restrictions
            $originalName = $database . '_backup_' . Str::random(8) . '.sql';
            $backupFileName = $originalName . '.txt'; // final filename: e.g., db_backup_Abc123.sql.txt
            $backupDir = storage_path('app/public/backups');
            $backupFilePath = $backupDir . '/' . $backupFileName;

            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            // Start logging
            Log::info('Starting database backup for user ' . $user_id, [
                'database' => $database,
                'backup_file' => $backupFilePath,
            ]);

            // Create backup using Spatie\DbDumper
            MySql::create()
                ->setDbName($database)
                ->setUserName($username)
                ->setPassword($password)
                ->setHost($host)
                ->setPort($port)
                ->dumpToFile($backupFilePath);

            // Save backup record
            DatabaseBackup::create([
                'user_id' => $user_id,
                'name' => $backupFileName,
                'path' => 'backups/' . $backupFileName,
            ]);

            // Log successful backup
            Log::info('Database backup completed successfully for user ' . $user_id, [
                'backup_file' => $backupFilePath,
            ]);

            return $backupFilePath;
        } catch (\Exception $e) {
            Log::error('Database backup failed for user ' . $user_id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
