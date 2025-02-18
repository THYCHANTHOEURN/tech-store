<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function configure()
    {
        return $this->afterCreating(function (Product $product) {
            // Add a default image for each product
            ProductImage::create([
                'product_id' => $product->id,
                'image' => 'products/default.jpg',
                'is_primary' => true
            ]);
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        return [
            'uuid' => fake()->uuid(),
            'category_id' => Category::inRandomOrder()->first()->id,
            'brand_id' => Brand::inRandomOrder()->first()->id,
            'name' => ucwords($name),
            'slug' => Str::slug($name),
            'sku' => strtoupper(Str::random(8)),
            'description' => fake()->paragraphs(3, true),
            'price' => fake()->randomFloat(2, 10, 1000),
            'sale_price' => fake()->optional(0.3)->randomFloat(2, 5, 900),
            'stock' => fake()->numberBetween(0, 100),
            'featured' => fake()->boolean(20),
            'status' => true,
        ];
    }
}
