<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // Module permissions
            'site_data.view',
            'site_data.create',
            'site_data.edit',
            'site_data.delete',

            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',
            
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            
            // System permissions
            'system.backup',
            'system.view_logs'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}