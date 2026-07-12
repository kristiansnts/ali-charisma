<?php

return [

    'api_key' => env('SHIPSTATION_API_KEY'),

    'base_url' => env('SHIPSTATION_BASE_URL', 'https://api.shipstation.com'),

    /*
    |--------------------------------------------------------------------------
    | Fixed carrier catalog (developer-managed)
    |--------------------------------------------------------------------------
    |
    | Single ShipStation rates service for now: DHL Express domestic.
    | Tenant admins can only enable/disable the seeded row.
    |
    */
    'carriers' => [
        [
            'code' => 'dhl_express',
            'name' => 'DHL Express',
            'carrier_id' => 'se-6345411',
            'service_codes' => ['dhl_express_mydhl_domestic_express'],
        ],
    ],

];
