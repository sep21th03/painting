<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/admin/login', function() {
    return view('auth.login');
})->name('auth.login')->middleware('guest');
Route::post('/admin/login', [AdminController::class, 'postlogin'])->name('auth.login.post');

// Route::middleware(['auth:sanctum'])->group(function () {
//     Route::get('/', function () {
//         return view('welcome');
//     });
// });

Route::get('/', function () {
    return redirect('/admin');
});
Route::prefix('/admin')->middleware(['auth', 'role:admin'])->group(function () {
    //Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    //Category
    Route::prefix('category')->group(function () {
        Route::get('/', [DashboardController::class, 'manager_category'])->name('category.list');
        Route::get('/get', [CategoryController::class, 'index'])->name('category.index');
        Route::post('/add', [CategoryController::class, 'store'])->name('category.store');
        Route::post('/addsetcategory', [CategoryController::class, 'addSetCategory'])->name('category.addsetcategory');
        Route::post('/updatesetcategory', [CategoryController::class, 'updateSetCategory'])->name('category.updatesetcategory');
        Route::post('/edit', [CategoryController::class, 'update'])->name('category.update');
        Route::post('/delete', [CategoryController::class, 'destroy'])->name('category.destroy'); 
        Route::post('/deletesetcategory', [CategoryController::class, 'deleteSetCategory'])->name('category.setcategory'); 
    });
    //Product
    Route::prefix('product')->group(function () {
        Route::get('/', [DashboardController::class, 'manager_product'])->name('product.list');
        Route::get('/get', [ProductController::class, 'index'])->name('product.index');
        Route::get('/{id}', [DashboardController::class, 'detai_product'])->name('product.detail');
        Route::get('/add/product/', [DashboardController::class, 'add_product'])->name('product.add');
        Route::get('/get/{id}', [ProductController::class, 'show'])->name('product.show');
        Route::post('/add/hex/', [ProductController::class, 'addHex'])->name('product.addHex');
        Route::post('/add/size/', [ProductController::class, 'addSize'])->name('product.addSize');
        Route::post('/edit', [ProductController::class, 'update'])->name('product.update');
        Route::post('/update/hex/', [ProductController::class, 'updateHex'])->name('product.updateHex');
        Route::post('/add', [ProductController::class, 'store'])->name('product.store');
        Route::post('/delete', [ProductController::class, 'destroy'])->name('product.destroy');
        Route::post('/delete/products', [ProductController::class, 'deleteProducts'])->name('product.deleteProducts');
        Route::post('/delete/hex', [ProductController::class, 'deleteProductHex'])->name('product.deleteProductHex');
        Route::post('/delete/size', [ProductController::class, 'deleteProductSize'])->name('product.deleteProductSize');
        Route::get('/get/category={query}', [ProductController::class, 'getProductsByCategory']);
    });
     //Cart
     Route::prefix('cart')->group(function () {
        Route::get('get', [CartController::class, 'getMyCart']);
        Route::post('/add', [CartController::class, 'addMyCart']);
        Route::post('/update', [CartController::class, 'updateMyCart']);
    });
    //Order
    Route::prefix('order')->group(function () {
        Route::get('/detail/{id}', [OrderController::class,'getDetailOrder']);
        Route::get('/', [DashboardController::class, 'manager_order'])->name('order.list');
        Route::get('/{id}', [DashboardController::class, 'detail_order'])->name('order.detail');
        Route::post('update', [OrderController::class, 'updateStatus'])->name('order.update');
        Route::get('user/{id}', [DashboardController::class, 'history_order'])->name('order.history');
    });
    //User
    Route::prefix('user')->group(function () {
        Route::get('/', [DashboardController::class, 'manager_user'])->name('user.list');
        Route::get('/get', [UserController::class, 'index'])->name('user.index');
        Route::post('/edit', [UserController::class, 'update'])->name('user.update');
        Route::post('/delete', [UserController::class, 'destroy'])->name('user.destroy');
    });
    //Review
    Route::prefix('review')->group(function () {
        Route::get('/', [DashboardController::class, 'manager_review'])->name('review.list');
        Route::get('/get', [ProductController::class, 'getReviewsAll'])->name('review.index');
    });
});

Route::prefix('order')->namespace('App\Http\Controllers')->group(function () {
    Route::get('vnpay_payment_complete/{id}', [OrderController::class, 'vnpayPaymentComplete'])->name('order.vnpay_payment_complete');
});

Route::middleware('auth:sanctum')->get('/check-session', [Controller::class, 'checkSession']);

Route::post('upload-image', [UploadController::class, 'uploadImage'])->name('upload.image');


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


include 'auth.php';