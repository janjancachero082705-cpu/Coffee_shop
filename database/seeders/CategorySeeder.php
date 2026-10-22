<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Coffee Beans', 'emoji' => '☕'],
            ['name' => 'Seedlings', 'emoji' => '🌱'],
            ['name' => 'Raw Cherries', 'emoji' => '🍒'],
            ['name' => 'Packaging', 'emoji' => '📦'],
            ['name' => 'Farm Supplies', 'emoji' => '🛠️'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['name' => $cat['name']],
                ['emoji' => $cat['emoji']]
            );
        }
    }
}