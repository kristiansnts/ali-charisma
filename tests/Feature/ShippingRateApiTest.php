<?php

use App\Models\ShippingVendor;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config([
        'shipstation.api_key' => 'test-api-key',
        'shipstation.base_url' => 'https://api.shipstation.com',
    ]);
});

it('returns shipping rates for customer payload using tenant activated carriers', function () {
    $team = Team::query()->create([
        'name' => 'Ali Charisma',
        'slug' => 'ali-charisma',
    ]);

    ShippingVendor::query()->create([
        'team_id' => $team->id,
        'code' => 'dhl_express',
        'name' => 'DHL Express Worldwide',
        'carrier_id' => 'se-6345411',
        'service_codes' => ['dhl_express_mydhl_express_worldwide_nondoc'],
        'is_activated' => true,
        'price' => 0,
    ]);

    Http::fake([
        'api.shipstation.com/v2/rates' => Http::response([
            'rate_response' => [
                'rates' => [
                    ['rate_id' => 'se-rate-1'],
                ],
            ],
        ], 200),
    ]);

    $this->postJson('/api/ali-charisma/shipping/rates', [
        'preferred_currency' => 'usd',
        'ship_to' => [
            'name' => 'The President',
            'phone' => '222-333-4444',
            'company_name' => '',
            'address_line1' => '1600 Pennsylvania Avenue NW',
            'city_locality' => 'Washington',
            'state_province' => 'DC',
            'postal_code' => '20500',
            'country_code' => 'US',
            'address_residential_indicator' => 'no',
        ],
        'ship_from' => [
            'name' => 'ShipStation Team',
            'phone' => '222-333-4444',
            'company_name' => 'ShipStation',
            'address_line1' => '4301 Bull Creek Road',
            'city_locality' => 'Austin',
            'state_province' => 'TX',
            'postal_code' => '78731',
            'country_code' => 'US',
            'address_residential_indicator' => 'no',
        ],
        'packages' => [
            [
                'package_code' => 'package',
                'weight' => ['value' => 6, 'unit' => 'ounce'],
                'dimensions' => [
                    'unit' => 'inch',
                    'length' => 10,
                    'width' => 8,
                    'height' => 4,
                ],
            ],
        ],
        // Customer cannot force carriers — ignored even if sent:
        'carrier_ids' => ['se-hacker'],
        'service_codes' => ['hacked'],
    ])
        ->assertSuccessful()
        ->assertJsonPath('rates.0.rate_id', 'se-rate-1');

    Http::assertSent(function ($request): bool {
        $data = $request->data();

        return ($data['rate_options']['carrier_ids'] ?? null) === ['se-6345411']
            && ($data['rate_options']['service_codes'] ?? null) === ['dhl_express_mydhl_express_worldwide_nondoc']
            && ($data['rate_options']['preferred_currency'] ?? null) === 'USD'
            && ! in_array('se-hacker', $data['rate_options']['carrier_ids'] ?? [], true);
    });
});

it('rejects customer rates when the tenant has no activated carriers', function () {
    Team::query()->create([
        'name' => 'Ali Charisma',
        'slug' => 'ali-charisma',
    ]);

    $this->postJson('/api/ali-charisma/shipping/rates', [
        'ship_to' => [
            'name' => 'A',
            'address_line1' => '1 Main',
            'city_locality' => 'Austin',
            'state_province' => 'TX',
            'postal_code' => '78701',
            'country_code' => 'US',
        ],
        'ship_from' => [
            'name' => 'B',
            'address_line1' => '2 Main',
            'city_locality' => 'Austin',
            'state_province' => 'TX',
            'postal_code' => '78702',
            'country_code' => 'US',
        ],
        'packages' => [
            ['weight' => ['value' => 1, 'unit' => 'ounce']],
        ],
    ])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'No activated shipping carriers for this store.');
});
