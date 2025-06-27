<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    ProfileController,
    OrderController,
    ProductController,
    CartController,
    CheckoutController,
    PaymentController,
    Admin\OrderAdminController,
    Admin\ProductAdminController
};

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Dashboard (alleen voor ingelogde en geverifieerde gebruikers)
Route::get('/dashboard', fn() => view('dashboard'))->middleware(['auth', 'verified'])->name('dashboard');

// Winkelwagen
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/update/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
});

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

// Bedankpagina
Route::get('/thank-you', fn() => view('checkout.thankyou'))->name('thankyou');

// Betaling
Route::get('/payment', [PaymentController::class, 'checkout'])->name('payment.checkout');
Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');

// Profielbeheer (alleen voor ingelogde gebruikers)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// Admin routes (alleen voor beheerders)

Route::prefix('admin')
    ->middleware(['auth', 'is_admin'])->name('admin.')->group(function () {
        Route::get('/orders', [OrderAdminController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderAdminController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [OrderAdminController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::delete('/orders/{order}', [OrderAdminController::class, 'destroy'])->name('orders.destroy');

        // products
        Route::resource('products', ProductAdminController::class);
    });

Route::get('/check-delivery', [OrderController::class, 'checkDelivery'])->name('check.delivery');


// Authenticatie (login/register routes)
require __DIR__.'/auth.php';
