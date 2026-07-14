<?php

use App\Models\Account;
use App\Models\ShippingVendor;
use App\Models\Team;
use App\Services\Midtrans\MidtransSnapService;
use App\Support\ProductCartList;
use App\Support\StoreCurrency;
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Mockery\MockInterface;
use TomatoPHP\FilamentEcommerce\Models\Order;
use TomatoPHP\FilamentEcommerce\Models\OrdersItem;

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
        'midtrans.server_key' => 'SB-Mid-server-test-key',
        'midtrans.client_key' => 'SB-Mid-client-test-key',
        'midtrans.is_production' => false,
        'midtrans.payment_currency' => 'IDR',
    ]);

    Team::query()->create([
        'name' => 'Ali Charisma',
        'slug' => 'ali-charisma',
    ]);
});

function seedCheckoutCarrier(): void
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

function seedCheckoutCart(): void
{
    ProductCartList::add([
        'key' => 'multi-pocket-chest-bag',
        'name' => 'Multi-pocket Chest Bag',
        'price' => 43.48,
        'price_label' => '$43.48',
        'image' => '/malefashion/img/product/product-3.jpg',
    ]);
}

function fakeCheckoutRates(): void
{
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
}

function checkoutPayPayload(): array
{
    return [
        'email' => 'nadia@example.com',
        'first_name' => 'Nadia',
        'last_name' => 'Customer',
        'phone' => '0811111111',
        'address' => '12 Marina Boulevard',
        'apartment' => '#18-03',
        'city' => 'Singapore',
        'province' => 'Singapore',
        'postal' => '018982',
        'country' => 'SG',
        'shipping_service_code' => 'dhl_express_mydhl_express_worldwide_nondoc',
        'shipping_rate_id' => 'se-rate-dhl-stale',
        'shipping_amount' => 18.75,
    ];
}

function mockIdrExchangeRate(): void
{
    test()->mock(ExchangeRate::class, function (MockInterface $mock): void {
        $mock->shouldReceive('exchangeRate')
            ->andReturnUsing(function (string $base, array|string $target): float|array {
                if ($base === 'EUR' && is_array($target)) {
                    return ['USD' => 1.0, 'IDR' => 17000.0];
                }

                return 1.0;
            });
    });
}

function midtransSignature(string $orderId, string $statusCode, int|string $grossAmount, string $serverKey): string
{
    return hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);
}

it('requires login before starting checkout payment', function () {
    seedCheckoutCarrier();
    seedCheckoutCart();
    fakeCheckoutRates();

    $this->postJson(route('malefashion.checkout.pay'), checkoutPayPayload())
        ->assertUnauthorized();
});

it('creates a pending order and returns a snap token for authenticated customers', function () {
    seedCheckoutCarrier();
    seedCheckoutCart();
    fakeCheckoutRates();
    mockIdrExchangeRate();

    $team = Team::query()->where('slug', 'ali-charisma')->firstOrFail();
    $account = Account::factory()->create([
        'team_id' => $team->id,
        'email' => 'nadia@example.com',
    ]);

    $this->mock(MidtransSnapService::class, function (MockInterface $mock): void {
        $mock->shouldReceive('ensureConfigured')->once();
        $mock->shouldReceive('getSnapToken')
            ->once()
            ->andReturn('sandbox-snap-token');
    });

    $this->actingAs($account, 'account')
        ->postJson(route('malefashion.checkout.pay'), checkoutPayPayload())
        ->assertSuccessful()
        ->assertJsonPath('snap_token', 'sandbox-snap-token');

    $order = Order::query()->first();

    expect($order)->not->toBeNull()
        ->and($order->account_id)->toBe($account->id)
        ->and($order->team_id)->toBe($team->id)
        ->and($order->status)->toBe('pending')
        ->and($order->is_payed)->toBeFalse()
        ->and($order->payment_method)->toBe('midtrans')
        ->and((float) $order->total)->toBe(62.23)
        ->and((float) $order->shipping)->toBe(18.75);

    expect(OrdersItem::query()->count())->toBe(1);

    $currency = app(StoreCurrency::class);

    expect($order->meta('payment_amount_idr'))->toBe($currency->toPaymentAmount(62.23))
        ->and($order->meta('payment_currency'))->toBe('IDR');
});

it('accepts checkout pay when shipstation returns a fresh rate id for the same service', function () {
    seedCheckoutCarrier();
    seedCheckoutCart();
    mockIdrExchangeRate();

    Http::fake([
        'api.shipstation.com/v2/rates' => Http::response([
            'rate_response' => [
                'rates' => [[
                    'rate_id' => 'se-rate-dhl-fresh',
                    'service_code' => 'dhl_express_mydhl_express_worldwide_nondoc',
                    'service_type' => 'DHL Express Worldwide (nondoc)',
                    'carrier_friendly_name' => 'DHL Express',
                    'shipping_amount' => ['amount' => 18.75, 'currency' => 'usd'],
                    'insurance_amount' => ['amount' => 0, 'currency' => 'usd'],
                    'confirmation_amount' => ['amount' => 0, 'currency' => 'usd'],
                    'other_amount' => ['amount' => 0, 'currency' => 'usd'],
                ]],
            ],
        ], 200),
    ]);

    $team = Team::query()->where('slug', 'ali-charisma')->firstOrFail();
    $account = Account::factory()->create(['team_id' => $team->id]);

    $this->mock(MidtransSnapService::class, function (MockInterface $mock): void {
        $mock->shouldReceive('ensureConfigured')->once();
        $mock->shouldReceive('getSnapToken')->once()->andReturn('sandbox-snap-token');
    });

    $this->actingAs($account, 'account')
        ->postJson(route('malefashion.checkout.pay'), checkoutPayPayload())
        ->assertSuccessful();

    expect(Order::query()->first()?->meta('shipping_rate_id'))->toBe('se-rate-dhl-fresh');
});

