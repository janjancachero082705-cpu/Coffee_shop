<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ═══════════════════════════════════════════
        // USERS
        // ═══════════════════════════════════════════
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@coffee.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Staff User',
            'email' => 'staff@coffee.test',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        // ═══════════════════════════════════════════
        // CATEGORIES (Powder Coffee Focus)
        // ═══════════════════════════════════════════
        $instant = Category::create([
            'name' => 'Instant Coffee',
            'description' => 'Ready-to-mix powder coffee',
            'emoji' => '&#9749;',
        ]);

        $premium = Category::create([
            'name' => 'Premium Powder',
            'description' => 'Premium quality powder blends',
            'emoji' => '&#11088;',
        ]);

        $flavored = Category::create([
            'name' => 'Flavored Coffee',
            'description' => 'Flavored coffee powder',
            'emoji' => '&#127851;',
        ]);

        $creamer = Category::create([
            'name' => 'Creamer & Mix',
            'description' => 'Coffee creamers and mixes',
            'emoji' => '&#129388;',
        ]);

        $bulk = Category::create([
            'name' => 'Bulk Sacks',
            'description' => 'Bulk powder for distribution',
            'emoji' => '&#128230;',
        ]);

        // ═══════════════════════════════════════════
        // PRODUCTS (Powder Coffee)
        // ═══════════════════════════════════════════
        $products = [
            // INSTANT COFFEE
            [$instant, 'Coffee House Instant 30g', 'Single-serve instant coffee sachet', 15, 8, 500, 50, 'pack', '30g', 30, true],
            [$instant, 'Coffee House Instant 100g', 'Instant coffee powder 100g', 45, 28, 300, 30, 'pack', '100g', 100, true],
            [$instant, 'Coffee House Instant 250g', 'Instant coffee powder 250g jar', 105, 65, 200, 25, 'pack', '250g', 250, false],
            [$instant, 'Coffee House Instant 1kg', 'Instant coffee powder 1kg pack', 380, 240, 100, 15, 'pack', '1kg', 1000, false],

            // PREMIUM POWDER
            [$premium, 'Premium Blend 200g', 'Rich premium coffee blend', 135, 85, 150, 20, 'pack', '200g', 200, true],
            [$premium, 'Premium Blend 500g', 'Rich premium coffee blend - large', 320, 200, 100, 15, 'pack', '500g', 500, false],
            [$premium, 'Barako Blend 250g', 'Strong Filipino blend', 145, 90, 120, 15, 'pack', '250g', 250, true],

            // FLAVORED
            [$flavored, 'Mocha Powder 250g', 'Chocolate mocha flavored', 125, 75, 100, 15, 'pack', '250g', 250, false],
            [$flavored, 'Caramel Coffee 250g', 'Caramel flavored coffee', 125, 75, 100, 15, 'pack', '250g', 250, false],
            [$flavored, 'Vanilla Latte Mix 200g', 'Vanilla latte powder mix', 130, 80, 80, 12, 'pack', '200g', 200, false],

            // CREAMER
            [$creamer, 'Coffee Creamer 200g', 'Non-dairy creamer', 65, 38, 200, 25, 'pack', '200g', 200, false],
            [$creamer, '3-in-1 Coffee Mix', 'Coffee + creamer + sugar', 10, 5, 800, 100, 'pack', '20g', 20, true],

            // BULK SACKS (for distribution)
            [$bulk, 'Instant Coffee Bulk 10kg', 'Bulk powder for distribution', 3200, 2200, 20, 5, 'sako', '10kg', 10000, true],
            [$bulk, 'Premium Blend Bulk 20kg', 'Premium blend bulk sack', 6500, 4500, 10, 3, 'sako', '20kg', 20000, false],
            [$bulk, 'Barako Blend Bulk 15kg', 'Barako blend bulk for stores', 4800, 3300, 15, 3, 'sako', '15kg', 15000, false],
        ];

        foreach ($products as $i => $p) {
            Product::create([
                'sku' => 'CB-' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'category_id' => $p[0]->id,
                'name' => $p[1],
                'description' => $p[2],
                'price' => $p[3],
                'cost_price' => $p[4],
                'stock' => $p[5],
                'reorder_level' => $p[6],
                'unit_type' => $p[7],
                'base_unit' => $p[8],
                'weight_grams' => $p[9],
                'is_featured' => $p[10],
                'is_active' => true,
            ]);
        }

        // ═══════════════════════════════════════════
        // SAMPLE STORES (Consignment Partners)
        // ═══════════════════════════════════════════
        $stores = [
            ['Juan Sari-sari Store', 'Juan Dela Cruz', '09171234567', 'juan@store.test', '123 Main St', 'Poblacion', 'Cebu City', 5000, 'weekly', 'Monday', 'active'],
            ['Maria Mini Mart', 'Maria Santos', '09181234568', 'maria@store.test', '456 Rizal Ave', 'Lahug', 'Cebu City', 10000, 'semi_monthly', '15', 'active'],
            ['Pedro Trading', 'Pedro Reyes', '09191234569', 'pedro@store.test', '789 Colon St', 'San Roque', 'Cebu City', 8000, 'monthly', '30', 'active'],
            ['Ana Grocery', 'Ana Lopez', '09201234570', 'ana@store.test', '321 Osmena Blvd', 'Capitol', 'Cebu City', 6000, 'weekly', 'Friday', 'active'],
            ['Rosa Store', 'Rosa Garcia', '09211234571', null, '654 Mango Ave', 'Kamputhaw', 'Cebu City', 4000, 'monthly', '15', 'suspended'],
        ];

        foreach ($stores as $i => $s) {
            Store::create([
                'code' => 'STORE-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'store_name' => $s[0],
                'owner_name' => $s[1],
                'contact_number' => $s[2],
                'email' => $s[3],
                'address' => $s[4],
                'barangay' => $s[5],
                'city' => $s[6],
                'credit_limit' => $s[7],
                'payment_terms' => $s[8],
                'payment_day' => $s[9],
                'status' => $s[10],
            ]);
        }

        $this->command->info('✓ Seeded: 2 users, 5 categories, 15 products, 5 stores');
    }
}