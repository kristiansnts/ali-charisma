<?php

use App\Http\Controllers\CompareController;
use App\Http\Controllers\MidtransNotificationController;
use App\Http\Controllers\PredictiveSearchController;
use App\Http\Controllers\StorefrontAccountController;
use App\Http\Controllers\StorefrontCartController;
use App\Http\Controllers\StorefrontCheckoutController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'malefashion.pages.index')->name('malefashion.home');

Route::name('malefashion.')->group(function (): void {
    Route::view('/about', 'malefashion.pages.about')->name('about');
    Route::view('/work', 'malefashion.pages.work')->name('work');
    Route::view('/contact', 'malefashion.pages.contact')->name('contact');
    Route::view('/blog', 'malefashion.pages.blog')->name('blog');
    Route::view('/shop', 'malefashion.pages.shop')->name('shop');
    Route::view('/shop/product', 'malefashion.pages.shop-details')->name('shop-details');

    Route::middleware('guest:account')->group(function (): void {
        Route::get('/account/login', [StorefrontAccountController::class, 'showLogin'])->name('account.login');
        Route::post('/account/login', [StorefrontAccountController::class, 'login'])->name('account.login.store');
        Route::get('/account/register', [StorefrontAccountController::class, 'showRegister'])->name('account.register');
        Route::post('/account/register', [StorefrontAccountController::class, 'register'])->name('account.register.store');
    });

    Route::middleware('auth:account')->group(function (): void {
        Route::get('/account', [StorefrontAccountController::class, 'index'])->name('account');
        Route::get('/account/addresses', [StorefrontAccountController::class, 'addresses'])->name('account.addresses');
        Route::post('/account/addresses', [StorefrontAccountController::class, 'storeAddress'])->name('account.addresses.store');
        Route::delete('/account/addresses/{id}', [StorefrontAccountController::class, 'destroyAddress'])->name('account.addresses.destroy');
        Route::post('/account/logout', [StorefrontAccountController::class, 'logout'])->name('account.logout');
    });

    Route::get('/cart', [StorefrontCartController::class, 'index'])->name('cart');
    Route::get('/checkout', [StorefrontCartController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/shipping-rates', [StorefrontCartController::class, 'shippingRates'])->name('checkout.shipping-rates');
    Route::post('/checkout/pay', [StorefrontCheckoutController::class, 'pay'])
        ->middleware('auth:account')
        ->name('checkout.pay');
    Route::get('/checkout/finish', [StorefrontCheckoutController::class, 'finish'])->name('checkout.finish');
    Route::get('/checkout/unfinish', [StorefrontCheckoutController::class, 'unfinish'])->name('checkout.unfinish');
    Route::post('/midtrans/notification', [MidtransNotificationController::class, 'store'])->name('midtrans.notification');
    Route::get('/search/predictive', PredictiveSearchController::class)->name('search.predictive');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{key}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::post('/storefront-cart', [StorefrontCartController::class, 'store'])->name('storefront-cart.store');
    Route::get('/storefront-cart/drawer', [StorefrontCartController::class, 'drawer'])->name('storefront-cart.drawer');
    Route::put('/storefront-cart', [StorefrontCartController::class, 'sync'])->name('storefront-cart.sync');
    Route::delete('/storefront-cart/{key}', [StorefrontCartController::class, 'destroy'])->name('storefront-cart.destroy');

    Route::get('/compare', [CompareController::class, 'index'])->name('compare.index');
    Route::post('/compare/{product}', [CompareController::class, 'store'])->name('compare.store');
    Route::delete('/compare/{product}', [CompareController::class, 'destroy'])->name('compare.destroy');
    Route::delete('/compare', [CompareController::class, 'clear'])->name('compare.clear');
});
