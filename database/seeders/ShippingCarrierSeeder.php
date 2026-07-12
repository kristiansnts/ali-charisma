<?php

namespace Database\Seeders;

use App\Services\ShipStation\ShippingCarrierSync;
use Illuminate\Database\Seeder;

class ShippingCarrierSeeder extends Seeder
{
    public function run(): void
    {
        app(ShippingCarrierSync::class)->syncAllTeams();
    }
}
