<?php

use App\Models\Account;
use App\Support\ProductCartList;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function seedCheckoutCart(): void
{
    ProductCartList::add([
        'key' => 'multi-pocket-chest-bag',
        'name' => 'Multi-pocket Chest Bag',
        'price' => 43.48,
        'price_label' => '$43.48',
        'image' => '/malefashion/img/product/product-3.jpg',
    ]);
}

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
    seedCheckoutCart();

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

it('prefills checkout fields for logged-in customers from account and default address', function () {
    seedCheckoutCart();

    $account = Account::factory()->create([
        'name' => 'Nadia Customer',
        'email' => 'nadia@example.com',
        'phone' => '0811111111',
    ]);

    $this->actingAs($account, 'account')
        ->post(route('malefashion.account.addresses.store'), [
            'first_name' => 'Ali',
            'last_name' => 'Charisma',
            'phone' => '08123456789',
            'address1' => 'Jl. Melati 1',
            'city' => 'Malang',
            'zip' => '65141',
            'country' => 'Indonesia',
            'province' => 'Jawa Timur',
            'default' => '1',
        ])
        ->assertRedirect(route('malefashion.account.addresses'));

    $this->actingAs($account, 'account')
        ->get(route('malefashion.checkout'))
        ->assertSuccessful()
        ->assertSee('value="nadia@example.com"', false)
        ->assertSee('value="Ali"', false)
        ->assertSee('value="Charisma"', false)
        ->assertSee('value="Jl. Melati 1"', false)
        ->assertSee('value="Malang"', false)
        ->assertSee('value="65141"', false)
        ->assertSee('value="Jawa Timur"', false)
        ->assertSee('value="08123456789"', false)
        ->assertSee('checkout__logged-in', false)
        ->assertDontSee('>Log in</a>', false);
});

it('links checkout login back to checkout after sign in', function () {
    seedCheckoutCart();

    $account = Account::factory()->create([
        'email' => 'nadia@example.com',
        'password' => bcrypt('password'),
        'is_login' => true,
        'is_active' => true,
    ]);

    $this->get(route('malefashion.account.login', [
        'redirect' => route('malefashion.checkout', absolute: false),
    ]))
        ->assertSuccessful();

    $this->post(route('malefashion.account.login.store'), [
        'email' => 'nadia@example.com',
        'password' => 'password',
    ])
        ->assertRedirect(route('malefashion.checkout'));
});
