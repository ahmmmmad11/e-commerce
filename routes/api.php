<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\UpdatePasswordController;
use App\Http\Controllers\Auth\UserDocumentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CouponProductController;
use App\Http\Controllers\CouponStatusController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\VariantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [RegisterController::class, 'register'])->name('register');
Route::post('login', [LoginController::class, 'login'])->name('login');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::put('user/update/password', [UpdatePasswordController::class, 'update'])->name('user.update.password');
    Route::put('coupon/status/{coupon}', [CouponStatusController::class, 'update'])->name('coupon.status.update');

    Route::post('coupon/products', [CouponProductController::class, 'store'])->name('coupon.products.store');

    Route::apiResource('addresses', AddressController::class)->only(['store', 'update']);
    Route::apiResource('profile', ProfileController::class)->only(['show', 'update'])->parameters(['profile' => 'user']);

    Route::apiResources([
        'files' => FileController::class,
        'product/variants' => VariantController::class,
        'user/document' => UserDocumentController::class
    ], [
        'only' => ['store']
    ]);

    Route::apiResources([
        'orders' => OrderController::class,
        'product/ratings' => RatingController::class,
    ], [
        'except' => ['update', 'destroy']
    ]);

   Route::apiResources([
       'categories' => CategoryController::class,
       'coupons' => CouponController::class,
       'products' => ProductController::class,
   ]);
});
