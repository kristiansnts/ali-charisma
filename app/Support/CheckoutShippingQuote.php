<?php

namespace App\Support;

use App\Models\Team;
use App\Services\ShipStation\RatesResult;
use App\Services\ShipStation\ShipmentRateRequest;
use App\Services\ShipStation\ShipStationRatesService;
use RuntimeException;

class CheckoutShippingQuote
{
    public function __construct(
        private readonly ShipStationRatesService $rates,
        private readonly StoreCurrency $currency,
    ) {}

    /**
     * @param  array{name: string, phone: string, address_line1: string, address_line2?: string|null, city_locality: string, state_province: string, postal_code: string, country_code: string}  $shipTo
     * @return array{rates: list<array{rate_id: string, service_code: string, service_type: string, carrier_friendly_name: string, amount: float, currency: string, delivery_days: int|null, meta: string}>, subtotal: float, shipping: float, total: float}
     */
    public function forCart(array $shipTo): array
    {
        $items = ProductCartList::items();

        if ($items === []) {
            throw new RuntimeException('Your cart is empty.');
        }

        $team = Team::query()
            ->where('slug', config('shipstation.team_slug', 'ali-charisma'))
            ->first();

        if ($team === null) {
            throw new RuntimeException('Store shipping is not configured.');
        }

        $shipFrom = $this->shipFrom();
        $international = strtoupper((string) $shipTo['country_code']) !== strtoupper((string) ($shipFrom['country_code'] ?? ''));

        $result = $this->rates->getRatesForTeam($team, new ShipmentRateRequest(
            shipTo: $shipTo,
            shipFrom: $shipFrom,
            packages: $this->packagesFromCart($items, $international, $shipFrom),
            preferredCurrency: strtoupper((string) config('shipstation.preferred_currency', 'USD')),
            calculateTaxAmount: false,
            isReturn: false,
            customs: $international ? [
                'contents' => 'merchandise',
                'non_delivery' => 'return_to_sender',
            ] : null,
        ));

        $rates = $this->normalizeRates($result);
        $shipping = $rates[0]['amount'] ?? 0.0;
        $subtotal = ProductCartList::subtotal();

        return [
            'rates' => $rates,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $subtotal + $shipping,
        ];
    }

    /**
     * ShipStation rate_id values are single-use; match the shopper's selection by service_code.
     *
     * @param  array{rates: list<array{rate_id: string, service_code: string, service_type: string, carrier_friendly_name: string, amount: float, currency: string, delivery_days: int|null, meta: string}>}  $quote
     * @return array{rate_id: string, service_code: string, service_type: string, carrier_friendly_name: string, amount: float, currency: string, delivery_days: int|null, meta: string}|null
     */
    public function matchSelectedRate(array $quote, string $serviceCode, float $amount): ?array
    {
        $rates = collect($quote['rates']);

        $matched = $rates->first(
            fn (array $rate): bool => $rate['service_code'] === $serviceCode,
        );

        if ($matched === null) {
            return null;
        }

        if (abs((float) $matched['amount'] - $amount) > 0.01) {
            return null;
        }

        return $matched;
    }

    /**
     * @return array{name: string, phone?: string|null, company_name?: string|null, address_line1: string, address_line2?: string|null, city_locality: string, state_province: string, postal_code: string, country_code: string, address_residential_indicator?: string}
     */
    private function shipFrom(): array
    {
        /** @var array{name: string, phone?: string|null, company_name?: string|null, address_line1: string, address_line2?: string|null, city_locality: string, state_province: string, postal_code: string, country_code: string, address_residential_indicator?: string} $shipFrom */
        $shipFrom = config('shipstation.ship_from');

        $phone = trim((string) ($shipFrom['phone'] ?? ''));

        if ($phone === '') {
            throw new RuntimeException('Store shipping is not configured.');
        }

        $shipFrom['phone'] = $phone;

        return array_filter($shipFrom, fn (mixed $value): bool => $value !== null && $value !== '');
    }

