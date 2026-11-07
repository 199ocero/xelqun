<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'sort' => 0,
            'product' => fake()->words(2, true),
            'qty' => $qty = fake()->numberBetween(1, 5),
            'price' => $price = fake()->randomFloat(2, 5, 200),
            'subtotal' => round($qty * $price, 2),
        ];
    }
}
