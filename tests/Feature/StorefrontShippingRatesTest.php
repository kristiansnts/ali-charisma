<?php

use App\Models\ShippingVendor;
use App\Models\Team;
use App\Support\ProductCartList;
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Mockery\MockInterface;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config([
        'shipstation.api_key' => 'test-api-key',
        'shipstation.base_url' => 'https://api.shipstation.com',
        'shipstation.team_slug' => 'ali-charisma',
        'shipstation.preferred_currency' => 'USD',
        'shipstation.ship_from' => [
            'name' => 'Ali Charisma',
            'phone' => '08123456789',
            'company_name' => 'Ali Charisma',
            'address_line1' => 'Jl. Melati 1',
            'city_locality' => 'Malang',
            'state_province' => 'JI',
            'postal_code' => '65141',
            'country_code' => 'ID',
            'address_residential_indicator' => 'no',
        ],
        'shipstation.default_package' => [
            'package_code' => 'package',
            'weight' => ['value' => 0.5, 'unit' => 'kilogram'],
            'dimensions' => [
                'unit' => 'centimeter',
                'length' => 30,
                'width' => 20,
                'height' => 10,
            ],
        ],
    ]);

    Team::query()->create([
        'name' => 'Ali Charisma',
        'slug' => 'ali-charisma',
    ]);
});

function seedDhlCarrier(): void
{
    $team = Team::query()->where('slug', 'ali-charisma')->firstOrFail();

    ShippingVendor::query()->create([
        'team_id' => $team->id,
        'code' => 'dhl_express',
        'name' => 'DHL Express',
        'carrier_id' => 'se-6345411',
        'service_codes' => ['dhl_express_mydhl_express_worldwide_nondoc'],
        'is_activated' => true,
        'price' => 0,
    ]);
}

function seedCart(int $qty = 2): void
{
    ProductCartList::add([
        'key' => 'multi-pocket-chest-bag',
        'name' => 'Multi-pocket Chest Bag',
        'price' => 43.48,
        'price_label' => '$43.48',
        'image' => '/malefashion/img/product/product-3.jpg',
    ]);

    if ($qty > 1) {
        ProductCartList::updateQty('multi-pocket-chest-bag', $qty);
    }
}

it('quotes dhl express rates from checkout address and cart weight', function () {
    seedDhlCarrier();
    seedCart(2);

    Http::fake([
        'api.shipstation.com/v2/rates' => Http::response([
            'rate_response' => [
                'rates' => [
                    [
                        'rate_id' => 'se-rate-dhl',
                        'service_code' => 'dhl_express_mydhl_express_worldwide_nondoc',
                        'service_type' => 'DHL Express Worldwide (nondoc)',
                        'carrier_friendly_name' => 'DHL Express',
                        'delivery_days' => 2,
                        'carrier_delivery_days' => '2 business days',
                        'shipping_amount' => ['amount' => 18.75, 'currency' => 'usd'],
                        'insurance_amount' => ['amount' => 0, 'currency' => 'usd'],
                        'confirmation_amount' => ['amount' => 0, 'currency' => 'usd'],
                        'other_amount' => ['amount' => 0, 'currency' => 'usd'],
                    ],
                ],
            ],
        ], 200),
    ]);

    $this->postJson(route('malefashion.checkout.shipping-rates'), [
        'first_name' => 'Nadia',
        'last_name' => 'Customer',
        'phone' => '0811111111',
        'address' => '12 Marina Boulevard',
        'apartment' => '#18-03',
        'city' => 'Singapore',
        'province' => 'Singapore',
        'postal' => '018982',
        'country' => 'SG',
    ])
        ->assertSuccessful()
        ->assertJsonPath('rates.0.rate_id', 'se-rate-dhl')
        ->assertJsonPath('rates.0.amount', 18.75)
        ->assertJsonPath('rates.0.currency', 'USD')
        ->assertJsonPath('rates.0.service_type', 'DHL Express Worldwide (nondoc)')
        ->assertJsonPath('subtotal', 86.96)
        ->assertJsonPath('shipping', 18.75)
        ->assertJsonPath('total', 105.71);

    Http::assertSent(function ($request): bool {
        $data = $request->data();

        return $request->url() === 'https://api.shipstation.com/v2/rates'
            && ($data['rate_options']['carrier_ids'] ?? null) === ['se-6345411']
            && ($data['rate_options']['service_codes'] ?? null) === ['dhl_express_mydhl_express_worldwide_nondoc']
            && ($data['shipment']['ship_to']['phone'] ?? null) === '0811111111'
            && ($data['shipment']['ship_to']['country_code'] ?? null) === 'SG'
            && ($data['shipment']['ship_to']['postal_code'] ?? null) === '018982'
            && ($data['shipment']['ship_from']['phone'] ?? null) === '08123456789'
            && ($data['shipment']['ship_from']['postal_code'] ?? null) === '65141'
            && ($data['shipment']['customs']['contents'] ?? null) === 'merchandise'
            && ($data['shipment']['packages'][0]['weight']['value'] ?? null) === 1.0
            && ($data['shipment']['packages'][0]['products'][0]['sku'] ?? null) === 'multi-pocket-chest-bag';
    });
});

