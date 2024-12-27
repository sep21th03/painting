<?php

namespace App\Services;

use App\Models\Cart;

class CartService extends BaseService
{
    public function setModel()
    {
        return new Cart();
    }
    public function findByUserId($userId)
    {
        $cart = Cart::where('user_id', $userId)
            ->with([
                'productHex.product',
                'productHex.sizes',
                'productHex.galleries',
                'productHex.product.categories.set'
            ])
            ->get();

        return $cart->map(function ($item) {
            $size = $item->productHex->sizes->first();
            $discountedPrice = $size->price * (1 - $item->productHex->product->discount / 100);

            $categories = $item->productHex->product->categories;
            $setName = optional($categories->set)->name;
            $categoryName = optional($categories)->name;

            return [
                'id' => $item->id,
                'product_name' => $item->productHex->product->name,
                'product_id' => $item->productHex->product_id,
                'product_hex_id' => $item->product_hex_id,
                'code' => $item->productHex->hex_code,
                'set' => ($setName ? $setName . ' ' : '') . ($categoryName ?? ''),
                'quantity' => $item->quantity,
                'discount' => $item->productHex->product->discount,
                'size' => $size->size,
                'current_price' => (float) $size->price,
                'discounted_price' => (float) $discountedPrice,
                'images' => $item->productHex->galleries->map(fn($gallery) => $gallery->image_path),
                'total_amount' => $discountedPrice * $item->quantity,
            ];
        });
    }

    public function updateQuantityByUserId($userId, $data)
    {
        $cart = Cart::where('user_id', $userId)
            ->where('product_hex_id', $data['product_hex_id'])
            ->where('size_id', $data['size_id'])
            ->first();

        if (!$cart) {
            return false;
        }

        $productSize = \App\Models\ProductSize::find($data['size_id']);
        if (!$productSize || $data['quantity'] > $productSize->stock) {
            return false;
        }

        $result = $cart->update(['quantity' => $data['quantity']]);
        if ($result == false) {
            return false;
        }

        return $this->findByUserId($userId);
    }
    public function addByUserId($userId, $data)
    {
        $result = false;
        if ($data['quantity'] == 0) {
            $result = $this->deleteByUserId($userId, $data['product_hex_id'], $data['size_id']);
        } else {
            $cart = Cart::where('user_id', $userId)
                ->where('product_hex_id', $data['product_hex_id'])
                ->where('size_id', $data['size_id'])
                ->first();

            $productHex = \App\Models\ProductHex::find($data['product_hex_id']);
            $productSize = \App\Models\ProductSize::find($data['size_id']);

            if (!$productHex || !$productSize) {
                return false;
            }

            $newQuantity = $cart ? $cart->quantity + $data['quantity'] : $data['quantity'];
            if ($newQuantity > $productSize->stock) {
                return false;
            }

            if ($cart) {
                $result = $cart->update(['quantity' => $newQuantity]);
            } else {
                $result = Cart::create([
                    'user_id' => $userId,
                    'product_hex_id' => $data['product_hex_id'],
                    'size_id' => $data['size_id'],
                    'quantity' => $data['quantity']
                ]);
            }
        }
        if ($result == false)
            return false;
        return $this->findByUserId($userId);
    }
    public function deleteByUserId($userId, $productHex, $sizeId)
    {
        return Cart::where('user_id', $userId)->where('product_hex_id', $productHex)->where('size_id', $sizeId)->delete();
    }
}
