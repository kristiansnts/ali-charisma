<?php

namespace App\Support;

use Illuminate\Support\Facades\Session;

class ProductCartList
{
    public const SESSION_KEY = 'product_cart_items';

    /**
     * @return list<array{key: string, name: string, price: float, price_label: string, image: string, qty: int, product_id: int|null}>
     */
    public static function items(): array
    {
        return collect(Session::get(self::SESSION_KEY, []))
            ->filter(fn (mixed $item): bool => is_array($item) && filled($item['key'] ?? null))
            ->map(fn (array $item): array => [
                'key' => (string) $item['key'],
                'name' => (string) ($item['name'] ?? 'Product'),
                'price' => (float) ($item['price'] ?? 0),
                'price_label' => (string) ($item['price_label'] ?? ('$'.number_format((float) ($item['price'] ?? 0), 2))),
                'image' => (string) ($item['image'] ?? ''),
                'qty' => max(1, (int) ($item['qty'] ?? 1)),
                'product_id' => isset($item['product_id']) ? (int) $item['product_id'] : null,
            ])
            ->values()
            ->all();
    }

    public static function count(): int
    {
        return (int) collect(self::items())->sum(fn (array $item): int => $item['qty']);
    }

    public static function subtotal(): float
    {
        return (float) collect(self::items())->sum(
            fn (array $item): float => $item['price'] * $item['qty']
        );
    }

    public static function formattedSubtotal(): string
    {
        return '$'.number_format(self::subtotal(), 2);
    }

    /**
     * @param  array{key: string, name?: string, price?: float|int|string, price_label?: string, image?: string, product_id?: int|null}  $item
     * @return array{key: string, name: string, price: float, price_label: string, image: string, qty: int, product_id: int|null}
     */
    public static function add(array $item): array
    {
        $key = (string) ($item['key'] ?? '');
        $items = self::items();

        foreach ($items as $index => $existing) {
            if ($existing['key'] === $key) {
                $items[$index]['qty'] = $existing['qty'] + 1;
                Session::put(self::SESSION_KEY, $items);

                return $items[$index];
            }
        }

        $price = (float) preg_replace('/[^0-9.]/', '', (string) ($item['price'] ?? 0));
        $added = [
            'key' => $key !== '' ? $key : uniqid('cart_', true),
            'name' => (string) ($item['name'] ?? 'Product'),
            'price' => $price,
            'price_label' => (string) ($item['price_label'] ?? ('$'.number_format($price, 2))),
            'image' => (string) ($item['image'] ?? ''),
            'qty' => 1,
            'product_id' => isset($item['product_id']) ? (int) $item['product_id'] : null,
        ];

        $items[] = $added;
        Session::put(self::SESSION_KEY, $items);

        return $added;
    }

    public static function updateQty(string $key, int $qty): bool
    {
        if ($qty < 1) {
            self::remove($key);

            return true;
        }

        $items = self::items();
        $updated = false;

        foreach ($items as $index => $item) {
            if ($item['key'] === $key) {
                $items[$index]['qty'] = $qty;
                $updated = true;
                break;
            }
        }

        if (! $updated) {
            return false;
        }

        Session::put(self::SESSION_KEY, $items);

        return true;
    }

    public static function remove(string $key): void
    {
        Session::put(
            self::SESSION_KEY,
            array_values(array_filter(
                self::items(),
                fn (array $item): bool => $item['key'] !== $key
            ))
        );
    }

    /**
     * @param  array<string, int>  $quantities  keyed by cart item key
     */
    public static function syncQuantities(array $quantities): void
    {
        foreach ($quantities as $key => $qty) {
            self::updateQty((string) $key, (int) $qty);
        }
    }

    public static function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }
}
