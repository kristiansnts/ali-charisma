@extends('layouts.malefashion')

@section('title', 'Wishlist — Ali Charisma')

@section('content')
<section class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__text">
                    <h4>Wishlist</h4>
                    <div class="breadcrumb__links">
                        <a href="{{ route('malefashion.home') }}">Home</a>
                        <span>Wishlist</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="wishlist spad">
    <div class="container">
        @if ($items === [])
            <div class="wishlist__empty">
                <p>Your wishlist is empty.</p>
                <a href="{{ route('malefashion.shop') }}" class="primary-btn">Continue shopping</a>
            </div>
        @else
            <div class="row">
                @foreach ($items as $item)
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="product__item product__item--april">
                            <div class="product__item__pic set-bg" data-setbg="{{ $item['image'] }}">
                                <ul class="product__hover">
                                    <li>
                                        <a href="#" data-wishlist-remove="{{ $item['key'] }}" aria-label="Remove from wishlist">
                                            <img src="{{ asset('malefashion/img/icon/heart.png') }}" alt="">
                                            <span>Remove</span>
                                        </a>
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
                                    data-cart-price="{{ preg_replace('/[^0-9.]/', '', $item['price']) }}"
                                    data-cart-price-label="{{ $item['price'] }}"
                                    data-cart-image="{{ $item['image'] }}"
                                >+ Add To Cart</a>
                                <h5>{{ $item['price'] }}</h5>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
