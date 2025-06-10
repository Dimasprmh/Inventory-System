<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => (string) Str::uuid(),
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'email_verified_at' => null,
                'token' => Str::random(60),
                'password' => Hash::make('12345678'), // password default
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
