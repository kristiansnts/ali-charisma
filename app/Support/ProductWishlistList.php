<?php

namespace App\Support;

use Illuminate\Support\Facades\Session;

class ProductWishlistList
{
    public const SESSION_KEY = 'product_wishlist_items';

    public const MAX_ITEMS = 24;

    /**
     * @return list<array{key: string, name: string, price: string, image: string, product_id: int|null}>
     */
    public static function items(): array
    {
        return collect(Session::get(self::SESSION_KEY, []))
            ->filter(fn (mixed $item): bool => is_array($item) && filled($item['key'] ?? null))
            ->map(fn (array $item): array => [
                'key' => (string) $item['key'],
                'name' => (string) ($item['name'] ?? 'Product'),
                'price' => (string) ($item['price'] ?? '$0.00'),
                'image' => (string) ($item['image'] ?? ''),
                'product_id' => isset($item['product_id']) ? (int) $item['product_id'] : null,
            ])
            ->values()
            ->all();
    }

    public static function count(): int
    {
        return count(self::items());
    }

    public static function has(string $key): bool
    {
        return collect(self::items())->contains(fn (array $item): bool => $item['key'] === $key);
    }

    /**
     * @param  array{key: string, name?: string, price?: string, image?: string, product_id?: int|null}  $item
     */
    public static function add(array $item): bool
    {
        $key = (string) ($item['key'] ?? '');

        if ($key === '') {
            return false;
        }

        $items = self::items();

        if (self::has($key)) {
            return true;
        }

        if (count($items) >= self::MAX_ITEMS) {
            return false;
        }

        $items[] = [
            'key' => $key,
            'name' => (string) ($item['name'] ?? 'Product'),
            'price' => (string) ($item['price'] ?? '$0.00'),
            'image' => (string) ($item['image'] ?? ''),
            'product_id' => isset($item['product_id']) ? (int) $item['product_id'] : null,
        ];

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

    public static function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }
}
