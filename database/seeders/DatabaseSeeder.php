<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Brand;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@ibex.local'],
            [
                'name'     => 'IBEX Admin',
                'password' => Hash::make('password'),
            ]
        );
    }
}
