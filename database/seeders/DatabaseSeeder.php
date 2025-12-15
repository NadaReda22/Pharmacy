<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
      

      $this->call([
        // PharmacySeeder::class,
        // CategorySeeder::class,
        ProductSeeder::class,
      ]);
//         User::factory()->owner()->count(10)->create();
//         User::factory()->client()->count(10)->create();
//
 }
}