    /**
     * @param  list<array{key: string, name: string, price: float, qty: int}>  $items
     * @param  array{country_code?: string}  $shipFrom
     * @return list<array{package_code: string, weight: array{value: float, unit: string}, dimensions: array{unit: string, length: float, width: float, height: float}, products?: list<array<string, mixed>>}>
     */
    private function packagesFromCart(array $items, bool $international, array $shipFrom): array
    {
        $qty = max(1, (int) collect($items)->sum(fn (array $item): int => $item['qty']));
        $defaults = config('shipstation.default_package');

        $weightValue = (float) ($defaults['weight']['value'] ?? 0.5);
        $weightUnit = (string) ($defaults['weight']['unit'] ?? 'kilogram');
        $dimensions = $defaults['dimensions'] ?? [
            'unit' => 'centimeter',
            'length' => 30,
            'width' => 20,
            'height' => 10,
        ];
        $currency = strtolower((string) config('shipstation.preferred_currency', 'USD'));
        $origin = strtoupper((string) ($shipFrom['country_code'] ?? 'ID'));

        $package = [
            'package_code' => (string) ($defaults['package_code'] ?? 'package'),
            'weight' => [
                'value' => round($weightValue * $qty, 3),
                'unit' => $weightUnit,
            ],
            'dimensions' => [
                'unit' => (string) $dimensions['unit'],
                'length' => (float) $dimensions['length'],
                'width' => (float) $dimensions['width'],
                'height' => (float) $dimensions['height'],
            ],
        ];

        if ($international) {
            // ponytail: unit weight = default package weight; upgrade when products store real weight.
            $package['products'] = collect($items)
                ->map(fn (array $item): array => [
                    'description' => mb_substr((string) $item['name'], 0, 100),
                    'quantity' => (int) $item['qty'],
                    'value' => [
                        'amount' => round((float) $item['price'], 2),
                        'currency' => $currency,
                    ],
                    'country_of_origin' => $origin,
                    'weight' => [
                        'value' => round($weightValue, 3),
                        'unit' => $weightUnit,
                    ],
                    'sku' => (string) $item['key'],
                ])
                ->values()
                ->all();
        }

        return [$package];
    }

    /**
     * @return list<array{rate_id: string, service_code: string, service_type: string, carrier_friendly_name: string, amount: float, currency: string, delivery_days: int|null, meta: string}>
     */
    private function normalizeRates(RatesResult $result): array
    {
        $rates = collect($result->rates)
            ->map(function (array $rate): array {
                $amount = (float) data_get($rate, 'shipping_amount.amount', 0)
                    + (float) data_get($rate, 'insurance_amount.amount', 0)
                    + (float) data_get($rate, 'confirmation_amount.amount', 0)
                    + (float) data_get($rate, 'other_amount.amount', 0);

                $sourceCurrency = strtoupper((string) data_get(
                    $rate,
                    'shipping_amount.currency',
                    config('shipstation.preferred_currency', 'USD'),
                ));

                $amount = $this->currency->convert($amount, $sourceCurrency);

                $deliveryDays = isset($rate['delivery_days']) ? (int) $rate['delivery_days'] : null;
                $meta = filled($rate['carrier_delivery_days'] ?? null)
                    ? (string) $rate['carrier_delivery_days']
                    : ($deliveryDays !== null ? $deliveryDays.' business days' : 'Express delivery');

                return [
                    'rate_id' => (string) ($rate['rate_id'] ?? ''),
                    'service_code' => (string) ($rate['service_code'] ?? ''),
                    'service_type' => (string) ($rate['service_type'] ?? 'DHL Express'),
                    'carrier_friendly_name' => (string) ($rate['carrier_friendly_name'] ?? 'DHL Express'),
                    'amount' => $amount,
                    'currency' => $this->currency->code(),
                    'delivery_days' => $deliveryDays,
                    'meta' => $meta,
                ];
            })
            ->filter(fn (array $rate): bool => $rate['rate_id'] !== '')
            ->sortBy('amount')
            ->values()
            ->all();

        return $rates;
    }
}
