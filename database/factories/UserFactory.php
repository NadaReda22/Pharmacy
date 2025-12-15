<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Pharmacy;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'phone'=>fake()->optional()->numerify('01#######'),
            'role'=>fake()->randomElement(['vendor','client']),
            'pharmacy_id'=>null,
            'license_file'=>null,

        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function owner(): static
    {
        return $this->state(fn (array $attributes) => [
            'role'=>'vendor',
            'license_file' => null,
        ])->afterCreating(
            function(User $user){
          $pharmacy = Pharmacy::factory()->create();

          $user->pharmacy_id=$pharmacy->id;
          $user->save();
            }
        );

    }

    public function client()
    {
        return $this->state(fn(array $attributes)=>
        [
        'role'=>'client',
        'license_file'=>null
        ]);
    }
}
