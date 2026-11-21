<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use App\Services\SaleService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Staff (passwords are hashed by the User model cast) ---
        $admin = User::firstOrCreate(['username' => 'admin'], [
            'full_name' => 'Store Administrator',
            'password' => 'admin12345',
        ]);
        $cashier = User::firstOrCreate(['username' => 'cashier'], [
            'full_name' => 'Maria Santos',
            'password' => 'cashier12345',
        ]);

        // Only seed demo data once, on a fresh database.
        if (Product::exists()) {
            return;
        }

        // --- Products ---
        $products = collect([
            ['Arabica Coffee Beans 250g', 385.00, 40],
            ['Cold Brew Bottle 500ml', 165.00, 25],
            ['Butter Croissant', 85.00, 18],
            ['Blueberry Muffin', 95.00, 12],
            ['Matcha Latte Powder 200g', 520.00, 8],
            ['Reusable Tumbler 16oz', 650.00, 15],
            ['Ceramic Mug', 275.00, 4],
            ['Paper Filters (100 pcs)', 140.00, 0],
        ])->map(fn ($p) => Product::create(['name' => $p[0], 'price' => $p[1], 'stock_quantity' => $p[2]]));

        // --- Customers ---
        $customers = collect([
            ['Juan Dela Cruz', 'juan.delacruz@example.com', '0917 123 4567'],
            ['Ana Reyes', 'ana.reyes@example.com', '0928 555 0192'],
            ['Carlo Mendoza', 'carlo.mendoza@example.com', null],
            ['Liza Navarro', 'liza.navarro@example.com', '0905 777 3321'],
        ])->map(fn ($c) => Customer::create(['full_name' => $c[0], 'email' => $c[1], 'phone' => $c[2]]));

        // --- A few sales, recorded through the real service so stock stays consistent ---
        $service = app(SaleService::class);
        $service->record($products[0]->id, $customers[0]->id, $admin->id, 2);
        $service->record($products[2]->id, null, $cashier->id, 3);
        $service->record($products[5]->id, $customers[1]->id, $cashier->id, 1);
        $service->record($products[1]->id, $customers[3]->id, $admin->id, 4);
    }
}
