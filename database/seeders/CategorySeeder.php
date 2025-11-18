<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Technology'],
            ['name' => 'Science'],
            ['name' => 'Business'],
            ['name' => 'Lifestyle'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
