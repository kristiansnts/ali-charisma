<?php

namespace App\Support;

use App\Models\Account;
use App\Models\Team;
use Illuminate\Support\Str;
use RuntimeException;
use TomatoPHP\FilamentEcommerce\Models\Order;
use TomatoPHP\FilamentEcommerce\Models\OrdersItem;

class StorefrontOrderPlacer
{
    public function __construct(
        private readonly StoreCurrency $currency,
    ) {}

    /**
     * @param  array<string, mixed>  $checkout
     */
    public function place(array $checkout, Account $account): Order
    {
        $items = ProductCartList::items();

        if ($items === []) {
            throw new RuntimeException('Your cart is empty.');
        }

        $team = Team::query()
            ->where('slug', config('shipstation.team_slug', 'ali-charisma'))
            ->first();

        if ($team === null) {
            throw new RuntimeException('Store is not configured.');
        }

        $subtotal = ProductCartList::subtotal();
        $shipping = round((float) $checkout['shipping_amount'], 2);
        $total = round($subtotal + $shipping, 2);
        $paymentAmount = $this->currency->toPaymentAmount($total);

        $order = Order::query()->create([
            'team_id' => $team->id,
            'account_id' => $account->id,
            'uuid' => (string) Str::uuid(),
            'source' => 'web',
            'name' => trim($checkout['first_name'].' '.$checkout['last_name']),
            'phone' => $checkout['phone'],
            'flat' => $checkout['apartment'] ?? null,
            'address' => $this->formatAddress($checkout),
            'total' => $total,
            'shipping' => $shipping,
            'discount' => 0,
            'vat' => 0,
            'status' => 'pending',
            'is_payed' => false,
            'payment_method' => 'midtrans',
            'payment_vendor' => 'midtrans',
        ]);

        foreach ($items as $item) {
            $lineTotal = round((float) $item['price'] * (int) $item['qty'], 2);

            OrdersItem::query()->create([
                'order_id' => $order->id,
                'account_id' => $account->id,
                'product_id' => $item['product_id'],
                'item' => $item['name'],
                'price' => (float) $item['price'],
                'qty' => (int) $item['qty'],
                'total' => $lineTotal,
                'discount' => 0,
                'vat' => 0,
            ]);
        }

        $order->meta('checkout_email', $checkout['email']);
        $order->meta('shipping_rate_id', $checkout['shipping_rate_id']);
        $order->meta('shipping_service_code', $checkout['shipping_service_code']);
        $order->meta('shipping_country', strtoupper($checkout['country']));
        $order->meta('payment_amount_idr', $paymentAmount);
        $order->meta('payment_currency', $this->currency->paymentCode());

        return $order->fresh(['ordersItems']);
    }

    /**
     * @param  array<string, mixed>  $checkout
     */
    private function formatAddress(array $checkout): string
    {
        $parts = array_filter([
            $checkout['address'],
            $checkout['apartment'] ?? null,
            $checkout['city'],
            $checkout['province'],
            $checkout['postal'],
            strtoupper($checkout['country']),
        ]);

        return implode(', ', $parts);
    }
}
