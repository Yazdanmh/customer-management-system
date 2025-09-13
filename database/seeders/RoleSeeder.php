<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $role = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);

        $permissions = Permission::all();
        $role->syncPermissions($permissions);

        $user = User::where('email', 'admin@gmail.com')->first();

        if ($user) {
            $user->assignRole($role);
        }
    }
}
