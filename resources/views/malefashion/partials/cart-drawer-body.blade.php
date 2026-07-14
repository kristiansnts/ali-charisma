@php
    $items = $items ?? [];
    $total = $total ?? '$0.00';
@endphp

@if ($items === [])
    <div class="cart-drawer__empty">
        <p>Your cart is empty.</p>
        <a href="{{ route('malefashion.shop') }}" class="cart-drawer__continue" data-cart-close>Continue shopping</a>
    </div>
@else
    <ul class="cart-drawer__items">
        @foreach ($items as $item)
            <li class="cart-drawer__item" data-cart-item="{{ $item['key'] }}">
                <a href="{{ route('malefashion.shop-details') }}" class="cart-drawer__item-pic">
                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                </a>
                <div class="cart-drawer__item-info">
                    <a href="{{ route('malefashion.shop-details') }}" class="cart-drawer__item-name">{{ $item['name'] }}</a>
                    <div class="cart-drawer__item-price">{{ $item['price_label'] }}</div>
                    <div class="cart-drawer__item-actions">
                        <div class="cart-drawer__qty" role="group" aria-label="Quantity for {{ $item['name'] }}">
                            <button
                                type="button"
                                class="cart-drawer__qty-btn"
                                data-cart-qty-change
                                data-cart-key="{{ $item['key'] }}"
                                data-cart-qty="{{ max(0, $item['qty'] - 1) }}"
                                aria-label="Decrease quantity"
                            >−</button>
                            <span class="cart-drawer__qty-value">{{ $item['qty'] }}</span>
                            <button
                                type="button"
                                class="cart-drawer__qty-btn"
                                data-cart-qty-change
                                data-cart-key="{{ $item['key'] }}"
                                data-cart-qty="{{ min(99, $item['qty'] + 1) }}"
                                aria-label="Increase quantity"
                            >+</button>
                        </div>
                        <button
                            type="button"
                            class="cart-drawer__remove"
                            data-cart-remove
                            data-cart-key="{{ $item['key'] }}"
                        >Remove</button>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>

    <div class="cart-drawer__footer">
        <button type="button" class="cart-drawer__note-toggle" data-cart-note-toggle>Add order note</button>
        <div class="cart-drawer__note" hidden>
            <label for="cart-drawer-note" class="sr-only">Order note</label>
            <textarea id="cart-drawer-note" name="order_note" rows="3" placeholder="Special instructions for your order"></textarea>
        </div>
        <p class="cart-drawer__disclaimer">Taxes and shipping calculated at checkout</p>
        <a href="{{ route('malefashion.checkout') }}" class="cart-drawer__checkout">
            <span>Checkout</span>
            <span class="cart-drawer__checkout-total" data-cart-drawer-total>{{ $total }}</span>
        </a>
    </div>
@endif
