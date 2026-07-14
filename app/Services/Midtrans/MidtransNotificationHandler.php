<?php

namespace App\Services\Midtrans;

use App\Support\ProductCartList;
use App\Support\StoreCurrency;
use TomatoPHP\FilamentEcommerce\Models\Order;

class MidtransNotificationHandler
{
    public function __construct(
        private readonly StoreCurrency $currency,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public function handle(array $payload): void
    {
        if (! $this->isValidSignature($payload)) {
            abort(403, 'Invalid signature key.');
        }

        $orderId = (string) ($payload['order_id'] ?? '');
        $order = Order::query()->where('uuid', $orderId)->first();

        if ($order === null) {
            return;
        }

        if ($order->is_payed) {
            return;
        }

        $status = (string) ($payload['transaction_status'] ?? '');

        match ($status) {
            'capture', 'settlement' => $this->markPaid($order, $payload),
            'deny', 'cancel', 'expire' => $order->update(['status' => 'cancelled']),
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function markPaid(Order $order, array $payload): void
    {
        $paidIdr = (int) ($payload['gross_amount'] ?? 0);

        $order->update([
            'is_payed' => true,
            'status' => 'paid',
            'payment_vendor_id' => (string) ($payload['transaction_id'] ?? ''),
        ]);

        if ($paidIdr > 0) {
            $order->meta('paid_amount_idr', $paidIdr);
            $order->meta('paid_amount_usd', $this->currency->fromPaymentAmount($paidIdr));
        }

        ProductCartList::clear();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function isValidSignature(array $payload): bool
    {
        $serverKey = (string) config('midtrans.server_key');

        if ($serverKey === '') {
            return false;
        }

        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');

        if ($orderId === '' || $statusCode === '' || $grossAmount === '' || $signatureKey === '') {
            return false;
        }

        $expected = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        return hash_equals($expected, $signatureKey);
    }
}
