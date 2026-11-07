<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => ucfirst(fake()->word()).' '.fake()->word(),
            'sku' => fake()->unique()->bothify('??-#####'),
            'status' => fake()->randomElement(['draft', 'active', 'archived']),
            'price' => fake()->randomFloat(2, 5, 900),
            'stock' => fake()->numberBetween(0, 250),
            'is_featured' => fake()->boolean(20),
            'released_at' => fake()->optional(0.8)->date(),
        ];
    }
}
