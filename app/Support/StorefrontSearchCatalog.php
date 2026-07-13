<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use TomatoPHP\FilamentEcommerce\Models\Product;

class StorefrontSearchCatalog
{
    /**
     * @return array{
     *     query: string,
     *     suggestions: list<array{label: string, url: string}>,
     *     products: list<array{name: string, price: string, image: string, url: string}>,
     *     collections: list<array{name: string, image: string|null, url: string}>,
     *     pages: list<array{name: string, url: string}>
     * }
     */
    public static function search(string $query): array
    {
        $query = trim($query);

        if ($query === '') {
            return [
                'query' => '',
                'suggestions' => [],
                'products' => [],
                'collections' => [],
                'pages' => [],
            ];
        }

        $needle = Str::lower($query);

        return [
            'query' => $query,
            'suggestions' => self::filterSuggestions($needle),
            'products' => self::filterProducts($needle),
            'collections' => self::filterCollections($needle),
            'pages' => self::filterPages($needle),
        ];
    }

    /**
     * Highlight the first case-insensitive match of $needle inside $label.
     */
    public static function highlight(string $label, string $needle): string
    {
        $needle = trim($needle);

        if ($needle === '') {
            return e($label);
        }

        $pos = stripos($label, $needle);

        if ($pos === false) {
            return e($label);
        }

        $match = substr($label, $pos, strlen($needle));
        $before = substr($label, 0, $pos);
        $after = substr($label, $pos + strlen($needle));

        return e($before).'<mark>'.e($match).'</mark><span>'.e($after).'</span>';
    }

