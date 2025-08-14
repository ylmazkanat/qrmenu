<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Business;
use App\Models\Restaurant;
use App\Models\Category;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        // Create a business owner
        $businessOwner = User::create([
            'name' => 'Demo Business',
            'email' => 'demo@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'business_owner',
            'remember_token' => Str::random(10),
        ]);

        // Create a business
        $business = Business::create([
            'user_id' => $businessOwner->id,
            'name' => 'Demo Business',
            'email' => 'business@example.com',
            'phone' => '1234567890',
            'address' => '123 Demo Street',
            // 'is_active' => true, // Removed as per schema
        ]);

        // Create a restaurant manager
        $manager = User::create([
            'name' => 'Restaurant Manager',
            'email' => 'manager@example.com',
            'password' => bcrypt('password'),
            'role' => 'restaurant_manager',
        ]);

        // Create a restaurant
        $restaurant = Restaurant::create([
            'business_id' => $business->id,
            'restaurant_manager_id' => $manager->id,
            'name' => 'Demo Restaurant',
            'phone' => '0987654321',
            'address' => '456 Restaurant Avenue',
            'table_count' => 10,
            // 'is_active' => true, // Removed as per schema
        ]);

        // Create categories
        $categories = [
            'Appetizers',
            'Main Courses',
            'Desserts',
            'Drinks',
        ];

        foreach ($categories as $name => $description) {
            $category = Category::create([
                'restaurant_id' => $restaurant->id,
                'name' => $name,
                // 'slug' => Str::slug($name), // Removed as per schema
                // 'description' => $description, // Removed as per schema
                // 'is_active' => true, // Removed as per schema
            ]);

            // Create products for each category
            for ($i = 1; $i <= 5; $i++) {
                Product::create([
                    'restaurant_id' => $restaurant->id,
                    'category_id' => $category->id,
                    'name' => "$name Item $i",
                    // 'slug' => Str::slug("$name item $i"), // Removed as per schema
                    // 'description' => "Description for $name item $i", // Removed as per schema
                    'price' => rand(10, 100),
                    // 'is_active' => true, // Removed as per schema
                ]);
            }
        }

        // Create tables
        for ($i = 1; $i <= 10; $i++) {
            Table::create([
                'restaurant_id' => $restaurant->id,
                'name' => "Table $i",
                'capacity' => rand(2, 8),
                // 'is_active' => true, // Removed as per schema
            ]);
        }

        // Create staff members
        $roles = ['waiter', 'kitchen', 'cashier'];
        foreach ($roles as $role) {
            User::create([
                'name' => ucfirst($role),
                'email' => "$role@example.com",
                'password' => bcrypt('password'),
                'role' => $role,
            ]);
        }
    }
}
