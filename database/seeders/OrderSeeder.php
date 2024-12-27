<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;
use App\Models\User;
use App\Models\ProductHex;
use App\Models\ProductSize;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        // Tạo 20 đơn hàng ngẫu nhiên
        foreach (range(1, 20) as $index) {
            $orderDetails = [];
            // Tạo chi tiết đơn hàng (ngẫu nhiên từ 1 đến 3 sản phẩm cho mỗi đơn hàng)
            foreach (range(1, rand(1, 3)) as $detailIndex) {
                $productHex = ProductHex::inRandomOrder()->first(); // Lấy sản phẩm ngẫu nhiên
                $productSize = ProductSize::where('product_hex_id', $productHex->id)->inRandomOrder()->first(); // Lấy size ngẫu nhiên

                // Kiểm tra xem sản phẩm và size có tồn tại không
                if ($productHex && $productSize) {
                    $orderDetails[] = [
                        'product_hex_id' => $productHex->id,
                        'size_id' => $productSize->id,
                        'quantity' => rand(1, 5), // Số lượng ngẫu nhiên
                        'price' => $productSize->price, // Sử dụng giá từ size
                    ];
                }
            }

            $orderId = DB::table('orders')->insertGetId([
                'code' => 'DT-DVL-' . strtoupper(uniqid()), // Mã đơn hàng ngẫu nhiên
                'user_id' => User::all()->random()->id,
                'user_name' => $faker->name,
                'phone' => $faker->phoneNumber,
                'status' => rand(1, 5), // Trạng thái ngẫu nhiên
                'payment_method' => $faker->numberBetween(0, 2),
                'total_price' => array_reduce($orderDetails, function ($carry, $item) {
                    return $carry + ($item['quantity'] * $item['price']);
                }, 0), // Tổng giá
                'note' => $faker->sentence,
                'message' => $faker->sentence,
                'address' => $faker->address,
                'created_at' => Carbon::now()->subDays(rand(0, 30)), // Ngày tạo ngẫu nhiên trong 30 ngày qua
                'updated_at' => Carbon::now(),
                'shipping_fee' => $faker->numberBetween(10000, 20000),
            ]);

            // Insert chi tiết đơn hàng
            foreach ($orderDetails as $detail) {
                DB::table('order_details')->insert([
                    'order_id' => $orderId,
                    'product_hex_id' => $detail['product_hex_id'],
                    'size_id' => $detail['size_id'],
                    'quantity' => $detail['quantity'],
                    'price' => $detail['price'],
                ]);

                // Cập nhật tồn kho
                $productSize = ProductSize::find($detail['size_id']);
                $productSize->stock -= $detail['quantity'];
                $productSize->save();
            }
        }
    }
}