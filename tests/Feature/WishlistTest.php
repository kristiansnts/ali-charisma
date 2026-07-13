<?php

use App\Support\ProductWishlistList;

it('renders the wishlist page and header wishlist link', function () {
    $this->get('/wishlist')
        ->assertSuccessful()
        ->assertSee('Wishlist', false)
        ->assertSee('Your wishlist is empty.', false);

    $this->get('/')
        ->assertSuccessful()
        ->assertSee('data-wishlist-count', false)
        ->assertSee(route('malefashion.wishlist', absolute: false), false);
});

it('adds and removes wishlist items via json endpoints', function () {
    $this->postJson(route('malefashion.wishlist.store'), [
        'key' => 'long-strappy-dress',
        'name' => 'Long strappy dress',
        'price' => '$390.00',
        'image' => '/malefashion/img/womens_coll.jpg',
    ])
        ->assertSuccessful()
        ->assertJson([
            'ok' => true,
            'count' => 1,
            'in_wishlist' => true,
        ]);

    expect(ProductWishlistList::count())->toBe(1);

    $this->get('/wishlist')
        ->assertSuccessful()
        ->assertSee('Long strappy dress', false)
        ->assertDontSee('Your wishlist is empty.', false);

    $this->deleteJson(route('malefashion.wishlist.destroy', ['key' => 'long-strappy-dress']))
        ->assertSuccessful()
        ->assertJson([
            'ok' => true,
            'count' => 0,
        ]);

    expect(ProductWishlistList::count())->toBe(0);
});
