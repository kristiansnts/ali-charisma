<div class="checkout-summary">
    <ul class="checkout-summary__items">
        @foreach ($lineItems as $item)
            <li class="checkout-summary__item">
                <div class="checkout-summary__thumb">
                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                    <span class="checkout-summary__qty">{{ $item['qty'] }}</span>
                </div>
                <div class="checkout-summary__info">
                    <p class="checkout-summary__name">{{ $item['name'] }}</p>
                    @if (! empty($item['variant']))
                        <p class="checkout-summary__variant">{{ $item['variant'] }}</p>
                    @endif
                </div>
                <div class="checkout-summary__price">${{ number_format($item['price'] * $item['qty'], 2) }}</div>
            </li>
        @endforeach
    </ul>

    <form class="checkout-summary__discount" action="#" method="post" onsubmit="return false;">
        <label class="sr-only" for="checkout-discount">Discount code</label>
        <input id="checkout-discount" type="text" name="discount" placeholder="Discount code">
        <button type="submit">Apply</button>
    </form>

    <dl class="checkout-summary__totals">
        <div>
            <dt>Subtotal</dt>
            <dd>${{ number_format($subtotal, 2) }}</dd>
        </div>
        <div>
            <dt>Shipping</dt>
            <dd>{{ $shipping > 0 ? '$'.number_format($shipping, 2) : 'Free' }}</dd>
        </div>
        <div class="checkout-summary__total">
            <dt>Total</dt>
            <dd><span class="checkout-summary__currency">USD</span> ${{ number_format($total, 2) }}</dd>
        </div>
    </dl>
</div>
