<?php

use App\Models\Team;
use App\Support\ProductCompareAttributes;
use Database\Seeders\MalefashionProductSeeder;
use Database\Seeders\TenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use TomatoPHP\FilamentEcommerce\Models\Product;

uses(RefreshDatabase::class);

it('seeds malefashion compare products with readable attributes', function () {
    $this->seed(TenantSeeder::class);
    $this->seed(MalefashionProductSeeder::class);

    $team = Team::query()->where('slug', 'ali-charisma')->firstOrFail();

    $dress = Product::query()
        ->whereBelongsTo($team)
        ->where('slug', 'long-strappy-dress')
        ->firstOrFail();

    $tee = Product::query()
        ->whereBelongsTo($team)
        ->where('slug', 'jersey-graphic-tee-dolce')
        ->firstOrFail();

    $dressAttributes = ProductCompareAttributes::from($dress);
    $teeAttributes = ProductCompareAttributes::from($tee);

    expect($dressAttributes)->toMatchArray([
        'name' => 'Long strappy dress',
        'price_min' => 390.0,
        'price_max' => 505.0,
        'price_label' => '$390.00 – $505.00',
        'availability' => 'In Stock',
        'sku' => '00117cbn',
        'category' => 'Dress',
        'colors' => ['Black', 'White'],
        'sizes' => ['L', 'M', 'S', 'XL'],
    ])
        ->and($dressAttributes['meta_tags'])->toEqualCanonicalizing([
            'Featured Products',
            'New Products',
        ])
        ->and($dressAttributes['description'])->toContain('Sed ut perspiciatis');

    expect($teeAttributes)->toMatchArray([
        'name' => 'Jersey Graphic Tee Dolce',
        'price_min' => 330.0,
        'price_max' => 410.0,
        'price_label' => '$330.00 – $410.00',
        'availability' => 'In Stock',
        'sku' => '00114c-21',
        'category' => 'T-shirt',
        'colors' => ['Gainsboro', 'LightPink'],
        'sizes' => ['L', 'M', 'S', 'XL', 'XS'],
    ])
        ->and($teeAttributes['meta_tags'])->toEqualCanonicalizing([
            'New Products',
            'Special Products',
            'Woman',
        ]);
});

it('is idempotent when seeding malefashion products twice', function () {
    $this->seed(TenantSeeder::class);
    $this->seed(MalefashionProductSeeder::class);
    $this->seed(MalefashionProductSeeder::class);

    $team = Team::query()->where('slug', 'ali-charisma')->firstOrFail();

    expect(Product::query()->whereBelongsTo($team)->count())->toBe(2);
});
