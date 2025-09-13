<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\backend\SettingController;
use App\Http\Controllers\backend\DashboardController;
use App\Http\Controllers\backend\BackupController;
use App\Http\Controllers\backend\CustomersController;
use App\Http\Controllers\backend\RolesController;
use App\Http\Controllers\backend\UsersController;
use App\Http\Middleware\PassUserDataToViews;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', '/auth/login');

Route::middleware(['auth', PassUserDataToViews::class])
    ->prefix('admin')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Customers
        Route::delete('/customers/bulk-delete', [CustomersController::class, 'bulkDelete'])->name('customers.bulkDelete');
        Route::get('/customers/trash', [CustomersController::class, 'trash'])->name('customers.trash');
        Route::post('/customers/{id}/restore', [CustomersController::class, 'restore'])->name('customers.restore');
        Route::delete('/customers/{id}/force-delete', [CustomersController::class, 'forceDelete'])->name('customers.forceDelete');
        Route::delete('/customers/bulk-force-delete', [CustomersController::class, 'bulkForceDelete'])->name('customers.bulkForceDelete');
        Route::post('/customers/bulk-restore', [CustomersController::class, 'bulkRestore'])->name('customers.bulkRestore');
        Route::post('/customers/{customer}/toggle-status', [CustomersController::class, 'toggleStatus'])->name('customers.toggle-status'); // optional, similar to togglePublish

        Route::resource('/customers', CustomersController::class)->except(['update']);
        Route::post('/customers/{customer}', [CustomersController::class, 'update'])->name('customers.update');
        Route::get('/export', [CustomersController::class, 'export'])->name('export'); 
       

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])
            ->name('settings.index');

        Route::post('/settings', [SettingController::class, 'update'])
            ->name('settings.update');

        // Roles
        Route::get('/roles/trash', [RolesController::class, 'trash'])->name('roles.trash');
        Route::post('/roles/{id}/restore', [RolesController::class, 'restore'])->name('roles.restore');
        Route::post('/roles/{id}/force-delete', [RolesController::class, 'forceDelete'])->name('roles.forceDelete');
        Route::post('/roles/bulk-delete', [RolesController::class, 'bulkDelete'])
            ->name('roles.bulkDelete');
        Route::post('/roles/bulk-restore', [RolesController::class, 'bulkRestore'])->name('roles.bulkRestore');
        Route::post('/roles/bulk-force-delete', [RolesController::class, 'bulkForceDelete'])->name('roles.bulkForceDelete');
        Route::resource('/roles', RolesController::class)->names([
            'index'   => 'roles.index',
            'create'  => 'roles.create',
            'store'   => 'roles.store',
            'show'    => 'roles.show',
            'edit'    => 'roles.edit',
            'update'  => 'roles.update',
            'destroy' => 'roles.destroy',
        ]);

        // Users
        Route::delete('/users/bulk-delete', [UsersController::class, 'bulkDelete'])->name('users.bulkDelete');
        Route::get('/users/trash', [UsersController::class, 'trash'])->name('users.trash');
        Route::post('/users/{id}/restore', [UsersController::class, 'restore'])->name('users.restore');
        Route::delete('/users/{id}/force-delete', [UsersController::class, 'forceDelete'])->name('users.forceDelete');
        Route::delete('/users/bulk-force-delete', [UsersController::class, 'bulkForceDelete'])->name('users.bulkForceDelete');
        Route::post('/users/bulk-restore', [UsersController::class, 'bulkRestore'])->name('users.bulkRestore');
        Route::put('/users/{id}/change-password', [UsersController::class, 'changePassword'])->name('users.changePassword');

        Route::resource('/users', UsersController::class)->names([
            'index'   => 'users.index',
            'create'  => 'users.create',
            'store'   => 'users.store',
            'show'    => 'users.show',
            'edit'    => 'users.edit',
            'update'  => 'users.update',
            'destroy' => 'users.destroy',
        ]);

        // Backups
        Route::get('/backup/list', [BackupController::class, 'index'])->name('backup.index');
        Route::get('/backups/download/{filename}', [BackupController::class, 'downloadBackup'])->name('backup.download');
        Route::delete('/backups/delete/', [BackupController::class, 'deleteBackup'])->name('backup.delete');
        Route::get('/backup/db/now', [BackupController::class, 'backup'])->name('backup.db');
        Route::get('/backup/file/now', [BackupController::class, 'filebackup'])->name('backup.file');

        // Logs
        Route::get('/system/logs', [SettingController::class, 'logs'])->name('system.logs');

        // Your existing backup routes
        Route::post('/backup/db/now', [BackupController::class, 'backup'])->name('backup.db');
        Route::post('/backup/full/now', [BackupController::class, 'fullBackup'])->name('backup.full');
        Route::post('/backup/file/now', [BackupController::class, 'filebackup']);

    });


Route::middleware(['auth', PassUserDataToViews::class])->group(function () {
    Route::get('/admin/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/admin/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/admin/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/history', function () {
    return 'adf';
})->name('history.index');
require __DIR__ . '/auth.php';
