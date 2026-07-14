<?php

use App\Support\ProductCartList;

it('adds a product to the storefront cart and returns an upsell dialog', function () {
    $response = $this->postJson(route('malefashion.storefront-cart.store'), [
        'key' => 'sterling-sweater-grey',
        'name' => 'Sterling Sweater Grey',
        'price' => 289,
        'price_label' => '$289.00',
        'image' => '/malefashion/img/product/product-2.jpg',
    ])
        ->assertSuccessful()
        ->assertJson([
            'ok' => true,
            'count' => 1,
            'total' => '$289.00',
        ]);

    expect(ProductCartList::count())->toBe(1);
    expect($response->json('html'))
        ->toContain('You added')
        ->toContain('Sterling Sweater Grey')
        ->toContain('Customers who bought this item also bought')
        ->toContain('Checkout')
        ->toContain('Add to cart');

    $this->postJson(route('malefashion.storefront-cart.store'), [
        'key' => 'sterling-sweater-grey',
        'name' => 'Sterling Sweater Grey',
        'price' => 289,
        'price_label' => '$289.00',
        'image' => '/malefashion/img/product/product-2.jpg',
    ])
        ->assertSuccessful()
        ->assertJsonPath('count', 2)
        ->assertJsonPath('total', '$578.00');
});

it('exposes cart upsell modal markup on storefront pages', function () {
    $this->get('/')
        ->assertSuccessful()
        ->assertSee('cart-upsell-modal', false)
        ->assertSee('cart-drawer', false)
        ->assertSee('data-cart-open', false)
        ->assertSee('data-add-to-cart', false)
        ->assertSee('malefashionCart', false)
        ->assertSee('drawerUrl', false);
});

it('returns cart drawer html for the sidebar', function () {
    ProductCartList::add([
        'key' => 'sterling-sweater-grey',
        'name' => 'Sterling Sweater Grey',
        'price' => 289,
        'price_label' => '$289.00',
        'image' => '/malefashion/img/product/product-2.jpg',
    ]);

    $this->getJson(route('malefashion.storefront-cart.drawer'))
        ->assertSuccessful()
        ->assertJson([
            'ok' => true,
            'count' => 1,
            'total' => '$289.00',
        ])
        ->assertJsonPath('html', fn (string $html): bool => str_contains($html, 'Sterling Sweater Grey')
            && str_contains($html, 'Checkout')
            && str_contains($html, 'Add order note'));
});

it('updates and removes cart items via json for the drawer', function () {
    ProductCartList::add([
        'key' => 'evie-sweater-black',
        'name' => 'Evie Sweater Black',
        'price' => 271.15,
        'price_label' => '$271.15',
        'image' => '/malefashion/img/product/product-2.jpg',
    ]);

    $this->putJson(route('malefashion.storefront-cart.sync'), [
        'qty' => [
            'evie-sweater-black' => 3,
        ],
    ])
        ->assertSuccessful()
        ->assertJsonPath('count', 3)
        ->assertJsonPath('total', '$813.45');

    $this->deleteJson(route('malefashion.storefront-cart.destroy', 'evie-sweater-black'))
        ->assertSuccessful()
        ->assertJsonPath('count', 0)
        ->assertJsonPath('html', fn (string $html): bool => str_contains($html, 'Your cart is empty.'));
});

it('syncs quantities and removes cart items', function () {
    ProductCartList::add([
        'key' => 'evie-sweater-black',
        'name' => 'Evie Sweater Black',
        'price' => 271.15,
        'price_label' => '$271.15',
        'image' => '/malefashion/img/product/product-2.jpg',
    ]);
    ProductCartList::add([
        'key' => 'daliya-cardigan',
        'name' => 'Daliya Cardigan Dark Brown',
        'price' => 254.15,
        'price_label' => '$254.15',
        'image' => '/malefashion/img/product/product-3.jpg',
    ]);

    $this->put(route('malefashion.storefront-cart.sync'), [
        'qty' => [
            'evie-sweater-black' => 3,
            'daliya-cardigan' => 1,
        ],
    ])->assertRedirect(route('malefashion.cart'));

    expect(ProductCartList::count())->toBe(4);
    expect(ProductCartList::subtotal())->toBe(round(271.15 * 3 + 254.15, 2));

    $this->delete(route('malefashion.storefront-cart.destroy', 'daliya-cardigan'))
        ->assertRedirect(route('malefashion.cart'));

    expect(ProductCartList::count())->toBe(3);
    expect(collect(ProductCartList::items())->pluck('key')->all())->toBe(['evie-sweater-black']);
});

it('carries add-to-cart items through cart and checkout pages', function () {
    $this->postJson(route('malefashion.storefront-cart.store'), [
        'key' => 'multi-pocket-chest-bag',
        'name' => 'Multi-pocket Chest Bag',
        'price' => 43.48,
        'price_label' => '$43.48',
        'image' => '/malefashion/img/product/product-3.jpg',
    ])->assertSuccessful();

    $this->get(route('malefashion.cart'))
        ->assertSuccessful()
        ->assertSee('Multi-pocket Chest Bag', false)
        ->assertSee('$43.48', false);

    $this->get(route('malefashion.checkout'))
        ->assertSuccessful()
        ->assertSee('Multi-pocket Chest Bag', false)
        ->assertSee('$43.48', false)
        ->assertDontSee('T-shirt Contrast Pocket', false);
});
