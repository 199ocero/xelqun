<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\OrderItemPhoto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItemPhoto>
 */
class OrderItemPhotoFactory extends Factory
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
            'path' => 'demo/orders/photos/'.fake()->uuid().'.png',
            'sort' => 0,
        ];
    }
}
