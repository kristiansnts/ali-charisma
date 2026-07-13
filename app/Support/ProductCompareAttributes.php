<?php

namespace App\Support;

use TomatoPHP\FilamentEcommerce\Models\Product;

class ProductCompareAttributes
{
    /**
     * @return array{
     *     id: int,
     *     slug: string,
     *     name: string,
     *     image: string,
     *     price_min: float,
     *     price_max: float,
     *     price_label: string,
     *     description: string,
     *     meta_tags: list<string>,
     *     category: string|null,
     *     availability: string,
     *     sku: string,
     *     colors: list<string>,
     *     sizes: list<string>
     * }
     */
    public static function from(Product $product): array
    {
        $product->loadMissing(['productMetas', 'tags', 'category', 'categories']);

        $metas = $product->productMetas->keyBy('key');
        $priceMin = (float) $product->price;
        $priceMax = (float) ($metas->get('price_max')?->value ?? $priceMin);
        $options = $metas->get('options')?->value ?? [];

        if (! is_array($options)) {
            $options = [];
        }

        $categoryName = $product->category?->name
            ?? $product->categories->first()?->name;

        return [
            'id' => (int) $product->id,
            'slug' => (string) $product->slug,
            'name' => (string) $product->name,
            'image' => self::imageFor($product),
            'price_min' => $priceMin,
            'price_max' => $priceMax,
            'price_label' => self::formatPriceRange($priceMin, $priceMax),
            'description' => strip_tags((string) $product->description),
            'meta_tags' => $product->tags
                ->map(fn ($tag): string => (string) $tag->name)
                ->values()
                ->all(),
            'category' => $categoryName !== null ? (string) $categoryName : null,
            'availability' => $product->is_in_stock ? 'In Stock' : 'Out of Stock',
            'sku' => (string) $product->sku,
            'colors' => self::optionValues($options, 'color'),
            'sizes' => self::optionValues($options, 'size'),
        ];
    }

    private static function imageFor(Product $product): string
    {
        return match ((string) $product->slug) {
            'long-strappy-dress' => asset('malefashion/img/womens_coll.jpg'),
            'jersey-graphic-tee-dolce' => asset('malefashion/img/product/product-7.jpg'),
            default => asset('malefashion/img/product/product-1.jpg'),
        };
    }

    /**
     * @param  list<array{name?: string, values?: list<array{value?: string}>}>  $options
     * @return list<string>
     */
    private static function optionValues(array $options, string $name): array
    {
        foreach ($options as $option) {
            if (strcasecmp((string) ($option['name'] ?? ''), $name) !== 0) {
                continue;
            }

            return collect($option['values'] ?? [])
                ->pluck('value')
                ->filter()
                ->map(fn (mixed $value): string => (string) $value)
                ->values()
                ->all();
        }

        return [];
    }

    private static function formatPriceRange(float $min, float $max): string
    {
        $formattedMin = '$'.number_format($min, 2);
        $formattedMax = '$'.number_format($max, 2);

        if ($min === $max) {
            return $formattedMin;
        }

        return "{$formattedMin} – {$formattedMax}";
    }
}
