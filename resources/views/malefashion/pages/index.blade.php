@extends('layouts.malefashion')

@section('title', 'Home — Ali Charisma')

@section('content')
@php
    $dress = $compareableProducts['long-strappy-dress'] ?? null;
    $tee = $compareableProducts['jersey-graphic-tee-dolce'] ?? null;

    $bestSellers = [
        [
            'key' => 'long-strappy-dress',
            'name' => $dress->name ?? 'Long strappy dress',
            'price' => '$390.00',
            'image' => asset('malefashion/img/womens_coll.jpg'),
            'compare_id' => $dress?->id,
            'label' => 'New',
        ],
        [
            'key' => 'jersey-graphic-tee-dolce',
            'name' => $tee->name ?? 'Jersey Graphic Tee Dolce',
            'price' => '$330.00',
            'image' => asset('malefashion/img/product/product-7.jpg'),
            'compare_id' => $tee?->id,
            'label' => null,
        ],
        [
            'key' => 'multi-pocket-chest-bag',
            'name' => 'Multi-pocket Chest Bag',
            'price' => '$43.48',
            'image' => asset('malefashion/img/product/product-3.jpg'),
            'compare_id' => null,
            'label' => 'Sale',
        ],
        [
            'key' => 'diagonal-textured-cap',
            'name' => 'Diagonal Textured Cap',
            'price' => '$60.90',
            'image' => asset('malefashion/img/product/product-4.jpg'),
            'compare_id' => null,
            'label' => null,
        ],
        [
            'key' => 'ankle-boots',
            'name' => 'Ankle Boots',
            'price' => '$98.49',
            'image' => asset('malefashion/img/product/product-6.jpg'),
            'compare_id' => null,
            'label' => 'Sale',
        ],
    ];

    $newArrivals = [
        [
            'key' => 'leather-backpack',
            'name' => 'Leather Backpack',
            'price' => '$31.37',
            'image' => asset('malefashion/img/product/product-5.jpg'),
            'compare_id' => null,
            'label' => 'New',
        ],
        [
            'key' => 'tshirt-contrast-pocket',
            'name' => 'T-shirt Contrast Pocket',
            'price' => '$49.66',
            'image' => asset('malefashion/img/product/product-7.jpg'),
            'compare_id' => null,
            'label' => null,
        ],
        [
            'key' => 'basic-flowing-scarf',
            'name' => 'Basic Flowing Scarf',
            'price' => '$26.28',
            'image' => asset('malefashion/img/product/product-8.jpg'),
            'compare_id' => null,
            'label' => null,
        ],
        [
            'key' => 'pique-biker-jacket',
            'name' => 'Piqué Biker Jacket',
            'price' => '$67.24',
            'image' => asset('malefashion/img/product/product-2.jpg'),
            'compare_id' => null,
            'label' => 'New',
        ],
        [
            'key' => 'contrast-rain-jacket',
            'name' => 'Contrast Rain Jacket',
            'price' => '$35.00',
            'image' => asset('malefashion/img/product/product-1.jpg'),
            'compare_id' => null,
            'label' => null,
        ],
    ];

    $shopTheLook = [
        [
            'name' => 'Long strappy dress',
            'price' => '$390.00',
            'image' => asset('malefashion/img/womens_coll.jpg'),
            'url' => route('malefashion.shop-details'),
        ],
        [
            'name' => 'Editorial Look — Summer',
            'price' => '$289.00',
            'image' => asset('malefashion/img/work/ali_charisma_model_comp.jpeg'),
            'url' => route('malefashion.shop'),
        ],
        [
            'name' => 'Jersey Graphic Tee',
            'price' => '$330.00',
            'image' => asset('malefashion/img/mens_coll.jpg'),
            'url' => route('malefashion.shop-details'),
        ],
        [
            'name' => 'Runway White Shirt',
            'price' => '$259.00',
            'image' => asset('malefashion/img/work/whiteshirt_man.jpg'),
            'url' => route('malefashion.shop'),
        ],
        [
            'name' => 'Collection Look',
            'price' => '$419.00',
            'image' => asset('malefashion/img/work/ali_charisa_indonesian_fashion.png'),
            'url' => route('malefashion.shop'),
        ],
    ];
