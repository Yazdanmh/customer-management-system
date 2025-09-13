<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Yazdan Mohammadi',
            'email' => 'admin@gmail.com',
            'profile_image' => 'default-user.png',
            'password' => Hash::make('123123123'), 
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
