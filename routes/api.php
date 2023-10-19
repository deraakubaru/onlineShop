<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\FlashSaleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Authentication routes
// Auth::routes();
Route::post('/getToken', [CustomLoginController::class, 'login']);

Route::middleware(['auth:api'])->group(function () {

    //authenticated store routes
    Route::post('stores', [StoreController::class, 'store'])->name('store-create');
    Route::put('stores/{id}', [StoreController::class, 'update'])->name('store-update');
    Route::delete('stores/{id}', [StoreController::class, 'destroy'])->name('store-delete');

    //authenticated product routes
    Route::post('products', [ProductController::class, 'store'])->name('product-create');
    Route::put('products/{id}', [ProductController::class, 'update'])->name('product-update');
    Route::delete('products/{id}', [ProductController::class, 'destroy'])->name('product-delete');

    //flash sales routes
    Route::post('flash-sales', [FlashSaleController::class, 'store'])->name('flash-sale-create');
    Route::post('products/{product}/associate-with-flash-sale/{flashSale}', [ProductController::class, 'associateWithFlashSale']);
    Route::post('products/{product}/associate-with-flash-sale', [ProductController::class, 'disassociateWithFlashSale']);

    //order routes
    Route::get('orders', [OrderController::class, 'index'])->name('orders');
    Route::get('orders/{id}', [OrderController::class, 'show'])->name('order');
    
    //order detail routes
    Route::get('order-details', [OrderDetailController::class, 'index'])->name('orders-details');
    Route::get('orders-details/{id}', [OrderDetailController::class, 'show'])->name('order-detail');

    
    //cart routes
    Route::get('carts', [CartController::class, 'index'])->name('carts');
    Route::post('carts', [CartController::class, 'addToCart'])->name('cart-create');
    Route::put('carts/{id}', [CartController::class, 'updateCartItem'])->name('cart-update');
    Route::delete('carts/{id}', [CartController::class, 'removeCartItem'])->name('cart-delete');
    Route::post('checkout', [CartController::class, 'checkout'])->name('check-out');

    //balance routes
    Route::get('balance', [BalanceController::class, 'show'])->name('balance');
    Route::get('store-balance/{id}', [BalanceController::class, 'showStoreBalance'])->name('store-balance');
    Route::post('balance', [BalanceController::class, 'topUp'])->name('top-up');
    Route::post('store-balance/{id}', [BalanceController::class, 'topUpToko'])->name('top-up-store');

    //category routes
    Route::get('categories', [CategoryController::class, 'index'])->name('categories');
    Route::post('categories', [CategoryController::class, 'store'])->name('category-create');
    Route::put('categories/{id}', [CategoryController::class, 'update'])->name('category-update');
    Route::delete('categories/{id}', [CategoryController::class, 'destroy'])->name('category-delete');

});

//Store routes
Route::get('stores', [StoreController::class, 'index'])->name('stores');
Route::get('stores/{id}', [StoreController::class, 'show'])->name('store');

//Product routes
Route::get('products', [ProductController::class, 'index'])->name('products');
Route::get('products/{id}', [ProductController::class, 'show'])->name('product');

