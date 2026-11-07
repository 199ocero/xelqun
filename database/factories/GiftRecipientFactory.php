<?php

namespace Database\Factories;

use App\Models\GiftRecipient;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GiftRecipient>
 */
class GiftRecipientFactory extends Factory
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
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
        ];
    }
}
