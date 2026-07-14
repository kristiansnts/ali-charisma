@extends('layouts.malefashion')

@php
    $product = [
        'key' => 'hoodie-with-slogan',
        'name' => 'Hoodie with slogan',
        'price' => 210,
        'price_label' => '$210.00',
        'compare_at' => '$280.00',
        'sku' => '00115fdr',
        'category' => 'New Products',
        'tag' => 'Sweatshirts & Hoodies',
        'image' => asset('malefashion/img/shop-details/product-big-2.png'),
        'short' => 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit.',
        'colors' => ['Black', 'Gainsboro'],
        'gallery' => [
            asset('malefashion/img/shop-details/product-big-2.png'),
            asset('malefashion/img/shop-details/product-big-3.png'),
            asset('malefashion/img/shop-details/product-big.png'),
            asset('malefashion/img/shop-details/product-big-4.png'),
        ],
    ];

    $related = [
        [
            'name' => 'Flared trousers',
            'price' => '$274.00 – $309.00',
            'image' => asset('malefashion/img/product/product-1.jpg'),
            'key' => 'flared-trousers',
            'cart_price' => '274.00',
            'badge' => null,
        ],
        [
            'name' => 'Print sweater',
            'price' => '$270.00 – $290.00',
            'image' => asset('malefashion/img/product/product-2.jpg'),
            'key' => 'print-sweater',
            'cart_price' => '270.00',
            'badge' => 'Hot',
        ],
        [
            'name' => 'Sweater with slogan',
            'price' => '$210.00',
            'image' => asset('malefashion/img/product/product-5.jpg'),
            'key' => 'sweater-with-slogan',
            'cart_price' => '210.00',
            'badge' => 'Sale',
            'compare_at' => '$280.00',
        ],
        [
            'name' => 'Long strappy dress',
            'price' => '$390.00 – $505.00',
            'image' => asset('malefashion/img/womens_coll.jpg'),
            'key' => 'long-strappy-dress',
            'cart_price' => '390.00',
            'badge' => 'Sale',
            'compare_id' => optional($compareableProducts['long-strappy-dress'] ?? null)->id,
        ],
    ];
@endphp

@section('title', $product['name'].' — Ali Charisma')

