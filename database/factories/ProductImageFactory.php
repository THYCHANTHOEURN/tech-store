<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'image' => 'products/default.jpg',
            'is_primary' => false
        ];
    }

    public function primary()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_primary' => true
            ];
        });
    }
}
