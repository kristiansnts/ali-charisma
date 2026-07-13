<?php

namespace App\Providers;

use App\Models\Account;
use App\Models\ShippingVendor;
use App\Policies\CompanyPolicy;
use App\Policies\CouponPolicy;
use App\Policies\GiftCardPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use App\Policies\ReferralCodePolicy;
use App\Policies\ShippingVendorPolicy;
use App\Support\ProductCartList;
use App\Support\ProductCompareAttributes;
use App\Support\ProductCompareList;
use App\Support\ProductWishlistList;
use Filament\Events\TenantSet;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use TomatoPHP\FilamentEcommerce\Models\Company;
use TomatoPHP\FilamentEcommerce\Models\Coupon;
use TomatoPHP\FilamentEcommerce\Models\GiftCard;
use TomatoPHP\FilamentEcommerce\Models\Order;
use TomatoPHP\FilamentEcommerce\Models\Product;
use TomatoPHP\FilamentEcommerce\Models\ReferralCode;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        config(['filament-accounts.model' => Account::class]);
    }

    public function boot(): void
    {
        // Vendor models are outside App\Models, so Laravel won't auto-discover these policies.
        Gate::policy(Company::class, CompanyPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(ShippingVendor::class, ShippingVendorPolicy::class);
        Gate::policy(Coupon::class, CouponPolicy::class);
        Gate::policy(GiftCard::class, GiftCardPolicy::class);
        Gate::policy(ReferralCode::class, ReferralCodePolicy::class);

        // Keep Spatie team context in sync whenever Filament sets the tenant.
        // Without this, role permissions resolve empty and Shield hides all resources.
        Event::listen(TenantSet::class, function (TenantSet $event): void {
            setPermissionsTeamId($event->getTenant());
        });

        View::composer(['layouts.malefashion', 'malefashion.*'], function ($view): void {
            $wishlistCount = ProductWishlistList::count();
            $cartCount = ProductCartList::count();
            $cartTotal = ProductCartList::formattedSubtotal();

            if (! Schema::hasTable('products')) {
                $view->with([
                    'compareCount' => 0,
                    'compareProducts' => [],
                    'compareableProducts' => collect(),
                    'wishlistCount' => $wishlistCount,
                    'cartCount' => $cartCount,
                    'cartTotal' => $cartTotal,
                ]);

                return;
            }

            $products = ProductCompareList::products()
                ->map(fn (Product $product): array => ProductCompareAttributes::from($product))
                ->all();

            $compareableProducts = Product::query()
                ->whereIn('slug', ['long-strappy-dress', 'jersey-graphic-tee-dolce'])
                ->where('is_activated', true)
                ->get()
                ->keyBy('slug');

            $view->with([
                'compareCount' => count($products),
                'compareProducts' => $products,
                'compareableProducts' => $compareableProducts,
                'wishlistCount' => $wishlistCount,
                'cartCount' => $cartCount,
                'cartTotal' => $cartTotal,
            ]);
        });
    }
}
