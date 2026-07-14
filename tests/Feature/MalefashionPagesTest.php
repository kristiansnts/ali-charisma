<?php

use App\Support\ProductCartList;

it('renders malefashion storefront pages', function (string $uri) {
    $this->get($uri)
        ->assertSuccessful()
        ->assertSee('Ali Charisma', false);
})->with([
    'index' => '/',
    'about' => '/about',
    'work' => '/work',
    'contact' => '/contact',
    'blog' => '/blog',
    'shop' => '/shop',
    'shop details' => '/shop/product',
    'account login' => '/account/login',
    'account register' => '/account/register',
    'cart' => '/cart',
    'wishlist' => '/wishlist',
]);

it('links the header account icon to the customer login page', function () {
    $this->get('/')
        ->assertSuccessful()
        ->assertSee('header__account', false)
        ->assertSee(route('malefashion.account.login', absolute: false), false)
        ->assertSee('fa-user-o', false);
});

it('renders the customer login page like This Is April', function () {
    $this->get('/account/login')
        ->assertSuccessful()
        ->assertSee('Login', false)
        ->assertSee('Enter your email and password to login', false)
        ->assertSee('Forgot your password?', false)
        ->assertSee('Recover password', false)
        ->assertSee('Sign up', false)
        ->assertSee(route('malefashion.account.register', absolute: false), false);
});

it('renders editorial category hero on the home page', function () {
    $this->get('/')
        ->assertSuccessful()
        ->assertSee('hero--editorial', false)
        ->assertSee('>MEN</span>', false)
        ->assertSee('>WOMEN</span>', false)
        ->assertSee('>COLLECTION</span>', false);
});

it('renders april-style category grid on the home page', function () {
    $this->get('/')
        ->assertSuccessful()
        ->assertSee('categories-grid', false)
        ->assertSee('categories-grid__row--3', false)
        ->assertSee(">MEN'S</span>", false)
        ->assertSee(">WOMEN'S</span>", false)
        ->assertSee('>ACCESSORIES</span>', false)
        ->assertDontSee('>TOP</span>', false)
        ->assertDontSee('>DRESS</span>', false)
        ->assertDontSee('>OUTER</span>', false)
        ->assertDontSee('>BOTTOM</span>', false);
});

it('renders the work gallery like the legacy storefront', function () {
    $this->get('/work')
        ->assertSuccessful()
        ->assertSee('Our Work', false)
        ->assertSee('work__gallery', false)
        ->assertSee('VINTAGE', false)
        ->assertSee('SUMMER', false)
        ->assertSee('BEACHWEAR', false)
        ->assertSee('SUNGLASSES', false)
        ->assertSee('WINTER', false)
        ->assertSee('SHORTS', false)
        ->assertSee('ali_charisa_indonesian_fashion.png', false);
});

it('renders a promotion announcement bar above the navbar', function () {
    $this->get('/')
        ->assertSuccessful()
        ->assertSee('announcement-bar', false)
        ->assertSee('FINAL CLEARANCE: Take 20% off', false)
        ->assertDontSee('announcement-bar__track', false);
});

it('renders april-style best seller, new arrival, and shop the look sections', function () {
    $this->get('/')
        ->assertSuccessful()
        ->assertSee('BEST SELLER', false)
        ->assertSee('NEW ARRIVAL', false)
        ->assertSee('Shop the Look', false)
        ->assertSee('product-carousel', false)
        ->assertSee('shop-the-look', false)
        ->assertSee('View all Products', false)
        ->assertDontSee('filter__controls', false)
        ->assertDontSee('>New Products</li>', false)
        ->assertDontSee('product__filter', false);
});

it('renders a shopify-style checkout page with session cart items', function () {
    ProductCartList::add([
        'key' => 'multi-pocket-chest-bag',
        'name' => 'Multi-pocket Chest Bag',
        'price' => 43.48,
        'price_label' => '$43.48',
        'image' => '/malefashion/img/product/product-3.jpg',
    ]);

    $this->get('/checkout')
        ->assertSuccessful()
        ->assertSee('announcement-bar', false)
        ->assertSee('header--checkout', false)
        ->assertSee('header__account', false)
        ->assertDontSee('header__compare', false)
        ->assertSee('checkout', false)
        ->assertSee('Contact', false)
        ->assertSee('Delivery', false)
        ->assertSee('Shipping method', false)
        ->assertSee('Payment', false)
        ->assertSee('Pay now', false)
        ->assertSee('Order summary', false)
        ->assertSee('Return to cart', false)
        ->assertSee('Multi-pocket Chest Bag', false)
        ->assertSee('$43.48', false);
});

it('redirects empty checkout back to the cart', function () {
    $this->get('/checkout')
        ->assertRedirect(route('malefashion.cart'));
});

it('renders the cart page from session cart data', function () {
    ProductCartList::add([
        'key' => 'ankle-boots',
        'name' => 'Ankle Boots',
        'price' => 98.49,
        'price_label' => '$98.49',
        'image' => '/malefashion/img/product/product-6.jpg',
    ]);

    $this->get('/cart')
        ->assertSuccessful()
        ->assertSee('Ankle Boots', false)
        ->assertSee('$98.49', false)
        ->assertSee(route('malefashion.checkout', absolute: false), false)
        ->assertDontSee('T-shirt Contrast Pocket', false);
});

it('links the cart proceed button to checkout', function () {
    ProductCartList::add([
        'key' => 'ankle-boots',
        'name' => 'Ankle Boots',
        'price' => 98.49,
        'price_label' => '$98.49',
        'image' => '/malefashion/img/product/product-6.jpg',
    ]);

    $this->get('/cart')
        ->assertSuccessful()
        ->assertSee(route('malefashion.checkout', absolute: false), false);
});

it('aligns header nav icons without showing a cart total', function () {
    $css = file_get_contents(public_path('malefashion/css/style.css'));

    expect($css)
        ->toContain('.header__nav__option a img')
        ->toContain('filter: invert(1)')
        ->toContain('justify-content: flex-end')
        ->not->toMatch('/\.header__nav__option \.price\s*\{/');

    $this->get('/')
        ->assertSuccessful()
        ->assertSee('malefashion/img/icon/heart.png', false)
        ->assertSee('malefashion/img/icon/cart.png', false)
        ->assertDontSee('data-cart-total', false);
});

it('renders the Ali Charisma style product details page', function () {
    $this->get('/shop/product')
        ->assertSuccessful()
        ->assertSee('Hoodie with slogan', false)
        ->assertSee('$210.00', false)
        ->assertSee('$280.00', false)
        ->assertSee('00115fdr', false)
        ->assertSee('New Products', false)
        ->assertSee('Sweatshirts &amp; Hoodies', false)
        ->assertSee('ac-pdp', false)
        ->assertSee('data-add-to-cart', false)
        ->assertSee('data-wishlist-toggle', false)
        ->assertSee('Add to cart', false)
        ->assertSee('Additional information', false)
        ->assertSee('Related products', false)
        ->assertDontSee('Hooded thermal anorak', false);
});
