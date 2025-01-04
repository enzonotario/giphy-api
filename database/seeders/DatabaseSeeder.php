<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Passport Personal Access Client.
        Artisan::call('passport:client', [
            '--personal' => true,
            '--name' => env('TOKEN_NAME', 'Laravel Personal Access Client'),
        ]);
    }
}
