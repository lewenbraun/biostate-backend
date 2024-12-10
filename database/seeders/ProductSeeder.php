<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::first() ?? Category::create(['name' => 'Food Products']);

        $products = [
            [
                'name' => 'Chicken Breast',
                'description' => 'Tender high-quality dietary meat with exceptional protein content',
                'price' => 250.50,
                'weight' => 0.5,
                'calories' => 165.0,
                'proteins' => 31.0,
                'carbs' => 0.0,
                'fats' => 3.6
            ],
            [
                'name' => 'Fresh Salmon',
                'description' => 'Premium cold salmon rich in omega-3 fatty acids',
                'price' => 890.00,
                'weight' => 0.3,
                'calories' => 208.0,
                'proteins' => 22.0,
                'carbs' => 0.0,
                'fats' => 13.0
            ],
            [
                'name' => 'Buckwheat Groats',
                'description' => 'Premium whole grain buckwheat, naturally gluten-free and nutrient-dense',
                'price' => 120.75,
                'weight' => 0.8,
                'calories' => 343.0,
                'proteins' => 13.0,
                'carbs' => 71.0,
                'fats' => 3.5
            ],
            [
                'name' => 'Greek Yogurt',
                'description' => 'Thick, creamy yogurt with high protein content and probiotic benefits',
                'price' => 180.00,
                'weight' => 0.4,
                'calories' => 146.0,
                'proteins' => 20.0,
                'carbs' => 8.0,
                'fats' => 4.5
            ],
            [
                'name' => 'Spinach Leaves',
                'description' => 'Fresh organic baby spinach, packed with vitamins and minerals',
                'price' => 95.50,
                'weight' => 0.2,
                'calories' => 23.0,
                'proteins' => 2.9,
                'carbs' => 3.6,
                'fats' => 0.4
            ],
            [
                'name' => 'Quinoa',
                'description' => 'Whole grain superfood with complete protein profile and multiple nutrients',
                'price' => 210.75,
                'weight' => 0.5,
                'calories' => 368.0,
                'proteins' => 14.0,
                'carbs' => 64.0,
                'fats' => 6.1
            ],
            [
                'name' => 'Almond Butter',
                'description' => 'Smooth, natural almond butter without added sugars or preservatives',
                'price' => 320.00,
                'weight' => 0.3,
                'calories' => 614.0,
                'proteins' => 21.0,
                'carbs' => 20.0,
                'fats' => 54.0
            ],
            [
                'name' => 'Eggs (Large)',
                'description' => 'Free-range organic chicken eggs, rich in protein and essential nutrients',
                'price' => 150.25,
                'weight' => 0.6,
                'calories' => 143.0,
                'proteins' => 12.6,
                'carbs' => 0.7,
                'fats' => 9.5
            ],
            [
                'name' => 'Avocado',
                'description' => 'Ripe, creamy avocado with healthy monounsaturated fats',
                'price' => 135.50,
                'weight' => 0.2,
                'calories' => 160.0,
                'proteins' => 2.0,
                'carbs' => 8.5,
                'fats' => 14.7
            ],
            [
                'name' => 'Sweet Potato',
                'description' => 'Nutrient-rich orange sweet potato, high in vitamin A and fiber',
                'price' => 80.75,
                'weight' => 0.4,
                'calories' => 86.0,
                'proteins' => 1.6,
                'carbs' => 20.0,
                'fats' => 0.1
            ]
        ];

        foreach ($products as $productData) {
            Product::create(array_merge($productData, ['category_id' => $category->id]));
        }
    }
}
