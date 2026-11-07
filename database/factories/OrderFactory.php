<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'type' => 'individual',
            'company' => null,
            'vat_number' => null,
            'delivery_date' => fake()->dateTimeBetween('+1 day', '+1 month'),
            'is_gift' => false,
            'notes' => null,
            'total' => 0,
        ];
    }

    /**
     * A business order with company details.
     */
    public function business(): static
    {
        return $this->state(fn (): array => [
            'type' => 'business',
            'company' => fake()->company(),
            'vat_number' => strtoupper(fake()->bothify('??#########')),
        ]);
    }
}
