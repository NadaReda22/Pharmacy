<?php

namespace Database\Seeders;

use App\Models\Pharmacy;
use Illuminate\Database\Seeder;
use Database\Factories\PharmacyFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PharmacySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      Pharmacy::factory()->count(20)->create();
    }
}
