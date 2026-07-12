<?php

namespace App\Filament\Resources\ShippingVendorResource\Pages;

use App\Filament\Resources\ShippingVendorResource;
use App\Services\ShipStation\ShippingCarrierSync;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ManageRecords;

class ManageShippingVendors extends ManageRecords
{
    protected static string $resource = ShippingVendorResource::class;

    public function mount(): void
    {
        parent::mount();

        $tenant = Filament::getTenant();

        if ($tenant !== null) {
            app(ShippingCarrierSync::class)->syncTeam($tenant);
        }
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
