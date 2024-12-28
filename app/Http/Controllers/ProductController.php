<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Requests\Admin\Product\DeleteProductRequest;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Http\Requests\Admin\ProductHex\StoreProductHexRequest;
use App\Http\Requests\Admin\ProductHex\UpdateProductHexRequest;
use App\Http\Requests\Admin\ProductHex\DeleteProductHexRequest;
use App\Http\Requests\Admin\ProductSize\StoreProductSizeRequest;
use App\Http\Requests\Admin\ProductSize\DeleteProductSizeRequest;
use App\Http\Requests\Api\Product\StoreReviewRequest;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    /**
     * Lấy danh sách tất cả sản phẩm.
     * @return JSON danh sách sản phẩm hoặc lỗi nếu không tìm thấy sản phẩm.
     */
    public function index()
    {
        $products = $this->productService->getAllProducts();
        return $products
            ? jsonResponse('success', 'Danh sách sản phẩm', $this->prepareProductsData($products))
            : jsonResponse('error', 'Không tìm thấy danh sách sản phẩm!');
    }
    /**
     * Chuẩn bị dữ liệu sản phẩm để trả về.
     * @param Collection $products
     * @return Collection dữ liệu sản phẩm đã được chuẩn hóa.
     */
    private function prepareProductsData($products)
    {
        return $products->map(function ($product) {
            // Chỉnh sửa lấy tên danh mục từ set_category
            return [
                'id' => $product->id,
                'name' => $product->name,
                'set_category_id' => $product->set_category_id,
                'set_category_name' =>
                    ($product->categories && $product->categories->set ? $product->categories->set->name : '')
                    . ' ' .
                    ($product->categories ? $product->categories->name : ''),
                'product_hex' => $product->productHex->map(function ($productHex) {
                    return [
                        'id' => $productHex->id,
                        'code' => $productHex->hex_code,
                        'sizes' => $productHex->sizes->map(function ($size) {
                            return [
                                'size' => $size->size,
                                'price' => $size->price,
                                'stock' => $size->stock,
                            ];
                        }),
                        'galleries' => $productHex->galleries->map(function ($gallery) {
                            return [
                                'image_path' => $gallery->image_path,
                            ];
                        }),
                    ];
                }),
            ];
        });
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        $galleryImages = $request->file('gallery');
        $result = $this->productService->store($data, $galleryImages);

        return $result['status'] === 'success'
            ? jsonResponse('success', $result['message'])
            : jsonResponse('error', $result['message']);
    }

    /**
     * Hiển thị thông tin sản phẩm theo ID.
     * @param string $id
     * @return JSON thông tin sản phẩm hoặc lỗi nếu không tìm thấy.
     */
    public function show(string $id)
    {
        $product = $this->productService->show($id);

        if ($product) {
            return jsonResponse('success', 'Thông tin sản phẩm', $product);
        }

        return jsonResponse('error', 'Không tìm thấy sản phẩm!', []);
    }


    public function update(UpdateProductRequest $request)
    {
        $data = $request->validated();
        $result = $this->productService->update($data);

        return $result['status'] === 'success'
            ? jsonResponse('success', $result['message'])
            : jsonResponse('error', $result['message']);
    }


    /**
     * Xóa sản phẩm theo ID.
     * @param string $id
     * @return JSON thông tin sản phẩm hoặc lỗi nếu không tìm thấy.
     */
    public function destroy(DeleteProductRequest $request)
    {
        $data = $request->validated();
        $result = $this->productService->delete($data['id']);

        return $result
            ? jsonResponse('success', 'Xóa sản phẩm thành công!')
            : jsonResponse('error', 'Xóa sản phẩm thất bại!');
    }

    public function addHex(StoreProductHexRequest $request)
    {
        $data = $request->validated();
        $data['imageHex'] = $request->file('imageHex');
        $result = $this->productService->addHex($data);

        return $result['status'] === 'success'
            ? jsonResponse('success', $result['message'])
            : jsonResponse('error', $result['message']);
    }
    public function updateHex(UpdateProductHexRequest $request)
    {
        $data = $request->validated();
        $galleryImages = $request->file('gallery');
        $result = $this->productService->updateHex($data, $galleryImages);

        return $result['status'] === 'success'
            ? jsonResponse('success', $result['message'])
            : jsonResponse('error', $result['message']);
    }

    // Xóa nhiều sản phẩm
    public function deleteProducts(Request $request)
    {
        $idToDelete = $request->input('ids');
        $result = $this->productService->deleteProducts($idToDelete);
        return $result
            ? jsonResponse('success', 'Xóa thành công', $result)
            : jsonResponse('error', 'Xóa thất bại', $result);
    }

    // Xóa mã sản phẩm 
    public function deleteProductHex(DeleteProductHexRequest $request)
    {
        $data = $request->validated();
        $result = $this->productService->deleteHex($data['id']);
        return $result['status'] === 'success'
            ? jsonResponse('success', $result['message'])
            : jsonResponse('error', $result['message']);
    }
    public function addSize(StoreProductSizeRequest $request)
    {
        $data = $request->validated();
        $result = $this->productService->addSize($data);

        return $result['status'] === 'success'
            ? jsonResponse('success', $result['message'])
            : jsonResponse('error', $result['message']);
    }
    // Xóa size sản phẩm 
    public function deleteProductSize(DeleteProductSizeRequest $request)
    {
        $data = $request->validated();
        $result = $this->productService->deleteSize($data['id']);
        return $result['status'] === 'success'
            ? jsonResponse('success', $result['message'])
            : jsonResponse('error', $result['message']);
    }
    // public function getProductsByCategory()
    // {
    //     $categoryName = request()->query('category');

    //     if (!$categoryName) {
    //         return response()->json(['message' => 'Vui lòng cung cấp tên danh mục'], 400);
    //     }

    //     $result = $this->productService->getProductsByCategory($categoryName);
    //     return $result
    //         ? jsonResponse('success', 'Danh sách sản phẩm theo danh mục', $result)
    //         : jsonResponse('error', 'Không tìm thấy sản phẩm nào trong danh mục: ' . $categoryName, []);
    // }

    // Review
    /**
     * Lưu đánh giá sản phẩm.
     *
     * Phương thức này nhận yêu cầu chứa thông tin đánh giá sản phẩm từ người dùng, 
     * kiểm tra và xác thực dữ liệu đầu vào bằng lớp `StoreReviewRequest`, sau đó 
     * chuyển dữ liệu đã xác thực cho `ProductService` để lưu đánh giá.
     *
     * @param StoreReviewRequest $request Yêu cầu chứa dữ liệu đánh giá.
     * @return \Illuminate\Http\JsonResponse Trả về phản hồi dạng JSON thông báo kết quả.
     */
    public function storeProductReview(StoreReviewRequest $request)
    {
        $data = $request->validated();
        $result = $this->productService->storeReview($data);
        return jsonResponse($result ? 'success' : 'error', $result ? 'Đánh giá thành công!' : 'Đánh giá thất bại!', $result);
    }
    /**
     * Lấy danh sách các đánh giá sản phẩm.
     *
     * Phương thức này nhận yêu cầu và chuyển nó đến `ProductService` để lấy danh sách 
     * đánh giá sản phẩm từ cơ sở dữ liệu.
     *
     * @param \Illuminate\Http\Request $request Yêu cầu từ người dùng.
     * @return \Illuminate\Http\JsonResponse Trả về phản hồi dạng JSON chứa danh sách đánh giá hoặc thông báo lỗi.
     */
    public function getReviews(Request $request)
    {
        $result = $this->productService->getReviews($request);
        return jsonResponse($result ? 'success' : 'error', $result ? 'Lấy đánh giá thành công!' : 'Lấy đánh giá thất bại!', $result);
    }
    /**
     * Lấy danh sách đánh giá theo ID sản phẩm.
     *
     * Phương thức này nhận ID sản phẩm và lấy các đánh giá liên quan đến sản phẩm đó
     * thông qua `ProductService`.
     *
     * @param int $id ID của sản phẩm.
     * @return \Illuminate\Http\JsonResponse Trả về phản hồi dạng JSON chứa đánh giá sản phẩm hoặc thông báo lỗi.
     */
    public function getReviewByProduct($id)
    {
        $result = $this->productService->getReviewByProductId($id);
        return jsonResponse($result ? 'success' : 'error', $result ? 'Lấy đánh giá thành công!' : 'Lấy đánh giá thất bại!', $result);
    }
    /**
     * Lấy toàn bộ đánh giá sản phẩm.
     *
     * Phương thức này trả về toàn bộ các đánh giá sản phẩm hiện có thông qua `ProductService`.
     *
     * @return \Illuminate\Http\JsonResponse Trả về phản hồi dạng JSON chứa tất cả đánh giá hoặc thông báo lỗi.
     */
    public function getReviewsAll()
    {
        $result = $this->productService->getReviewsAll();
        return jsonResponse($result ? 'success' : 'error', $result ? 'Lấy đánh giá thành công!' : 'Lấy đánh giá thất bại!', $result);
    }
}
