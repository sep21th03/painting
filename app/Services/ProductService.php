<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductHex;
use App\Models\ProductSize;
use App\Models\ProductGalleries;
use App\Models\ProductReview;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ProductService
{
    private const PRODUCT_IMAGE_PATH = 'assets/img/products';
    private const DEFAULT_IMAGE = 'assets/img/products/template_img.jpg';

    public function getAllProducts()
    {
        return Product::with(['categories', 'productHex', 'productHex.sizes'])->get();
    }

    public function store($data, $galleryImages)
    {
        DB::beginTransaction();
        try {
            $product = Product::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'set_category_id' => $data['setcategory_select'],
                'info' => $data['info'],
                'discount' => $data['discount'],
            ]);

            $hex = $this->createHex($product->id, $data);

            $this->createSize($hex->id, $data);

            $this->handleMultipleImageUpload($hex->id, $galleryImages);

            DB::commit();
            return [
                'status' => 'success',
                'message' => 'Thêm sản phẩm thành công!'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => 'error',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ];
        }
    }

    private function createHex($productId, $data)
    {

        $hex = new ProductHex();
        $hex->product_id = $productId;
        $hex->hex_code = $data['code'];
        $hex->save();
        return $hex;
    }

    private function createSize($productHexId, $data)
    {
        $size = new ProductSize();
        $size->product_hex_id = $productHexId;
        $size->size = $data['sizeProduct'];
        $size->price = $data['price'];
        $size->stock = $data['stock'];
        $size->save();
    }
    private function handleMultipleImageUpload($hexId, $galleryImages)
    {
        if (empty($galleryImages)) {
            $this->saveDefaultImage($hexId);
            return;
        }

        foreach ($galleryImages as $image) {
            if ($this->isValidImage($image)) {
                $this->saveImage($hexId, $image);
            }
        }
    }

    private function isValidImage($image)
    {
        if (!$image instanceof \Illuminate\Http\UploadedFile) {
            return false;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($image->getMimeType(), $allowedTypes)) {
            return false;
        }

        if ($image->getSize() > 5 * 1024 * 1024) {
            return false;
        }

        return true;
    }
    private function saveImage($hexId, $image)
    {
        $filename = $this->generateUniqueFilename($image);
        $publicPath = public_path(self::PRODUCT_IMAGE_PATH);

        if (!file_exists($publicPath)) {
            mkdir($publicPath, 0777, true);
        }

        $image->move($publicPath, $filename);

        return ProductGalleries::create([
            'product_hex_id' => $hexId,
            'image_path' => self::PRODUCT_IMAGE_PATH . '/' . $filename
        ]);
    }
    private function saveDefaultImage($hexId)
    {
        return ProductGalleries::create([
            'product_hex_id' => $hexId,
            'image_path' => self::DEFAULT_IMAGE
        ]);
    }
    private function generateUniqueFilename($image)
    {
        return Str::random(10) . '_' . time() . '.' . $image->getClientOriginalExtension();
    }




    public function show(string $id)
    {
        $product = Product::with([
            'productHex.sizes',
            'productHex.galleries',
            'reviews',
            'productHex.product.categories'
        ])
            ->where('id', $id)
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'message' => 'Thông tin sản phẩm',
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'set_category_id' => $product->set_category_id,
                'set' => $product->categories->set->name . ' ' . $product->categories->name,
                'discount' => $product->discount,
                'info' => $product->info,
                'description' => $product->description,
                'product_hex' => $product->productHex->map(function ($hex) {
                    return [
                        'id' => $hex->id,
                        'hex_code' => $hex->hex_code,
                        'sizes' => $hex->sizes->map(function ($size) {
                            return [
                                'id' => $size->id,
                                'size' => $size->size,
                                'price' => $size->price,
                                'stock' => $size->stock,
                            ];
                        }),
                        'galleries' => $hex->galleries->map(function ($gallery) {
                            return [
                                'image_path' => asset($gallery->image_path),
                            ];
                        }),
                    ];
                }),
                'reviews' => $product->reviews,
                'category_name' => $product->productHex->first()?->product->set_category?->name,
            ]
        ]);
    }


    public function update($data)
    {
        try {
            DB::beginTransaction();

            $product = Product::find($data['id']);
            if (!$product) {
                return [
                    'status' => 'error',
                    'message' => 'Sản phẩm không tồn tại!',
                ];
            }

            $product->name = $data['name'];
            $product->info = $data['info'];
            $product->description = $data['description'];
            $product->set_category_id = $data['setcategory'];
            $product->discount = $data['discount'];
            $product->save();


            DB::commit();
            return [
                'status' => 'success',
                'message' => 'Cập nhật sản phẩm thành công!',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => 'error',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ];
        }
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        if ($product) {
            // ProductReview::where('product_id', $id)->delete();
            $product->delete();
            return true;
        }
        return false;
    }


    public function addHex($data)
    {
        $product = Product::find($data['product_id']);
        if (!$product) {
            return [
                'status' => 'error',
                'message' => 'Sản phẩm không tồn tại!'
            ];
        }

        DB::beginTransaction();

        try {
            $hex = ProductHex::create([
                'product_id' => $product->id,
                'hex_code' => $data['nameHex'],
            ]);
            ProductSize::create([
                'product_hex_id' => $hex->id,
                'size' => $data['sizeHex'],
                'price' => $data['priceHex'],
                'stock' => $data['stockHex'],
            ]);
            if (isset($data['imageHex']) && is_array($data['imageHex'])) {
                foreach ($data['imageHex'] as $image) {
                    if ($image instanceof \Illuminate\Http\UploadedFile) {
                        $randomName = Str::random(10);
                        $imageName = $randomName . '_' . time() . '.' . $image->getClientOriginalExtension();

                        $image->move(getPublicPath('assets/img/products'), $imageName);

                        ProductGalleries::create([
                            'product_hex_id' => $hex->id,
                            'image_path' => 'assets/img/products/' . $imageName,
                        ]);
                    }
                }
            } else {
                ProductGalleries::create([
                    'product_hex_id' => $hex->id,
                    'image_path' => 'assets/img/products/template_img.jpg',
                ]);
            }

            DB::commit();

            return [
                'status' => 'success',
                'message' => 'Mã đã thêm thành công.',
                'data' => ['hex' => $hex]
            ];
        } catch (\Exception $exception) {
            DB::rollBack();
            return [
                'status' => 'error',
                'message' => $exception->getMessage()
            ];
        }
    }
    private function replaceImages($hexId, $newImages)
    {
        try {
            $oldImages = ProductGalleries::where('product_hex_id', $hexId)->get();

            foreach ($oldImages as $oldImage) {
                $oldImagePath = public_path($oldImage->image_path);
                if (file_exists($oldImagePath) && $oldImage->image_path != self::DEFAULT_IMAGE) {
                    unlink($oldImagePath);
                }
            }

            ProductGalleries::where('product_hex_id', $hexId)->delete();

            if (empty($newImages)) {
                $this->saveDefaultImage($hexId);
                return;
            }

            foreach ($newImages as $image) {
                if ($this->isValidImage($image)) {
                    $this->saveImage($hexId, $image);
                }
            }
        } catch (\Exception $e) {
            throw new \Exception('Lỗi khi cập nhật ảnh: ' . $e->getMessage());
        }
    }
    public function updateHex($data, $galleryImages)
    {
        try {
            DB::beginTransaction();

            $hex = ProductHex::find($data['code']);
            if (!$hex) {
                return [
                    'status' => 'error',
                    'message' => 'Sản phẩm không tồn tại!',
                    'data' => []
                ];
            }

            $size = ProductSize::where('product_hex_id', $hex->id)
                ->where('id', $data['size'])
                ->first();

            if (!$size) {
                return [
                    'status' => 'error',
                    'message' => 'Kích thước không tồn tại!',
                    'data' => []
                ];
            }

            $size->stock = $data['stock'];
            $size->price = $data['price'];
            $size->save();

            $this->replaceImages($hex->id, $galleryImages);

            DB::commit();
            return [
                'status' => 'success',
                'message' => 'Cập nhật sản phẩm thành công!',
                'data' => [
                    'product' => $hex,
                    'size' => $size,
                ]
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => 'error',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    public function deleteHex($hexId)
    {
        $hex = ProductHex::find($hexId);
        if (!$hex) {
            return [
                'status' => 'error',
                'message' => 'Phiên bản sản phẩm không tồn tại!'
            ];
        }
        $hex->delete();
        $images = ProductGalleries::where('product_hex_id', $hex->id)->get();

        if ($images->isNotEmpty()) {
            foreach ($images as $image) {
                $imagePath = public_path($image->image_path);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $image->delete();
            }
        }
        return [
            'status' => 'success',
            'message' => 'Mã đã xóa thành công.'
        ];
    }
    public function addSize($data)
    {
        $hex = ProductHex::find($data['addhexID']);
        if (!$hex) {
            return [
                'status' => 'error',
                'message' => 'Sản phẩm không tồn tại!'
            ];
        }

        DB::beginTransaction();

        try {
            $size = ProductSize::create([
                'product_hex_id' => $hex->id,
                'size' => $data['sizeName'],
                'price' => $data['priceSize'],
                'stock' => $data['stockSize'],
            ]);
            DB::commit();

            return [
                'status' => 'success',
                'message' => 'Size đã thêm thành công.',
                'data' => ['size' => $size]
            ];
        } catch (\Exception $exception) {
            DB::rollBack();
            return [
                'status' => 'error',
                'message' => $exception->getMessage()
            ];
        }
    }
    public function deleteSize($sizeId)
    {
        $size = ProductSize::find($sizeId);
        if (!$size) {
            return [
                'status' => 'error',
                'message' => 'Phiên bản sản phẩm không tồn tại!'
            ];
        }
        $size->delete();
        return [
            'status' => 'success',
            'message' => 'Size đã xóa thành công.'
        ];
    }
    public function deleteProducts(array $ids)
    {
        try {
            DB::beginTransaction();

            $products = Product::whereIn('id', $ids)->get();

            foreach ($products as $product) {
                // $product->reviews()->delete();
                $product->delete();
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting products: ' . $e->getMessage());
            return false;
        }

    }

    // public function getProductsByCategory($categoryName)
    // {
    //     return Product::with('specifications', 'variants.rom', 'variants.images')->whereHas('category', function ($query) use ($categoryName) {
    //         $query->where('name', $categoryName);
    //     })->get();
    // }

    public function countTotalStockProducts()
    {
        return ProductHex::with('sizes')->get()->sum(function ($productHex) {
            return $productHex->sizes->sum('stock');
        });
    }

    //Reivew
    public function getReviews($data)
    {
        $limit = $data['length'] ?? 10;
        return ProductReview::with('product:id,title', 'user:id,name,avt_url')->orderBy('rating', 'desc')->limit($limit)->get();
        ;
    }
    function getReviewByProductId($product_id)
    {
        return ProductReview::where('product_id', $product_id)->with('user:id,name')->get();
    }
    public function storeReview($data)
    {
        return ProductReview::create([...$data, 'user_id' => auth()->user()->id]);
    }
    public function getReviewsAll()
    {
        $limit = $data['length'] ?? 10;
        $reviews = ProductReview::with([
            'product:id,name',
            'user:id,name,avt_url'
        ])->limit($limit)->orderBy('created_at', 'desc')->get();

        return $reviews;
    }
    public function removeReview($id)
    {
        return DB::table('reviews')->whereIn('id', $id)->delete();
    }
}
