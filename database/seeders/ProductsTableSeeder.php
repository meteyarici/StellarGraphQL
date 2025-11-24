<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductsTableSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['title' => 'T-shirt Red', 'sku' => 'TSHIRT-RED', 'brand' => 'BrandA', 'price' => 100, 'stock' => 50],
            ['title' => 'T-shirt Blue', 'sku' => 'TSHIRT-BLUE', 'brand' => 'BrandA', 'price' => 120, 'stock' => 30],
            ['title' => 'Sneakers White', 'sku' => 'SNK-WHITE', 'brand' => 'BrandB', 'price' => 250, 'stock' => 20],
        ];

        foreach ($products as $p) {
            Product::create([
                'uuid' => (string) Str::uuid(),
                'title' => $p['title'],
                'slug' => Str::slug($p['title']),
                'sku' => $p['sku'],
                'brand' => $p['brand'],
                'price' => $p['price'],
                'stock' => $p['stock'],
                'is_active' => true
            ]);
        }
    }
}
