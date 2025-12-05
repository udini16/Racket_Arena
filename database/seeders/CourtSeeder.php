<?php

namespace Database\Seeders;

use App\Models\Court;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Court::create(['name' => 'Court 1', 'is_active' => true]);
        Court::create(['name' => 'Court 2', 'is_active' => true]);
        Court::create(['name' => 'Court 3', 'is_active' => true]);
        Court::create(['name' => 'Court 4', 'is_active' => true]);
        Court::create(['name' => 'Court 5', 'is_active' => true]);
        Court::create(['name' => 'Court 6', 'is_active' => true]);
        Court::create(['name' => 'Court 7', 'is_active' => true]);
        Court::create(['name' => 'Court 8', 'is_active' => true]);
    }
}
