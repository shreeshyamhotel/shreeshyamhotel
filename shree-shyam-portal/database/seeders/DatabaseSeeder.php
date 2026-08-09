<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed default rooms from the public website catalog
        \App\Models\Room::updateOrCreate(
            ['room_number' => '101'],
            [
                'room_type' => 'Deluxe Room',
                'price_per_night' => 2499.00,
                'status' => 'Available',
            ]
        );
        
        \App\Models\Room::updateOrCreate(
            ['room_number' => '102'],
            [
                'room_type' => 'Deluxe Room',
                'price_per_night' => 2499.00,
                'status' => 'Available',
            ]
        );

        \App\Models\Room::updateOrCreate(
            ['room_number' => '201'],
            [
                'room_type' => 'Executive Suite',
                'price_per_night' => 4499.00,
                'status' => 'Available',
            ]
        );

        \App\Models\Room::updateOrCreate(
            ['room_number' => '202'],
            [
                'room_type' => 'Executive Suite',
                'price_per_night' => 4499.00,
                'status' => 'Available',
            ]
        );

        \App\Models\Room::updateOrCreate(
            ['room_number' => '301'],
            [
                'room_type' => 'Royal Family Suite',
                'price_per_night' => 6999.00,
                'status' => 'Available',
            ]
        );

        \App\Models\Room::updateOrCreate(
            ['room_number' => '302'],
            [
                'room_type' => 'Royal Family Suite',
                'price_per_night' => 6999.00,
                'status' => 'Available',
            ]
        );

        // Seed default user account
        User::updateOrCreate(
            ['email' => 'shyamhotel@gmail.com'],
            [
                'name' => 'Shree Shyam Admin',
                'password' => bcrypt('Shyam@2026'),
            ]
        );
    }
}