it('converts gbp shipstation shipping rates to usd for checkout', function () {
    seedDhlCarrier();
    seedCart(2);

    $this->mock(ExchangeRate::class, function (MockInterface $mock): void {
        $mock->shouldReceive('exchangeRate')
            ->once()
            ->with('EUR', ['GBP', 'USD'])
            ->andReturn(['GBP' => 0.8, 'USD' => 1.0]);
    });

    Http::fake([
        'api.shipstation.com/v2/rates' => Http::response([
            'rate_response' => [
                'rates' => [
                    [
                        'rate_id' => 'se-rate-dhl-gbp',
                        'service_code' => 'dhl_express_mydhl_express_worldwide_nondoc',
                        'service_type' => 'DHL Express Worldwide (nondoc)',
                        'carrier_friendly_name' => 'DHL Express',
                        'delivery_days' => 3,
                        'carrier_delivery_days' => '3 business days',
                        'shipping_amount' => ['amount' => 193.68, 'currency' => 'gbp'],
                        'insurance_amount' => ['amount' => 0, 'currency' => 'gbp'],
                        'confirmation_amount' => ['amount' => 0, 'currency' => 'gbp'],
                        'other_amount' => ['amount' => 0, 'currency' => 'gbp'],
                    ],
                ],
            ],
        ], 200),
    ]);

    $this->postJson(route('malefashion.checkout.shipping-rates'), [
        'first_name' => 'Kristian',
        'last_name' => 'Santoso',
        'phone' => '083125180658',
        'address' => '12 Marina Boulevard',
        'apartment' => '#18-03 Tower 3',
        'city' => 'Singapore',
        'province' => 'Singapore',
        'postal' => '018982',
        'country' => 'SG',
    ])
        ->assertSuccessful()
        ->assertJsonPath('rates.0.rate_id', 'se-rate-dhl-gbp')
        ->assertJsonPath('rates.0.amount', 242.10)
        ->assertJsonPath('rates.0.currency', 'USD')
        ->assertJsonPath('shipping', 242.10)
        ->assertJsonPath('total', 329.06);
});

it('does not call exchange rates when shipstation already returns usd', function () {
    seedDhlCarrier();
    seedCart();

    $this->mock(ExchangeRate::class, function (MockInterface $mock): void {
        $mock->shouldNotReceive('exchangeRate');
    });

    Http::fake([
        'api.shipstation.com/v2/rates' => Http::response([
            'rate_response' => [
                'rates' => [
                    [
                        'rate_id' => 'se-rate-usd',
                        'service_code' => 'dhl_express_mydhl_express_worldwide_nondoc',
                        'service_type' => 'DHL Express Worldwide (nondoc)',
                        'carrier_friendly_name' => 'DHL Express',
                        'shipping_amount' => ['amount' => 12.50, 'currency' => 'usd'],
                        'insurance_amount' => ['amount' => 0, 'currency' => 'usd'],
                        'confirmation_amount' => ['amount' => 0, 'currency' => 'usd'],
                        'other_amount' => ['amount' => 0, 'currency' => 'usd'],
                    ],
                ],
            ],
        ], 200),
    ]);

    $this->postJson(route('malefashion.checkout.shipping-rates'), [
        'first_name' => 'Nadia',
        'last_name' => 'Customer',
        'phone' => '0811111111',
        'address' => '12 Marina Boulevard',
        'city' => 'Singapore',
        'province' => 'Singapore',
        'postal' => '018982',
        'country' => 'SG',
    ])
        ->assertSuccessful()
        ->assertJsonPath('rates.0.amount', 12.50)
        ->assertJsonPath('rates.0.currency', 'USD');
});

it('requires a phone number before quoting shipping', function () {
    seedDhlCarrier();
    seedCart();

    $this->postJson(route('malefashion.checkout.shipping-rates'), [
        'first_name' => 'Nadia',
        'last_name' => 'Customer',
        'address' => 'Jl. Sudirman 10',
        'city' => 'Jakarta',
        'province' => 'JK',
        'postal' => '10220',
        'country' => 'ID',
    ])
        ->assertUnprocessable()
        ->assertJsonStructure(['errors' => ['phone']]);
});

it('rejects checkout shipping quotes when the cart is empty', function () {
    seedDhlCarrier();

    $this->postJson(route('malefashion.checkout.shipping-rates'), [
        'first_name' => 'Nadia',
        'last_name' => 'Customer',
        'phone' => '0811111111',
        'address' => 'Jl. Sudirman 10',
        'city' => 'Jakarta',
        'province' => 'JK',
        'postal' => '10220',
        'country' => 'ID',
    ])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Your cart is empty.');
});

it('rejects checkout shipping quotes when dhl is not activated', function () {
    seedCart();

    $this->postJson(route('malefashion.checkout.shipping-rates'), [
        'first_name' => 'Nadia',
        'last_name' => 'Customer',
        'phone' => '0811111111',
        'address' => 'Jl. Sudirman 10',
        'city' => 'Jakarta',
        'province' => 'JK',
        'postal' => '10220',
        'country' => 'ID',
    ])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'No activated shipping carriers for this store.');
});

it('rejects checkout shipping quotes when ship-from phone is missing', function () {
    seedDhlCarrier();
    seedCart();

    config(['shipstation.ship_from.phone' => null]);

    $this->postJson(route('malefashion.checkout.shipping-rates'), [
        'first_name' => 'Nadia',
        'last_name' => 'Customer',
        'phone' => '0811111111',
        'address' => 'Jl. Sudirman 10',
        'city' => 'Jakarta',
        'province' => 'JK',
        'postal' => '10220',
        'country' => 'ID',
    ])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Store shipping is not configured.');
});

it('shows dhl shipping UI hooks on the checkout page', function () {
    seedCart();

    $this->get(route('malefashion.checkout'))
        ->assertSuccessful()
        ->assertSee('data-shipping-methods', false)
        ->assertSee('Enter your delivery address to calculate DHL Express rates.', false)
        ->assertSee('checkout\\/shipping-rates', false);
});