it('rejects tampered shipping amounts at checkout pay', function () {
    seedCheckoutCarrier();
    seedCheckoutCart();
    fakeCheckoutRates();

    $team = Team::query()->where('slug', 'ali-charisma')->firstOrFail();
    $account = Account::factory()->create(['team_id' => $team->id]);

    $payload = checkoutPayPayload();
    $payload['shipping_amount'] = 1.00;

    $this->actingAs($account, 'account')
        ->postJson(route('malefashion.checkout.pay'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['shipping_service_code']);

    expect(Order::query()->count())->toBe(0);
});

it('marks orders paid from midtrans notifications and clears the cart', function () {
    seedCheckoutCart();
    mockIdrExchangeRate();

    $team = Team::query()->where('slug', 'ali-charisma')->firstOrFail();
    $account = Account::factory()->create(['team_id' => $team->id]);
    $currency = app(StoreCurrency::class);
    $grossAmount = $currency->toPaymentAmount(62.23);

    $order = Order::query()->create([
        'team_id' => $team->id,
        'account_id' => $account->id,
        'uuid' => 'order-uuid-123',
        'source' => 'web',
        'name' => 'Nadia Customer',
        'phone' => '0811111111',
        'address' => '12 Marina Boulevard',
        'total' => 62.23,
        'shipping' => 18.75,
        'discount' => 0,
        'vat' => 0,
        'status' => 'pending',
        'is_payed' => false,
        'payment_method' => 'midtrans',
        'payment_vendor' => 'midtrans',
    ]);

    $payload = [
        'order_id' => $order->uuid,
        'status_code' => '200',
        'gross_amount' => $grossAmount,
        'transaction_status' => 'settlement',
        'transaction_id' => 'midtrans-tx-001',
        'signature_key' => midtransSignature($order->uuid, '200', $grossAmount, 'SB-Mid-server-test-key'),
    ];

    $this->postJson(route('malefashion.midtrans.notification'), $payload)
        ->assertSuccessful()
        ->assertSee('OK');

    $order->refresh();

    expect($order->is_payed)->toBeTrue()
        ->and($order->status)->toBe('paid')
        ->and($order->payment_vendor_id)->toBe('midtrans-tx-001')
        ->and(ProductCartList::items())->toBe([]);
});

it('rejects midtrans notifications with invalid signatures', function () {
    $team = Team::query()->where('slug', 'ali-charisma')->firstOrFail();
    $account = Account::factory()->create(['team_id' => $team->id]);

    $order = Order::query()->create([
        'team_id' => $team->id,
        'account_id' => $account->id,
        'uuid' => 'order-uuid-456',
        'source' => 'web',
        'total' => 62.23,
        'shipping' => 18.75,
        'status' => 'pending',
        'is_payed' => false,
        'payment_method' => 'midtrans',
        'payment_vendor' => 'midtrans',
    ]);

    $this->postJson(route('malefashion.midtrans.notification'), [
        'order_id' => $order->uuid,
        'status_code' => '200',
        'gross_amount' => 1000000,
        'transaction_status' => 'settlement',
        'transaction_id' => 'midtrans-tx-002',
        'signature_key' => 'invalid-signature',
    ])->assertForbidden();

    expect($order->fresh()->is_payed)->toBeFalse();
});

it('returns a clear error when midtrans keys are missing', function () {
    seedCheckoutCarrier();
    seedCheckoutCart();
    fakeCheckoutRates();
    mockIdrExchangeRate();

    config([
        'midtrans.server_key' => null,
        'midtrans.client_key' => null,
    ]);

    $team = Team::query()->where('slug', 'ali-charisma')->firstOrFail();
    $account = Account::factory()->create(['team_id' => $team->id]);

    $this->actingAs($account, 'account')
        ->postJson(route('malefashion.checkout.pay'), checkoutPayPayload())
        ->assertStatus(502)
        ->assertJsonPath('message', 'Midtrans is not configured. Add MIDTRANS_SERVER_KEY and MIDTRANS_CLIENT_KEY to .env, then restart the app.');
});

it('shows midtrans payment copy on checkout instead of card fields', function () {
    seedCheckoutCart();

    $this->get(route('malefashion.checkout'))
        ->assertSuccessful()
        ->assertSee('Midtrans', false)
        ->assertSee('data-checkout-form', false)
        ->assertSee('checkout\\/pay', false)
        ->assertDontSee('name="card_number"', false);
});
