<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\Pharmacy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model =Product::class;

    public function definition(): array
    {
        return [
            'name'=>fake()->words(2,true),
            'description'=>fake()->optional()->sentence(12),
            'price'=>fake()->randomFloat(2,5,500),
            'quantity'=>fake()->numberBetween(0,100),
            'size'=>fake()->randomFloat(1,0.5,5),
            'image'=>fake()->unique()-> randomElement(  array_map(fn($n) => 'p'.$n.'.jpg', range(1, 22))),
            'category_id'=>Category::inRandomOrder()->value('id')??Category::factory()->create(),
            'pharmacy_id'=>Pharmacy::inRandomOrder()->value('id')??Pharmacy::factory()->create(),
            'is_available'=>fake()->boolean(),
        ];
    }


}
