<?php

use App\Models\ShippingVendor;
use App\Models\Team;
use App\Services\ShipStation\ShipmentRateRequest;
use App\Services\ShipStation\ShipStationRatesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config([
        'shipstation.api_key' => 'test-api-key',
        'shipstation.base_url' => 'https://api.shipstation.com',
    ]);
});

it('builds rates payload from customer input and activated tenant carriers only', function () {
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

    ShippingVendor::query()->create([
        'team_id' => $team->id,
        'code' => 'inactive_other',
        'name' => 'Inactive Other',
        'carrier_id' => 'se-other',
        'service_codes' => ['other_service'],
        'is_activated' => false,
        'price' => 0,
    ]);

    Http::fake([
        'api.shipstation.com/v2/rates' => Http::response([
            'rate_response' => [
                'rates' => [
                    [
                        'rate_id' => 'se-rate-1',
                        'shipping_amount' => ['amount' => 12.5, 'currency' => 'usd'],
                    ],
                ],
            ],
        ], 200),
    ]);

    $result = app(ShipStationRatesService::class)->getRatesForTeam($team, new ShipmentRateRequest(
        shipTo: [
            'name' => 'The President',
            'phone' => '222-333-4444',
            'address_line1' => '1600 Pennsylvania Avenue NW',
            'city_locality' => 'Washington',
            'state_province' => 'DC',
            'postal_code' => '20500',
            'country_code' => 'US',
            'address_residential_indicator' => 'no',
        ],
        shipFrom: [
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
        packages: [
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
        preferredCurrency: 'USD',
        calculateTaxAmount: false,
        isReturn: false,
    ));

    Http::assertSent(function ($request): bool {
        $data = $request->data();

        return $request->hasHeader('API-Key', 'test-api-key')
            && $request->url() === 'https://api.shipstation.com/v2/rates'
            && $data === [
                'rate_options' => [
                    'carrier_ids' => ['se-6345411'],
                    'service_codes' => ['dhl_express_mydhl_express_worldwide_nondoc'],
                    'preferred_currency' => 'USD',
                    'calculate_tax_amount' => false,
                    'is_return' => false,
                ],
                'shipment' => [
                    'validate_address' => 'no_validation',
                    'ship_to' => [
                        'name' => 'The President',
                        'phone' => '222-333-4444',
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
                ],
            ];
    });

    expect($result->rates)->toHaveCount(1)
        ->and($result->rates[0]['rate_id'])->toBe('se-rate-1');
});

it('throws when no activated carriers exist for the team', function () {
    $team = Team::query()->create([
        'name' => 'Ali Charisma',
        'slug' => 'ali-charisma',
    ]);

    expect(fn () => app(ShipStationRatesService::class)->getRatesForTeam($team, new ShipmentRateRequest(
        shipTo: [
            'name' => 'A',
            'address_line1' => '1 Main',
            'city_locality' => 'Austin',
            'state_province' => 'TX',
            'postal_code' => '78701',
            'country_code' => 'US',
        ],
        shipFrom: [
            'name' => 'B',
            'address_line1' => '2 Main',
            'city_locality' => 'Austin',
            'state_province' => 'TX',
            'postal_code' => '78702',
            'country_code' => 'US',
        ],
        packages: [
            ['weight' => ['value' => 1, 'unit' => 'ounce']],
        ],
    )))->toThrow(RuntimeException::class, 'No activated shipping carriers');
});

it('throws when shipstation returns a non success response', function () {
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
        'api.shipstation.com/v2/rates' => Http::response(['message' => 'Unauthorized'], 401),
    ]);

    expect(fn () => app(ShipStationRatesService::class)->getRatesForTeam($team, new ShipmentRateRequest(
        shipTo: [
            'name' => 'A',
            'address_line1' => '1 Main',
            'city_locality' => 'Austin',
            'state_province' => 'TX',
            'postal_code' => '78701',
            'country_code' => 'US',
        ],
        shipFrom: [
            'name' => 'B',
            'address_line1' => '2 Main',
            'city_locality' => 'Austin',
            'state_province' => 'TX',
            'postal_code' => '78702',
            'country_code' => 'US',
        ],
        packages: [
            ['weight' => ['value' => 1, 'unit' => 'ounce']],
        ],
    )))->toThrow(RuntimeException::class);
});
