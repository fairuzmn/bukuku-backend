<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@mail.com',
            'password' => Hash::make('admin'),
        ]);

        // 2. Create Tables
        $tables = ['Table 1', 'Table 2', 'Table 3', 'VIP 1', 'VIP 2'];
        foreach ($tables as $name) {
            Table::create([
                'name' => $name,
                'status' => 'available',
                'qr_code_path' => 'qrcodes/' . \Illuminate\Support\Str::slug($name) . '.png'
            ]);
        }

        // 3. Create Categories & Menu Items

        // Category: Drinks
        $drinks = Category::create(['name' => 'Beverages', 'description' => 'Cold and Hot Drinks']);
        MenuItem::insert([
            [
                'category_id' => $drinks->id,
                'name' => 'Ice Tea',
                'description' => 'Sweet jasmine tea',
                'price' => 5000,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => $drinks->id,
                'name' => 'Espresso',
                'description' => 'Strong black coffee',
                'price' => 18000,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Category: Main Course
        $mains = Category::create(['name' => 'Main Course', 'description' => 'Heavy meals']);
        MenuItem::insert([
            [
                'category_id' => $mains->id,
                'name' => 'Fried Rice Special',
                'description' => 'With egg and chicken',
                'price' => 25000,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_id' => $mains->id,
                'name' => 'Beef Burger',
                'description' => 'Cheese and double patty',
                'price' => 45000,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Category: Desserts
        $desserts = Category::create(['name' => 'Desserts', 'description' => 'Sweet treats']);
        MenuItem::create([
            'category_id' => $desserts->id,
            'name' => 'Chocolate Lava Cake',
            'description' => 'Melted chocolate inside',
            'price' => 22000,
            'is_active' => true
        ]);
    }
}
