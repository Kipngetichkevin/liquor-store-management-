<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Whiskey (category_id = 1)
            [
                'name' => 'Johnnie Walker Black Label',
                'description' => '12 year aged blended Scotch whiskey',
                'price' => 45.99,
                'cost_price' => 32.50,
                'category_id' => 1,
                'status' => 'active',
                'alcohol_percentage' => 40.0,
                'volume_ml' => 750,
                'sku' => 'WHIS-001',
                'brand' => 'Johnnie Walker',
                'stock_quantity' => 50,
                'min_stock_level' => 10,
            ],
            [
                'name' => 'Jack Daniels Old No. 7',
                'description' => 'Tennessee whiskey',
                'price' => 32.50,
                'cost_price' => 24.00,
                'category_id' => 1,
                'status' => 'active',
                'alcohol_percentage' => 40.0,
                'volume_ml' => 700,
                'sku' => 'WHIS-002',
                'brand' => 'Jack Daniels',
                'stock_quantity' => 35,
                'min_stock_level' => 10,
            ],
            // Vodka (category_id = 2)
            [
                'name' => 'Absolut Vodka',
                'description' => 'Swedish premium vodka',
                'price' => 24.99,
                'cost_price' => 17.50,
                'category_id' => 2,
                'status' => 'active',
                'alcohol_percentage' => 40.0,
                'volume_ml' => 1000,
                'sku' => 'VOD-001',
                'brand' => 'Absolut',
                'stock_quantity' => 60,
                'min_stock_level' => 15,
            ],
            // Wine (category_id = 3)
            [
                'name' => 'Yellow Tail Shiraz',
                'description' => 'Australian red wine',
                'price' => 12.99,
                'cost_price' => 8.50,
                'category_id' => 3,
                'status' => 'active',
                'alcohol_percentage' => 13.5,
                'volume_ml' => 750,
                'sku' => 'WINE-001',
                'brand' => 'Yellow Tail',
                'stock_quantity' => 80,
                'min_stock_level' => 20,
            ],
            // Beer (category_id = 4)
            [
                'name' => 'Heineken Lager Beer',
                'description' => 'Dutch premium lager, 24-pack',
                'price' => 32.99,
                'cost_price' => 24.00,
                'category_id' => 4,
                'status' => 'active',
                'alcohol_percentage' => 5.0,
                'volume_ml' => 330,
                'sku' => 'BEER-001',
                'brand' => 'Heineken',
                'stock_quantity' => 100,
                'min_stock_level' => 30,
            ],
            // Rum (category_id = 5)
            [
                'name' => 'Bacardi Superior',
                'description' => 'White rum',
                'price' => 18.99,
                'cost_price' => 13.50,
                'category_id' => 5,
                'status' => 'active',
                'alcohol_percentage' => 37.5,
                'volume_ml' => 750,
                'sku' => 'RUM-001',
                'brand' => 'Bacardi',
                'stock_quantity' => 45,
                'min_stock_level' => 15,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('✅ 6 products seeded successfully!');
    }
}