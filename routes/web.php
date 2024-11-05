<?php

use App\Http\Controllers\{
    AuthController,
    DashboardController,
    HomeController,
    ProductController
};
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class,'login'])->name('login');
Route::post('/login', [AuthController::class,'loginStore'])->name('login-store');

Route::get('/signup', [AuthController::class,'signup'])->name('signup');
Route::post('/signup', [AuthController::class,'signupStore'])->name('signup-store');

Route::middleware(['auth'])->group(function() {
    Route::post('/logout', [AuthController::class,'logout'])->name('logout');
    Route::get('/', [HomeController::class,'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');

    Route::prefix('products')->group(function() {
        Route::get('/', [ProductController::class,'index'])->name('product.');
        Route::post('/store', [ProductController::class,'store'])->name('product.store');
        Route::get('/{product}', [ProductController::class,'edit'])->name('product.edit');
        Route::put('/{product}', [ProductController::class,'update'])->name('product.update');
        Route::delete('/{product}', [ProductController::class,'destroy'])->name('product.destroy');
    });
});