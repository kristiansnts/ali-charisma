<?php

namespace App\Services\ShipStation;

use App\Models\ShippingVendor;
use App\Models\Team;

class ShippingCarrierSync
{
    /**
     * Ensure config carriers exist for a tenant without resetting is_activated.
     */
    public function syncTeam(Team $team): void
    {
        /** @var list<array{code: string, name: string, carrier_id: string, service_codes: list<string>}> $carriers */
        $carriers = config('shipstation.carriers', []);

        foreach ($carriers as $carrier) {
            $vendor = ShippingVendor::query()->firstOrCreate(
                [
                    'team_id' => $team->id,
                    'code' => $carrier['code'],
                ],
                [
                    'name' => $carrier['name'],
                    'carrier_id' => $carrier['carrier_id'],
                    'service_codes' => $carrier['service_codes'],
                    'price' => 0,
                    'is_activated' => false,
                ],
            );

            $vendor->fill([
                'name' => $carrier['name'],
                'carrier_id' => $carrier['carrier_id'],
                'service_codes' => $carrier['service_codes'],
            ])->save();
        }
    }

    public function syncAllTeams(): void
    {
        Team::query()->each(fn (Team $team) => $this->syncTeam($team));
    }
}
