@extends('layouts.malefashion')

@section('title', 'Shopping Cart — Ali Charisma')

@section('content')
<section class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__text">
                    <h4>Shopping Cart</h4>
                    <div class="breadcrumb__links">
                        <a href="{{ route('malefashion.home') }}">Home</a>
                        <a href="{{ route('malefashion.shop') }}">Shop</a>
                        <span>Shopping Cart</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="shopping-cart spad">
    <div class="container">
        @if (session('status'))
            <div class="alert alert-info mb-4" role="status">{{ session('status') }}</div>
        @endif

        @if ($items === [])
            <div class="cart-empty text-center py-5">
                <p class="mb-4">Your cart is empty.</p>
                <a href="{{ route('malefashion.shop') }}" class="primary-btn">Continue shopping</a>
            </div>
        @else
            <div class="row">
                <div class="col-lg-8">
                    <form action="{{ route('malefashion.storefront-cart.sync') }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="shopping__cart__table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr>
                                            <td class="product__cart__item">
                                                <div class="product__cart__item__pic">
                                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                                                </div>
                                                <div class="product__cart__item__text">
                                                    <h6>{{ $item['name'] }}</h6>
                                                    <h5>{{ $item['price_label'] }}</h5>
                                                </div>
                                            </td>
                                            <td class="quantity__item">
                                                <div class="quantity">
                                                    <div class="pro-qty-2">
                                                        <input type="number" name="qty[{{ $item['key'] }}]" value="{{ $item['qty'] }}" min="0" max="99">
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="cart__price">${{ number_format($item['price'] * $item['qty'], 2) }}</td>
                                            <td class="cart__close">
                                                <button
                                                    type="submit"
                                                    form="cart-remove-{{ $item['key'] }}"
                                                    class="cart__remove"
                                                    aria-label="Remove {{ $item['name'] }}"
                                                >
                                                    <i class="fa fa-close"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="continue__btn">
                                    <a href="{{ route('malefashion.shop') }}">Continue Shopping</a>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="continue__btn update__btn">
                                    <button type="submit"><i class="fa fa-spinner"></i> Update cart</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    @foreach ($items as $item)
                        <form id="cart-remove-{{ $item['key'] }}" action="{{ route('malefashion.storefront-cart.destroy', $item['key']) }}" method="post" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endforeach
                </div>
                <div class="col-lg-4">
                    <div class="cart__discount">
                        <h6>Discount codes</h6>
                        <form action="#" method="post" onsubmit="return false;">
                            <input type="text" placeholder="Coupon code">
                            <button type="submit">Apply</button>
                        </form>
                    </div>
                    <div class="cart__total">
                        <h6>Cart total</h6>
                        <ul>
                            <li>Subtotal <span>${{ number_format($subtotal, 2) }}</span></li>
                            <li>Total <span>${{ number_format($total, 2) }}</span></li>
                        </ul>
                        <a href="{{ route('malefashion.checkout') }}" class="primary-btn">Proceed to checkout</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