@section('content')
<section class="ac-pdp">
    <div class="container">
        <nav class="ac-pdp__breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('malefashion.home') }}">Home</a>
            <span>/</span>
            <a href="{{ route('malefashion.shop') }}">{{ $product['category'] }}</a>
            <span>/</span>
            <span aria-current="page">{{ $product['name'] }}</span>
        </nav>

        <div class="row ac-pdp__row">
            <div class="col-lg-6">
                <div class="ac-pdp__gallery">
                    <div class="ac-pdp__badges">
                        <span class="ac-pdp__badge ac-pdp__badge--sale">Sale!</span>
                        <span class="ac-pdp__badge ac-pdp__badge--hot">Hot</span>
                    </div>
                    <div class="ac-pdp__stage">
                        <img
                            id="ac-pdp-main-image"
                            src="{{ $product['gallery'][0] }}"
                            alt="{{ $product['name'] }}"
                        >
                    </div>
                    <div class="ac-pdp__thumbs" role="list">
                        @foreach ($product['gallery'] as $index => $image)
                            <button
                                type="button"
                                class="ac-pdp__thumb {{ $index === 0 ? 'is-active' : '' }}"
                                data-ac-pdp-thumb
                                data-image="{{ $image }}"
                                aria-label="View image {{ $index + 1 }}"
                            >
                                <img src="{{ $image }}" alt="">
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="ac-pdp__summary">
                    <h1 class="ac-pdp__title">{{ $product['name'] }}</h1>

                    <div class="ac-pdp__price">
                        <span class="ac-pdp__price-compare">{{ $product['compare_at'] }}</span>
                        <span class="ac-pdp__price-sale">{{ $product['price_label'] }}</span>
                    </div>

                    <div class="ac-pdp__meta-top">
                        <span><strong>SKU:</strong> {{ $product['sku'] }}</span>
                        <span><strong>Availability:</strong> In stock</span>
                    </div>

                    <p class="ac-pdp__excerpt">{{ $product['short'] }}</p>

                    <div class="ac-pdp__option">
                        <label class="ac-pdp__option-label" for="ac-pdp-color">Color</label>
                        <select id="ac-pdp-color" class="ac-pdp__select" name="color">
                            <option value="">Choose an option</option>
                            @foreach ($product['colors'] as $color)
                                <option value="{{ $color }}">{{ $color }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="ac-pdp__purchase">
                        <div class="quantity">
                            <div class="pro-qty">
                                <input type="text" id="ac-pdp-qty" value="1" min="1" max="99" aria-label="Quantity">
                            </div>
                        </div>
                        <button
                            type="button"
                            class="primary-btn ac-pdp__add-cart"
                            data-add-to-cart
                            data-cart-key="{{ $product['key'] }}"
                            data-cart-name="{{ $product['name'] }}"
                            data-cart-price="{{ $product['price'] }}"
                            data-cart-price-label="{{ $product['price_label'] }}"
                            data-cart-image="{{ $product['image'] }}"
                        >Add to cart</button>
                    </div>

                    <div class="ac-pdp__actions">
                        <a
                            href="#"
                            data-wishlist-toggle
                            data-wishlist-key="{{ $product['key'] }}"
                            data-wishlist-name="{{ $product['name'] }}"
                            data-wishlist-price="{{ $product['price_label'] }}"
                            data-wishlist-image="{{ $product['image'] }}"
                        ><i class="fa fa-heart-o"></i> Add to Wishlist</a>
                        <a href="#" data-compare-open><i class="fa fa-exchange"></i> Add to Compare</a>
                    </div>

                    <ul class="ac-pdp__meta">
                        <li><span>SKU:</span> {{ $product['sku'] }}</li>
                        <li><span>Category:</span> <a href="{{ route('malefashion.shop') }}">{{ $product['category'] }}</a></li>
                        <li><span>Tag:</span> {{ $product['tag'] }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="ac-pdp__tabs product__details__tab">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#ac-pdp-desc" role="tab">Description</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#ac-pdp-info" role="tab">Additional information</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#ac-pdp-reviews" role="tab">Reviews (0)</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="ac-pdp-desc" role="tabpanel">
                    <div class="product__details__tab__content">
                        <div class="product__details__tab__content__item">
                            <h5>Sample Paragraph Text</h5>
                            <p>Lorem ipsum dolor sit amet conse ctetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
                        </div>
                        <div class="product__details__tab__content__item">
                            <h5>Sample Unordered List</h5>
                            <ul>
                                <li>Fabric 1: 100% Polyester</li>
                                <li>Fabric 2: 100% Polyester, Lining: 100% Polyester</li>
                                <li>Fabric 3: 75% Polyester, 20% Viscose, 5% Elastane</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="ac-pdp-info" role="tabpanel">
                    <div class="product__details__tab__content">
                        <table class="ac-pdp__info-table">
                            <tbody>
                                <tr>
                                    <th>Weight</th>
                                    <td>9 kg</td>
                                </tr>
                                <tr>
                                    <th>Dimensions</th>
                                    <td>12 × 5 × 7 cm</td>
                                </tr>
                                <tr>
                                    <th>Color</th>
                                    <td>{{ implode(', ', $product['colors']) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="ac-pdp-reviews" role="tabpanel">
                    <div class="product__details__tab__content">
                        <p>There are no reviews yet.</p>
                        <p>Be the first to review “{{ $product['name'] }}”.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="related spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h3 class="related-title">Related products</h3>
            </div>
        </div>
        <div class="row">
            @foreach ($related as $item)
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="product__item {{ ($item['badge'] ?? null) === 'Sale' ? 'sale' : '' }}">
                        <div class="product__item__pic set-bg" data-setbg="{{ $item['image'] }}">
                            @if (! empty($item['badge']))
                                <span class="label">{{ $item['badge'] }}</span>
                            @endif
                            <ul class="product__hover">
                                <li>
                                    <a
                                        href="#"
                                        data-wishlist-toggle
                                        data-wishlist-key="{{ $item['key'] }}"
                                        data-wishlist-name="{{ $item['name'] }}"
                                        data-wishlist-price="{{ $item['price'] }}"
                                        data-wishlist-image="{{ $item['image'] }}"
                                        @if (! empty($item['compare_id'])) data-wishlist-product-id="{{ $item['compare_id'] }}" @endif
                                        aria-label="Add to wishlist"
                                    >
                                        <img src="{{ asset('malefashion/img/icon/heart.png') }}" alt="">
                                    </a>
                                </li>
                                <li>
                                    @if (! empty($item['compare_id']))
                                        <a href="#" data-compare-add="{{ $item['compare_id'] }}">
                                            <img src="{{ asset('malefashion/img/icon/compare.png') }}" alt=""> <span>Compare</span>
                                        </a>
                                    @else
                                        <a href="#"><img src="{{ asset('malefashion/img/icon/compare.png') }}" alt=""> <span>Compare</span></a>
                                    @endif
                                </li>
                                <li>
                                    <a href="{{ route('malefashion.shop-details') }}">
                                        <img src="{{ asset('malefashion/img/icon/search.png') }}" alt="">
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="product__item__text">
                            <h6>{{ $item['name'] }}</h6>
                            <a
                                href="#"
                                class="add-cart"
                                data-add-to-cart
                                data-cart-key="{{ $item['key'] }}"
                                data-cart-name="{{ $item['name'] }}"
                                data-cart-price="{{ $item['cart_price'] }}"
                                data-cart-price-label="{{ $item['price'] }}"
                                data-cart-image="{{ $item['image'] }}"
                                @if (! empty($item['compare_id'])) data-cart-product-id="{{ $item['compare_id'] }}" @endif
                            >+ Add To Cart</a>
                            <h5>
                                @if (! empty($item['compare_at']))
                                    <span style="text-decoration: line-through; color: #b7b7b7; font-weight: 400; margin-right: 6px;">{{ $item['compare_at'] }}</span>
                                @endif
                                {{ $item['price'] }}
                            </h5>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
(function ($) {
    $(document).on('click', '[data-ac-pdp-thumb]', function () {
        var src = $(this).data('image');
        if (!src) {
            return;
        }
        $('#ac-pdp-main-image').attr('src', src);
        $('[data-ac-pdp-thumb]').removeClass('is-active');
        $(this).addClass('is-active');
    });
})(jQuery);
</script>
@endpush
