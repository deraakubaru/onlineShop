<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;

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
Auth::routes();

Route::middleware(['auth:second'])->group(function () {

    //authenticated store routes
    Route::post('stores', [StoreController::class, 'store'])->name('store-create');
    Route::put('stores/{id}', [StoreController::class, 'update'])->name('store-update');
    Route::delete('stores/{id}', [StoreController::class, 'destroy'])->name('store-delete');

    //authenticated product routes
    Route::post('products', [ProductController::class, 'store'])->name('product-create');
    Route::put('products/{id}', [ProductController::class, 'update'])->name('product-update');
    Route::put('products/{id}', [ProductController::class, 'destroy'])->name('product-delete');

    //order routes
    Route::get('orders', [OrderController::class, 'index'])->name('orders');
    Route::get('orders/{id}', [OrderController::class, 'show'])->name('order');
    
    //cart routes
    Route::get('carts', [CartController::class, 'index'])->name('carts');
    Route::post('carts', [CartController::class, 'addToCart'])->name('cart-create');
});

//Store routes
Route::get('stores', [StoreController::class, 'index'])->name('stores');
Route::get('stores/{id}', [StoreController::class, 'show'])->name('store');

//Product routes
Route::get('products', [ProductController::class, 'index'])->name('products');
Route::get('products/{id}', [ProductController::class, 'show'])->name('product');

