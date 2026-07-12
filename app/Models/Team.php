<?php

namespace App\Models;

use Filament\Models\Contracts\HasCurrentTenantLabel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model implements HasCurrentTenantLabel
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function getCurrentTenantLabel(): string
    {
        return $this->name;
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(\TomatoPHP\FilamentEcommerce\Models\Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(\TomatoPHP\FilamentEcommerce\Models\Order::class);
    }

    public function companies(): HasMany
    {
        return $this->hasMany(\TomatoPHP\FilamentEcommerce\Models\Company::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    /** @return HasMany<\TomatoPHP\FilamentEcommerce\Models\ShippingVendor, self> */
    public function shippingVendors(): HasMany
    {
        return $this->hasMany(\TomatoPHP\FilamentEcommerce\Models\ShippingVendor::class);
    }


    /** @return HasMany<\Spatie\Permission\Models\Role, self> */
    public function roles(): HasMany
    {
        return $this->hasMany(\Spatie\Permission\Models\Role::class);
    }

}
