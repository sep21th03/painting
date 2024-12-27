<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Models\SetCategory;
use App\Http\Requests\Admin\Category\DeleteCategoryRequest;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;

class CategoryController extends Controller
{
    protected $categoryService;

    // Inject CategoryService vào controller
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }
    /**
     * Lấy danh sách tất cả các hãng (categories).
     *
     * Phương thức này sử dụng `categoryService` để lấy toàn bộ danh sách các hãng từ cơ sở dữ liệu.
     * Nếu danh sách các hãng tồn tại, phương thức sẽ trả về phản hồi dạng JSON với trạng thái thành công,
     * nếu không sẽ trả về thông báo lỗi.
     *
     * @return \Illuminate\Http\JsonResponse Trả về phản hồi JSON chứa danh sách các hãng hoặc thông báo lỗi.
     */
    public function index()
    {
        $categories = $this->categoryService->getAllCategories();
        return $categories
            ? jsonResponse('success',  'Danh sách hãng', $categories)
            : jsonResponse('error', 'Không tìm thấy danh sách hãng!');
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();

        $category = $this->categoryService->store($data);

        return $category
            ? jsonResponse('success',  'Tạo danh mục thành công', $category)
            : jsonResponse('error', 'Tạo danh mục thất bại');
    }

    public function update(UpdateCategoryRequest $request)
    {
        $data = $request->validated();
        $category = $this->categoryService->update($data['id'], $data);

        return $category
            ? jsonResponse('success',  'Sửa danh mục thành công', $category)
            : jsonResponse('error', 'Sửa danh mục thất bại');
    }
    public function destroy(DeleteCategoryRequest $request)
    {
        $data = $request->validated();
        $category = $this->categoryService->delete($data['id']);
        return $category
            ? jsonResponse('success', 'Xóa loại sản phẩm thành công.')
            : jsonResponse('error', 'Xóa loại sản phẩm thất bại.');
    }

    public function addSetCategory(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:sets,id', 
            'name' => 'required|string|max:255', 
        ]);

        $category = $this->categoryService->addSetCategory($data);

        return $category
            ? jsonResponse('success',  'Tạo danh mục thành công', $category)
            : jsonResponse('error', 'Tạo danh mục thất bại');
    }

    public function updateSetCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'id' => 'required|exists:set_categories,id', 
        ]);

    $setcategory = SetCategory::find($request->id);
    if (!$setcategory) {
        return response()->json(['status' => 'error', 'message' => 'Không tồn tại!'], 404);
    }

    $setcategory->name = $request->name;
    $setcategory->save();

    return response()->json(['status' => 'success', 'message' => 'Cập nhật thành công!']);
    }

    public function deleteSetCategory(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:set_categories,id', 
        ]);
    
        $setcategory = SetCategory::find($request->id);
    
        if ($setcategory) {
            $setcategory->delete();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Xóa loại sản phẩm thành công.',
            ]);
        }
    
        return response()->json([
            'status' => 'error',
            'message' => 'Xóa loại sản phẩm thất bại.',
        ]);
    }
    
}
