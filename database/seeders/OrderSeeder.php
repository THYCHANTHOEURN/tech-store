<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Create some test users if they don't exist
        if (User::count() < 5) {
            User::factory(5)->create();
        }

        // Get all users except super admin
        $users = User::where('email', '!=', 'ctkh271102@gmail.com')->get();

        // Create 20 orders with different statuses
        foreach ($users as $user) {
            $numberOfOrders = rand(1, 4); // Random 1-4 orders per user

            for ($i = 0; $i < $numberOfOrders; $i++) {
                Order::create([
                    'user_id'           => $user->id,
                    'total_amount'      => 0, // Will be updated by OrderItemSeeder
                    'status'            => fake()->randomElement(['pending', 'processing', 'completed', 'cancelled']),
                    'shipping_address'  => $user->address,
                    'phone'             => $user->phone,
                    'payment_method'    => fake()->randomElement(['cash', 'card', 'bank_transfer']),
                    'payment_status'    => fake()->randomElement(['pending', 'paid', 'unpaid', 'failed']),
                ]);
            }
        }
    }
}
