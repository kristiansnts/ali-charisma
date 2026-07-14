<?php

use App\Support\ProductCompareList;
use Database\Seeders\MalefashionProductSeeder;
use Database\Seeders\TenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use TomatoPHP\FilamentEcommerce\Models\Product;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(TenantSeeder::class);
    $this->seed(MalefashionProductSeeder::class);
});

it('shows a compare trigger in the header nav icons', function () {
    $this->get('/')
        ->assertSuccessful()
        ->assertSee('data-compare-open', false)
        ->assertSee('data-compare-count', false)
        ->assertSee('compare-modal', false);
});

it('adds products to the compare list and returns compare table html', function () {
    $dress = Product::query()->where('slug', 'long-strappy-dress')->firstOrFail();
    $tee = Product::query()->where('slug', 'jersey-graphic-tee-dolce')->firstOrFail();

    $this->postJson(route('malefashion.compare.store', $dress))
        ->assertSuccessful()
        ->assertJson([
            'ok' => true,
            'count' => 1,
        ]);

    $this->postJson(route('malefashion.compare.store', $tee))
        ->assertSuccessful()
        ->assertJson([
            'ok' => true,
            'count' => 2,
        ]);

    expect(ProductCompareList::count())->toBe(2);

    $response = $this->getJson(route('malefashion.compare.index'))
        ->assertSuccessful()
        ->assertJsonPath('count', 2)
        ->assertJsonPath('products.0.name', 'Long strappy dress')
        ->assertJsonPath('products.1.name', 'Jersey Graphic Tee Dolce');

    expect($response->json('html'))
        ->toContain('Long strappy dress')
        ->toContain('Jersey Graphic Tee Dolce')
        ->toContain('Availability')
        ->toContain('00117cbn')
        ->toContain('Black, White');
});

it('removes a product and can clear the compare list', function () {
    $dress = Product::query()->where('slug', 'long-strappy-dress')->firstOrFail();
    $tee = Product::query()->where('slug', 'jersey-graphic-tee-dolce')->firstOrFail();

    ProductCompareList::add($dress->id);
    ProductCompareList::add($tee->id);

    $this->deleteJson(route('malefashion.compare.destroy', $dress))
        ->assertSuccessful()
        ->assertJson([
            'ok' => true,
            'count' => 1,
        ]);

    $this->deleteJson(route('malefashion.compare.clear'))
        ->assertSuccessful()
        ->assertJson([
            'ok' => true,
            'count' => 0,
        ]);

    expect(ProductCompareList::count())->toBe(0);
});

it('rejects adding more than the max compare items', function () {
    $extra = Product::query()->where('slug', 'long-strappy-dress')->firstOrFail();

    session([ProductCompareList::SESSION_KEY => [9001, 9002, 9003, 9004]]);

    $this->postJson(route('malefashion.compare.store', $extra))
        ->assertStatus(422)
        ->assertJson([
            'ok' => false,
            'count' => 4,
        ]);
});
