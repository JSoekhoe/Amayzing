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
    ThankYouController,
    Admin\OrderAdminController,
    Admin\ProductAdminController
};

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Dashboard (alleen voor ingelogde en geverifieerde gebruikers)
Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified', 'is_admin'])
    ->name('dashboard');

// Winkelwagen
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/update/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
});

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/pickup/timeslots', [CheckoutController::class, 'getPickupTimeSlots'])->name('pickup.timeslots');


// Bedankpagina
//Route::get('/thankyou', [ThankYouController::class, 'index'])->name('thankyou');



Route::get('/payment/checkout/{orderId}', [PaymentController::class, 'paymentCheckout'])->name('payment.checkout');
Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');
Route::post('/webhook/mollie', [PaymentController::class, 'webhook'])->name('mollie.webhook');
Route::get('/thankyou', [PaymentController::class, 'thankyou'])->name('thankyou');
Route::get('/payment-failed',[ThankYouController::class, 'thankyou'])->name('payment.failed');

// Profielbeheer (alleen voor ingelogde gebruikers)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Producten
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// Admin routes (alleen voor beheerders)
Route::prefix('admin')
    ->middleware(['auth', 'is_admin'])
    ->name('admin.')
    ->group(function () {
        Route::get('/orders', [OrderAdminController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderAdminController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [OrderAdminController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::delete('/orders/{order}', [OrderAdminController::class, 'destroy'])->name('orders.destroy');

        // Producten admin
        Route::resource('products', ProductAdminController::class);
    });

// Authenticatie (login/register routes)
require __DIR__.'/auth.php';