@endphp

<!-- Hero Section Begin — full-bleed category slides (This Is April–style) -->
    <section class="hero hero--editorial">
        <div class="hero__slider owl-carousel">
            <a href="{{ route('malefashion.shop') }}" class="hero__items set-bg" data-setbg="{{ asset('malefashion/img/mens_coll.jpg') }}">
                <span class="hero__label">MEN</span>
            </a>
            <a href="{{ route('malefashion.shop') }}" class="hero__items set-bg" data-setbg="{{ asset('malefashion/img/womens_coll.jpg') }}">
                <span class="hero__label">WOMEN</span>
            </a>
            <a href="{{ route('malefashion.shop') }}" class="hero__items set-bg" data-setbg="{{ asset('malefashion/img/work/ali_charisma_model_comp.jpeg') }}">
                <span class="hero__label">COLLECTION</span>
            </a>
        </div>
    </section>
    <!-- Hero Section End -->

    <!-- Category grid -->
    <section class="categories-grid">
        <div class="container-fluid categories-grid__inner">
            <div class="categories-grid__row categories-grid__row--3">
                <a href="{{ route('malefashion.shop') }}" class="categories-grid__item">
                    <div class="categories-grid__pic">
                        <img src="{{ asset('malefashion/img/mens_coll.jpg') }}" alt="Men's Collection">
                    </div>
                    <span class="categories-grid__label">MEN'S</span>
                </a>
                <a href="{{ route('malefashion.shop') }}" class="categories-grid__item">
                    <div class="categories-grid__pic">
                        <img src="{{ asset('malefashion/img/womens_coll.jpg') }}" alt="Women's Collection">
                    </div>
                    <span class="categories-grid__label">WOMEN'S</span>
                </a>
                <a href="{{ route('malefashion.shop') }}" class="categories-grid__item">
                    <div class="categories-grid__pic">
                        <img src="{{ asset('malefashion/img/banner/banner-3.jpg') }}" alt="Accessories">
                    </div>
                    <span class="categories-grid__label">ACCESSORIES</span>
                </a>
            </div>
        </div>
    </section>
    <!-- Category grid End -->

    <!-- Best Seller -->
    <section class="home-products">
        <div class="container">
            <div class="home-products__head">
                <h2 class="home-products__title">BEST SELLER</h2>
            </div>
            <div class="product-carousel owl-carousel">
                @foreach ($bestSellers as $product)
                    <div class="product__item product__item--april">
                        <div class="product__item__pic set-bg" data-setbg="{{ $product['image'] }}">
                            @if ($product['label'])
                                <span class="label">{{ $product['label'] }}</span>
                            @endif
                            <ul class="product__hover">
                                <li>
                                    <a
                                        href="#"
                                        data-wishlist-toggle
                                        data-wishlist-key="{{ $product['key'] }}"
                                        data-wishlist-name="{{ $product['name'] }}"
                                        data-wishlist-price="{{ $product['price'] }}"
                                        data-wishlist-image="{{ $product['image'] }}"
                                        @if ($product['compare_id']) data-wishlist-product-id="{{ $product['compare_id'] }}" @endif
                                        aria-label="Add to wishlist"
                                    >
                                        <img src="{{ asset('malefashion/img/icon/heart.png') }}" alt="">
                                        <span>Wishlist</span>
                                    </a>
                                </li>
                                <li>
                                    @if ($product['compare_id'])
                                        <a href="#" data-compare-add="{{ $product['compare_id'] }}">
                                            <img src="{{ asset('malefashion/img/icon/compare.png') }}" alt=""> <span>Compare</span>
                                        </a>
                                    @else
                                        <a href="#"><img src="{{ asset('malefashion/img/icon/compare.png') }}" alt=""> <span>Compare</span></a>
                                    @endif
                                </li>
                                <li><a href="{{ route('malefashion.shop-details') }}"><img src="{{ asset('malefashion/img/icon/search.png') }}" alt=""></a></li>
                            </ul>
                        </div>
                        <div class="product__item__text">
                            <h6>{{ $product['name'] }}</h6>
                            <a
                                href="#"
                                class="add-cart"
                                data-add-to-cart
                                data-cart-key="{{ $product['key'] }}"
                                data-cart-name="{{ $product['name'] }}"
                                data-cart-price="{{ preg_replace('/[^0-9.]/', '', $product['price']) }}"
                                data-cart-price-label="{{ $product['price'] }}"
                                data-cart-image="{{ $product['image'] }}"
                                @if ($product['compare_id']) data-cart-product-id="{{ $product['compare_id'] }}" @endif
                            >+ Add To Cart</a>
                            <h5>{{ $product['price'] }}</h5>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="home-products__footer">
                <a href="{{ route('malefashion.shop') }}" class="home-products__view-all">View all Products</a>
            </div>
        </div>
    </section>

    <!-- New Arrival -->
    <section class="home-products home-products--new">
        <div class="container">
            <div class="home-products__head">
                <h2 class="home-products__title">NEW ARRIVAL</h2>
            </div>
            <div class="product-carousel owl-carousel">
                @foreach ($newArrivals as $product)
                    <div class="product__item product__item--april">
                        <div class="product__item__pic set-bg" data-setbg="{{ $product['image'] }}">
                            @if ($product['label'])
                                <span class="label">{{ $product['label'] }}</span>
                            @endif
                            <ul class="product__hover">
                                <li>
                                    <a
                                        href="#"
                                        data-wishlist-toggle
                                        data-wishlist-key="{{ $product['key'] }}"
                                        data-wishlist-name="{{ $product['name'] }}"
                                        data-wishlist-price="{{ $product['price'] }}"
                                        data-wishlist-image="{{ $product['image'] }}"
                                        aria-label="Add to wishlist"
                                    >
                                        <img src="{{ asset('malefashion/img/icon/heart.png') }}" alt="">
                                        <span>Wishlist</span>
                                    </a>
                                </li>
                                <li><a href="#"><img src="{{ asset('malefashion/img/icon/compare.png') }}" alt=""> <span>Compare</span></a></li>
                                <li><a href="{{ route('malefashion.shop-details') }}"><img src="{{ asset('malefashion/img/icon/search.png') }}" alt=""></a></li>
                            </ul>
                        </div>
                        <div class="product__item__text">
                            <h6>{{ $product['name'] }}</h6>
                            <a
                                href="#"
                                class="add-cart"
                                data-add-to-cart
                                data-cart-key="{{ $product['key'] }}"
                                data-cart-name="{{ $product['name'] }}"
                                data-cart-price="{{ preg_replace('/[^0-9.]/', '', $product['price']) }}"
                                data-cart-price-label="{{ $product['price'] }}"
                                data-cart-image="{{ $product['image'] }}"
                            >+ Add To Cart</a>
                            <h5>{{ $product['price'] }}</h5>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="home-products__footer">
                <a href="{{ route('malefashion.shop') }}" class="home-products__view-all">View all Products</a>
            </div>
        </div>
    </section>

    <!-- Shop the Look -->
    <section class="shop-the-look">
        <div class="container">
            <div class="home-products__head">
                <h2 class="home-products__title">Shop the Look</h2>
            </div>
        </div>
        <div class="shop-the-look__inner">
            <div class="shop-the-look__carousel owl-carousel">
                @foreach ($shopTheLook as $look)
                    <a href="{{ $look['url'] }}" class="shop-the-look__item">
                        <div class="shop-the-look__pic">
                            <img src="{{ $look['image'] }}" alt="{{ $look['name'] }}">
                        </div>
                        <div class="shop-the-look__meta">
                            <span class="shop-the-look__eyebrow">Shop the look</span>
                            <h6>{{ $look['name'] }}</h6>
                            <p>{{ $look['price'] }}</p>
                            <span class="shop-the-look__cta">View product</span>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="home-products__footer">
                <a href="{{ route('malefashion.shop') }}" class="home-products__view-all">View products</a>
            </div>
        </div>
    </section>
@endsection
