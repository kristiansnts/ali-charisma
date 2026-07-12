<?php

namespace App\Providers;

use App\Models\Account;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        config(['filament-accounts.model' => Account::class]);
    }

    public function boot(): void
    {
        //
    }
}
