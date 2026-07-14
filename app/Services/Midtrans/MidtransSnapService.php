<?php

namespace App\Services\Midtrans;

use App\Support\StoreCurrency;
use Midtrans\Config;
use Midtrans\Snap;
use TomatoPHP\FilamentEcommerce\Models\Order;

class MidtransSnapService
{
    public function __construct(
        private readonly StoreCurrency $currency,
    ) {
        Config::$serverKey = (string) config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = (bool) config('midtrans.is_sanitized');
        Config::$is3ds = (bool) config('midtrans.is_3ds');
    }

    /**
     * @param  array<string, mixed>  $checkout
     */
    public function getSnapToken(Order $order, array $checkout): string
    {
        $this->ensureConfigured();

        $grossAmount = $this->currency->toPaymentAmount((float) $order->total);

        return Snap::getSnapToken([
            'transaction_details' => [
                'order_id' => $order->uuid,
                'gross_amount' => $grossAmount,
            ],
            'item_details' => $this->itemDetails($order, $grossAmount),
            'customer_details' => [
                'first_name' => $checkout['first_name'],
                'last_name' => $checkout['last_name'],
                'email' => $checkout['email'],
                'phone' => $checkout['phone'],
                'shipping_address' => [
                    'first_name' => $checkout['first_name'],
                    'last_name' => $checkout['last_name'],
                    'phone' => $checkout['phone'],
                    'address' => $checkout['address'],
                    'city' => $checkout['city'],
                    'postal_code' => $checkout['postal'],
                    'country_code' => $this->midtransCountryCode((string) $checkout['country']),
                ],
            ],
            'callbacks' => [
                'finish' => config('midtrans.urls.finish'),
                'unfinish' => config('midtrans.urls.unfinish'),
                'error' => config('midtrans.urls.error'),
            ],
        ]);
    }

    /**
     * @return list<array{id: string, price: int, quantity: int, name: string}>
     */
    private function itemDetails(Order $order, int $grossAmount): array
    {
        $lines = [];
        $order->loadMissing('ordersItems');

        foreach ($order->ordersItems as $index => $item) {
            $lines[] = [
                'id' => 'item-'.($index + 1),
                'name' => (string) $item->item,
                'usd_total' => (float) $item->total,
            ];
        }

        if ((float) $order->shipping > 0) {
            $lines[] = [
                'id' => 'shipping',
                'name' => 'Shipping',
                'usd_total' => (float) $order->shipping,
            ];
        }

        $totalUsd = max(0.01, (float) $order->total);
        $allocated = 0;
        $details = [];

        foreach ($lines as $index => $line) {
            $isLast = $index === count($lines) - 1;
            $amount = $isLast
                ? $grossAmount - $allocated
                : (int) floor($grossAmount * ($line['usd_total'] / $totalUsd));

            $allocated += $amount;

            $details[] = [
                'id' => $line['id'],
                'price' => $isLast ? $amount : max(1, $amount),
                'quantity' => 1,
                'name' => $line['name'],
            ];
        }

        if ($details === []) {
            $details[] = [
                'id' => 'order',
                'price' => $grossAmount,
                'quantity' => 1,
                'name' => 'Order total',
            ];
        }

        return $details;
    }

    public function ensureConfigured(): void
    {
        if (! filled(config('midtrans.server_key')) || ! filled(config('midtrans.client_key'))) {
            throw new \RuntimeException(
                'Midtrans is not configured. Add MIDTRANS_SERVER_KEY and MIDTRANS_CLIENT_KEY to .env, then restart the app.',
            );
        }
    }

    private function midtransCountryCode(string $country): string
    {
        $country = strtoupper(trim($country));

        if (strlen($country) === 3) {
            return $country;
        }

        /** @var array<string, string> $alpha3 */
        $alpha3 = [
            'ID' => 'IDN',
            'SG' => 'SGP',
            'MY' => 'MYS',
            'US' => 'USA',
            'AU' => 'AUS',
        ];

        if (isset($alpha3[$country])) {
            return $alpha3[$country];
        }

        throw new \RuntimeException('The selected country is not supported for payment.');
    }
}
