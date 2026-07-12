<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use TomatoPHP\FilamentEcommerce\Filament\Resources\OrderResource\Pages\ListOrders as BaseListOrders;

class ListOrders extends BaseListOrders
{
    protected static string $resource = OrderResource::class;
}
