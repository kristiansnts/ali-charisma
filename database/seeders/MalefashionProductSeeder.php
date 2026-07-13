<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;
use TomatoPHP\FilamentCms\Models\Category;
use TomatoPHP\FilamentEcommerce\Models\Product;

class MalefashionProductSeeder extends Seeder
{
    private const DESCRIPTION = <<<'HTML'
<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit.</p>
<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit.</p>
HTML;

    public function run(): void
    {
        $team = Team::query()->where('slug', 'ali-charisma')->firstOrFail();

        $dress = $this->category($team, 'Dress', 'dress');
        $tshirt = $this->category($team, 'T-shirt', 't-shirt');

        $featured = $this->tag($team, 'Featured Products', 'featured-products');
        $new = $this->tag($team, 'New Products', 'new-products');
        $special = $this->tag($team, 'Special Products', 'special-products');
        $woman = $this->tag($team, 'Woman', 'woman');

        $this->seedProduct($team, [
            'name' => 'Long strappy dress',
            'slug' => 'long-strappy-dress',
            'sku' => '00117cbn',
            'price' => 390,
            'price_max' => 505,
            'category' => $dress,
            'tags' => [$featured, $new],
            'colors' => [
                ['value' => 'Black', 'hex' => '#000000'],
                ['value' => 'White', 'hex' => '#ffffff'],
            ],
            'sizes' => ['L', 'M', 'S', 'XL'],
        ]);

        $this->seedProduct($team, [
            'name' => 'Jersey Graphic Tee Dolce',
            'slug' => 'jersey-graphic-tee-dolce',
            'sku' => '00114c-21',
            'price' => 330,
            'price_max' => 410,
            'category' => $tshirt,
            'tags' => [$new, $special, $woman],
            'colors' => [
                ['value' => 'Gainsboro', 'hex' => '#dcdcdc'],
                ['value' => 'LightPink', 'hex' => '#ffb6c1'],
            ],
            'sizes' => ['L', 'M', 'S', 'XL', 'XS'],
        ]);
    }

    /**
     * @param  array{
     *     name: string,
     *     slug: string,
     *     sku: string,
     *     price: float|int,
     *     price_max: float|int,
     *     category: Category,
     *     tags: list<Category>,
     *     colors: list<array{value: string, hex: string}>,
     *     sizes: list<string>
     * }  $data
     */
    private function seedProduct(Team $team, array $data): void
    {
        $product = Product::query()->updateOrCreate(
            [
                'team_id' => $team->id,
                'slug' => $data['slug'],
            ],
            [
                'category_id' => $data['category']->id,
                'name' => $data['name'],
                'sku' => $data['sku'],
                'type' => 'product',
                'description' => self::DESCRIPTION,
                'price' => $data['price'],
                'is_activated' => true,
                'is_in_stock' => true,
                'is_shipped' => true,
                'has_options' => true,
                'has_unlimited_stock' => true,
            ]
        );

        $product->meta('price_max', $data['price_max']);
        $product->meta('options', [
            [
                'name' => 'color',
                'values' => collect($data['colors'])
                    ->map(fn (array $color): array => [
                        'value' => $color['value'],
                        'has_custom_price' => false,
                        'has_color' => true,
                        'color' => $color['hex'],
                    ])
                    ->all(),
            ],
            [
                'name' => 'size',
                'values' => collect($data['sizes'])
                    ->map(fn (string $size): array => [
                        'value' => $size,
                        'has_custom_price' => false,
                        'has_color' => false,
                    ])
                    ->all(),
            ],
        ]);

        $product->categories()->sync([$data['category']->id]);
        $product->tags()->sync(collect($data['tags'])->pluck('id')->all());
    }

    private function category(Team $team, string $name, string $slug): Category
    {
        return $this->taxonomy($team, $name, $slug, 'category');
    }

    private function tag(Team $team, string $name, string $slug): Category
    {
        return $this->taxonomy($team, $name, $slug, 'tag');
    }

    private function taxonomy(Team $team, string $name, string $slug, string $type): Category
    {
        $category = Category::query()->firstOrNew([
            'slug' => $slug,
            'for' => 'product',
            'type' => $type,
        ]);

        $category->fill([
            'name' => $name,
            'is_active' => true,
            'show_in_menu' => true,
        ]);
        $category->save();
        $category->forceFill(['team_id' => $team->id])->save();

        return $category;
    }
}
