<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        // Get all orders
        $orders = Order::all();

        // Get popular products to make them bestsellers
        $popularProducts = [
            'rog-ally',
            'rog-strix-g15',
            'msi-katana-gf66',
            'razer-blackwidow-v3',
            'rog-chakram-mouse',
        ];

        $products       = Product::whereIn('slug', $popularProducts)->get();
        $otherProducts  = Product::whereNotIn('slug', $popularProducts)->get();

        foreach ($orders as $order) {
            // Add 1-3 popular products to each order
            $numberOfPopularItems = rand(1, 3);
            for ($i = 0; $i < $numberOfPopularItems; $i++) {
                $product    = $products->random();
                $quantity   = rand(1, 3);
                $price      = $product->sale_price ?? $product->price;

                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $product->id,
                    'quantity'      => $quantity,
                    'price'         => $price,
                ]);
            }

            // Maybe add other products too
            if (rand(0, 1)) {
                $otherProduct   = $otherProducts->random();
                $quantity       = rand(1, 2);
                $price          = $otherProduct->sale_price ?? $otherProduct->price;

                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $otherProduct->id,
                    'quantity'      => $quantity,
                    'price'         => $price,
                ]);
            }

            // Update order total
            $order->total_amount = $order->orderItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            $order->save();
        }
    }
}
