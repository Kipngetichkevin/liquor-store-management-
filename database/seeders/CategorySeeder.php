<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Whiskey', 'description' => 'Scotch, Bourbon, Irish whiskey', 'status' => 'active'],
            ['name' => 'Vodka', 'description' => 'Premium vodka brands', 'status' => 'active'],
            ['name' => 'Wine', 'description' => 'Red, white, rosé, sparkling', 'status' => 'active'],
            ['name' => 'Beer', 'description' => 'Local and imported beers', 'status' => 'active'],
            ['name' => 'Rum', 'description' => 'White, dark, spiced rums', 'status' => 'active'],
            ['name' => 'Tequila', 'description' => 'Blanco, Reposado, Añejo', 'status' => 'active'],
            ['name' => 'Gin', 'description' => 'London dry, craft gins', 'status' => 'active'],
            ['name' => 'Brandy', 'description' => 'Cognac, Armagnac', 'status' => 'active'],
            ['name' => 'Liqueurs', 'description' => 'Sweetened spirits', 'status' => 'active'],
            ['name' => 'Non-Alcoholic', 'description' => 'Alcohol-free beverages', 'status' => 'active'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('✅ 10 categories seeded successfully!');
    }
}