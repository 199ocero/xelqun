<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\OrderItemExtra;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItemExtra>
 */
class OrderItemExtraFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_item_id' => OrderItem::factory(),
            'name' => fake()->words(2, true),
            'price' => fake()->randomFloat(2, 1, 50),
        ];
    }
}
