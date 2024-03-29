<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        User::create([
            "username" => "testtsts",
            "password" => Hash::make('test123'),
            "name" => "test test test",
            "token" => "test"
        ]);

        User::create([
            "username" => "testtsts2",
            "password" => Hash::make('test1232'),
            "name" => "test test test2",
            "token" => "test2"
        ]);

    }
}
