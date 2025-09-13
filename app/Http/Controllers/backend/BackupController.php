<?php

namespace App\Http\Controllers\backend;

use App\Helpers\QueryHelper;
use App\Http\Controllers\backend\HelperController;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessDbBackup;
use App\Jobs\ProcessFullBackup;
use App\Models\DatabaseBackup;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class BackupController extends Controller implements \Illuminate\Routing\Controllers\HasMiddleware
{
    protected $helper;

    public static function middleware(): array
    {
        return [
            new Middleware('can:system.backup', only: ['index']),
            new Middleware('can:system.backup', only: ['create', 'store']),
            new Middleware('can:system.backup', only: ['edit', 'update']),
            new Middleware('can:system.backup', only: ['destroy']),
        ];
    }
    public function __construct(HelperController $helper)
    {
        $this->helper = $helper;
    }

    public function index(Request $request, QueryHelper $helper)
    {
        return $this->helper->safeGet(function () use ($request, $helper) {
            $query = DatabaseBackup::with('user');

            $backups = $helper->paginateQuery(
                $query,
                $request,
                ['name', 'created_at', 'user.name'],
                'created_at',
                'desc'
            );

            return Inertia::render('Admin/Backup/Index', [
                'backups' => $backups,
                'filters' => $request->only(['search', 'sort', 'direction', 'perPage']),
            ]);
        });
    }

    public function deleteBackup(Request $request)
    {
        return $this->helper->safeGet(function () use ($request) {
            $relativePath = $request->input('path');
            $backupPath = storage_path('app/public/' . $relativePath);

            abort_unless(file_exists($backupPath) && is_file($backupPath), 404, 'File not found');

            return $this->helper->executeWithTransaction(
                function () use ($backupPath, $relativePath, $request) {
                    unlink($backupPath);
                    DatabaseBackup::where('path', $request->input('path'))->delete();
                    return redirect()->back()->with('message', 'Backup file and record deleted successfully.');
                },
                'Delete Backup',
                'Backup deletion failed',
                ['path' => $relativePath],
                DatabaseBackup::class
            );
        });
    }


    public function downloadBackup($filename)
    {
        return $this->helper->safeGet(function () use ($filename) {
            $backupDir = storage_path('app/public/backups');
            $filePath = $backupDir . '/' . $filename;

            if (!file_exists($filePath)) {
                abort(404, 'Backup file not found.');
            }

            return response()->download($filePath);
        });
    }



    public function backup()
    {
        return $this->helper->executeWithTransaction(
            function () {
                $userId = Auth::id();
                ProcessDbBackup::dispatch($userId);
                return redirect()->back()->with('message', 'Database backup has been started.');
            },
            'Database Backup',
            'Database backup failed',
            ['user_id' => Auth::id()],
            DatabaseBackup::class
        );
    }

    public function fullBackup()
    {
        return $this->helper->executeWithTransaction(
            function () {
                $userId = Auth::id();
                ProcessFullBackup::dispatch($userId);
                return redirect()->back()->with('message', 'Full backup has been started. It may take a few minutes.');
            },
            'Full Backup',
            'Full backup failed',
            ['user_id' => Auth::id()],
            DatabaseBackup::class
        );
    }
}
