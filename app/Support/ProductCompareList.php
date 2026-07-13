<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use TomatoPHP\FilamentEcommerce\Models\Product;

class ProductCompareList
{
    public const SESSION_KEY = 'product_compare_ids';

    public const MAX_ITEMS = 4;

    /**
     * @return list<int>
     */
    public static function ids(): array
    {
        return collect(Session::get(self::SESSION_KEY, []))
            ->map(fn (mixed $id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    public static function count(): int
    {
        return count(self::ids());
    }

    public static function has(int $productId): bool
    {
        return in_array($productId, self::ids(), true);
    }

    public static function add(int $productId): bool
    {
        $ids = self::ids();

        if (in_array($productId, $ids, true)) {
            return true;
        }

        if (count($ids) >= self::MAX_ITEMS) {
            return false;
        }

        $ids[] = $productId;
        Session::put(self::SESSION_KEY, $ids);

        return true;
    }

    public static function remove(int $productId): void
    {
        Session::put(
            self::SESSION_KEY,
            array_values(array_filter(
                self::ids(),
                fn (int $id): bool => $id !== $productId
            ))
        );
    }

    public static function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * @return Collection<int, Product>
     */
    public static function products(): Collection
    {
        $ids = self::ids();

        if ($ids === []) {
            return collect();
        }

        return Product::query()
            ->with(['productMetas', 'tags', 'category', 'categories'])
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn (Product $product): int|false => array_search($product->id, $ids, true))
            ->values();
    }
}
