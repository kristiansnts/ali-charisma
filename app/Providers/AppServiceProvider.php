<?php

namespace App\Providers;

use App\Models\Account;
use App\Policies\CompanyPolicy;
use App\Policies\CouponPolicy;
use App\Policies\GiftCardPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use App\Policies\ReferralCodePolicy;
use App\Policies\ShippingVendorPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use TomatoPHP\FilamentEcommerce\Models\Company;
use TomatoPHP\FilamentEcommerce\Models\Coupon;
use TomatoPHP\FilamentEcommerce\Models\GiftCard;
use TomatoPHP\FilamentEcommerce\Models\Order;
use TomatoPHP\FilamentEcommerce\Models\Product;
use TomatoPHP\FilamentEcommerce\Models\ReferralCode;
use TomatoPHP\FilamentEcommerce\Models\ShippingVendor;

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
    }
}
