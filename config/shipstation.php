<?php

return [

    'api_key' => env('SHIPSTATION_API_KEY'),

    'base_url' => env('SHIPSTATION_BASE_URL', 'https://api.shipstation.com'),

    'team_slug' => env('SHIPSTATION_TEAM_SLUG', 'ali-charisma'),

    'preferred_currency' => env('SHIPSTATION_PREFERRED_CURRENCY', 'USD'),

    /*
    |--------------------------------------------------------------------------
    | Warehouse / ship-from (storefront quotes never accept this from the shopper)
    |--------------------------------------------------------------------------
    */
    'ship_from' => [
        'name' => env('SHIPSTATION_SHIP_FROM_NAME', 'Ali Charisma'),
        'phone' => env('SHIPSTATION_SHIP_FROM_PHONE'),
        'company_name' => env('SHIPSTATION_SHIP_FROM_COMPANY', 'Ali Charisma'),
        'address_line1' => env('SHIPSTATION_SHIP_FROM_ADDRESS', 'Jl. Melati 1'),
        'address_line2' => env('SHIPSTATION_SHIP_FROM_ADDRESS_2'),
        'city_locality' => env('SHIPSTATION_SHIP_FROM_CITY', 'Malang'),
        'state_province' => env('SHIPSTATION_SHIP_FROM_STATE', 'JI'),
        'postal_code' => env('SHIPSTATION_SHIP_FROM_POSTAL', '65141'),
        'country_code' => env('SHIPSTATION_SHIP_FROM_COUNTRY', 'ID'),
        'address_residential_indicator' => 'no',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default package used when products have no weight/dimensions
    |--------------------------------------------------------------------------
    |
    | ponytail: single package = default weight × cart qty; upgrade when products
    | store real weight/dimensions.
    |
    */
    'default_package' => [
        'package_code' => 'package',
        'weight' => [
            'value' => (float) env('SHIPSTATION_DEFAULT_WEIGHT', 0.5),
            'unit' => env('SHIPSTATION_DEFAULT_WEIGHT_UNIT', 'kilogram'),
        ],
        'dimensions' => [
            'unit' => env('SHIPSTATION_DEFAULT_DIM_UNIT', 'centimeter'),
            'length' => (float) env('SHIPSTATION_DEFAULT_LENGTH', 30),
            'width' => (float) env('SHIPSTATION_DEFAULT_WIDTH', 20),
            'height' => (float) env('SHIPSTATION_DEFAULT_HEIGHT', 10),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Fixed carrier catalog (developer-managed)
    |--------------------------------------------------------------------------
    |
    | Single ShipStation rates service for now: DHL Express Worldwide (parcels).
    | Tenant admins can only enable/disable the seeded row.
    |
    */
    'carriers' => [
        [
            'code' => 'dhl_express',
            'name' => 'DHL Express Worldwide',
            'carrier_id' => 'se-6345411',
            'service_codes' => ['dhl_express_mydhl_express_worldwide_nondoc'],
        ],
    ],

];
