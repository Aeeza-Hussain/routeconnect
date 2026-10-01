<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@routeconnect.com'],
            [
                'name' => 'RouteConnect Admin',
                'phone' => '03001234567',
                'password' => Hash::make('admin123456'),
                'role' => 'admin',
                'driver_status' => null,
            ]
        );
    }
}
