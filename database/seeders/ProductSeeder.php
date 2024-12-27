<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Product;
use App\Models\ProductHex;
use App\Models\ProductSize;
use App\Models\ProductGalleries;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 20) as $index) {
            DB::beginTransaction();
            try {
                // Tạo sản phẩm
                $product = Product::create([
                    'name' => $faker->sentence(3),
                    'description' => $faker->paragraph,
                    'set_category_id' => rand(1, 5), // Giả sử có 10 danh mục
                    'info' => $faker->sentence,
                    'discount' => rand(0, 50),
                ]);

                // Tạo ProductHex
                $hex = ProductHex::create([
                    'product_id' => $product->id,
                    'hex_code' => strtoupper(Str::random(6)),
                ]);

                // Tạo ProductSize
                ProductSize::create([
                    'product_hex_id' => $hex->id,
                    'size' => $faker->randomElement(['30 X 40', '20 x 30', '40 X 60', '60 X 80']),
                    'price' => $faker->randomFloat(2, 100000, 10000000),
                    'stock' => rand(10, 100),
                ]);

                // Tạo ProductGalleries
                ProductGalleries::create([
                    'product_hex_id' => $hex->id,
                    'image_path' => 'assets/img/products/template_img.jpg',
                ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                echo 'Error: ' . $e->getMessage();
            }
        }
    }
}