    /**
     * @return list<array{label: string, url: string}>
     */
    private static function filterSuggestions(string $needle): array
    {
        $suggestions = [
            'belmont',
            'beige',
            'belle dress',
            'beverly dress',
            'beverly skirt brown',
            'long strappy dress',
            'jersey graphic tee',
            'ankle boots',
            'leather backpack',
            'best seller',
            'new arrival',
            'men\'s',
            'women\'s',
            'accessories',
        ];

        return collect($suggestions)
            ->filter(fn (string $label): bool => str_contains(Str::lower($label), $needle))
            ->take(5)
            ->map(fn (string $label): array => [
                'label' => $label,
                'url' => route('malefashion.shop', ['q' => $label]),
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<array{name: string, price: string, image: string, url: string}>
     */
    private static function filterProducts(string $needle): array
    {
        $products = self::staticProducts();

        if (Schema::hasTable('products')) {
            $dbProducts = Product::query()
                ->where('is_activated', true)
                ->where(function ($query) use ($needle): void {
                    $query->where('name', 'like', '%'.$needle.'%')
                        ->orWhere('slug', 'like', '%'.$needle.'%');
                })
                ->limit(8)
                ->get()
                ->map(fn (Product $product): array => [
                    'name' => (string) $product->name,
                    'price' => '$'.number_format((float) ($product->price ?? 0), 2),
                    'image' => asset('malefashion/img/product/product-1.jpg'),
                    'url' => route('malefashion.shop-details'),
                ])
                ->all();

            $products = array_merge($dbProducts, $products);
        }

        return collect($products)
            ->unique('name')
            ->filter(fn (array $product): bool => str_contains(Str::lower($product['name']), $needle))
            ->take(8)
            ->values()
            ->all();
    }

    /**
     * @return list<array{name: string, image: string|null, url: string}>
     */
    private static function filterCollections(string $needle): array
    {
        $collections = [
            [
                'name' => 'Best Seller',
                'image' => asset('malefashion/img/banner/banner-1.jpg'),
                'url' => route('malefashion.shop', ['q' => 'best seller']),
            ],
            [
                'name' => 'New Arrival',
                'image' => asset('malefashion/img/banner/banner-2.jpg'),
                'url' => route('malefashion.shop', ['q' => 'new arrival']),
            ],
            [
                'name' => "Men's",
                'image' => asset('malefashion/img/mens_coll.jpg'),
                'url' => route('malefashion.shop', ['q' => 'men']),
            ],
            [
                'name' => "Women's",
                'image' => asset('malefashion/img/womens_coll.jpg'),
                'url' => route('malefashion.shop', ['q' => 'women']),
            ],
            [
                'name' => 'Accessories',
                'image' => asset('malefashion/img/banner/banner-3.jpg'),
                'url' => route('malefashion.shop', ['q' => 'accessories']),
            ],
            [
                'name' => 'Outer',
                'image' => null,
                'url' => route('malefashion.shop', ['q' => 'outer']),
            ],
        ];

        return collect($collections)
            ->filter(fn (array $collection): bool => str_contains(Str::lower($collection['name']), $needle))
            ->take(6)
            ->values()
            ->all();
    }

    /**
     * @return list<array{name: string, url: string}>
     */
    private static function filterPages(string $needle): array
    {
        $pages = [
            ['name' => 'About', 'url' => route('malefashion.about')],
            ['name' => 'Work', 'url' => route('malefashion.work')],
            ['name' => 'Contact', 'url' => route('malefashion.contact')],
            ['name' => 'Blog', 'url' => route('malefashion.blog')],
            ['name' => 'Shop', 'url' => route('malefashion.shop')],
            ['name' => 'Wishlist', 'url' => route('malefashion.wishlist')],
            ['name' => 'Cart', 'url' => route('malefashion.cart')],
        ];

        return collect($pages)
            ->filter(fn (array $page): bool => str_contains(Str::lower($page['name']), $needle))
            ->take(10)
            ->values()
            ->all();
    }

    /**
     * @return list<array{name: string, price: string, image: string, url: string}>
     */
    private static function staticProducts(): array
    {
        return [
            [
                'name' => 'Long strappy dress',
                'price' => '$390.00',
                'image' => asset('malefashion/img/womens_coll.jpg'),
                'url' => route('malefashion.shop-details'),
            ],
            [
                'name' => 'Jersey Graphic Tee Dolce',
                'price' => '$330.00',
                'image' => asset('malefashion/img/product/product-7.jpg'),
                'url' => route('malefashion.shop-details'),
            ],
            [
                'name' => 'Multi-pocket Chest Bag',
                'price' => '$43.48',
                'image' => asset('malefashion/img/product/product-3.jpg'),
                'url' => route('malefashion.shop-details'),
            ],
            [
                'name' => 'Diagonal Textured Cap',
                'price' => '$60.90',
                'image' => asset('malefashion/img/product/product-4.jpg'),
                'url' => route('malefashion.shop-details'),
            ],
            [
                'name' => 'Ankle Boots',
                'price' => '$98.49',
                'image' => asset('malefashion/img/product/product-6.jpg'),
                'url' => route('malefashion.shop-details'),
            ],
            [
                'name' => 'Leather Backpack',
                'price' => '$31.37',
                'image' => asset('malefashion/img/product/product-5.jpg'),
                'url' => route('malefashion.shop-details'),
            ],
            [
                'name' => 'Basic Flowing Scarf',
                'price' => '$26.28',
                'image' => asset('malefashion/img/product/product-8.jpg'),
                'url' => route('malefashion.shop-details'),
            ],
            [
                'name' => 'Piqué Biker Jacket',
                'price' => '$67.24',
                'image' => asset('malefashion/img/product/product-2.jpg'),
                'url' => route('malefashion.shop-details'),
            ],
            [
                'name' => 'Contrast Rain Jacket',
                'price' => '$35.00',
                'image' => asset('malefashion/img/product/product-1.jpg'),
                'url' => route('malefashion.shop-details'),
            ],
            [
                'name' => 'Bentleigh Cardigan Light Brown',
                'price' => '$369.00',
                'image' => asset('malefashion/img/product/product-9.jpg'),
                'url' => route('malefashion.shop-details'),
            ],
            [
                'name' => 'Juliet Top White',
                'price' => '$269.00',
                'image' => asset('malefashion/img/product/product-10.jpg'),
                'url' => route('malefashion.shop-details'),
            ],
            [
                'name' => 'Garvella Top Light Blue',
                'price' => '$289.00',
                'image' => asset('malefashion/img/product/product-11.jpg'),
                'url' => route('malefashion.shop-details'),
            ],
            [
                'name' => 'Susie Sweater Dark Brown',
                'price' => '$399.00',
                'image' => asset('malefashion/img/product/product-12.jpg'),
                'url' => route('malefashion.shop-details'),
            ],
            [
                'name' => 'Belle Dress',
                'price' => '$319.00',
                'image' => asset('malefashion/img/product/product-13.jpg'),
                'url' => route('malefashion.shop-details'),
            ],
            [
                'name' => 'Beverly Dress',
                'price' => '$349.00',
                'image' => asset('malefashion/img/product/product-14.jpg'),
                'url' => route('malefashion.shop-details'),
            ],
        ];
    }
}
