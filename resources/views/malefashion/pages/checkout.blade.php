@extends('layouts.malefashion-checkout')

@section('title', 'Checkout — Ali Charisma')

@section('content')
<div class="checkout-shell">
    <div class="checkout">
        <div class="checkout__main">
                <div class="checkout__main-inner">
                <button type="button" class="checkout__summary-toggle" data-checkout-summary-toggle aria-expanded="false">
                    <span class="checkout__summary-toggle-label">
                        <span data-checkout-summary-label>Show order summary</span>
                        <i class="fa fa-chevron-down" aria-hidden="true"></i>
                    </span>
                    <strong>${{ number_format($total, 2) }}</strong>
                </button>

                <aside class="checkout__summary checkout__summary--mobile" id="checkout-summary-mobile" hidden>
                    @include('malefashion.partials.checkout-summary', [
                        'lineItems' => $lineItems,
                        'subtotal' => $subtotal,
                        'shipping' => $shipping,
                        'total' => $total,
                    ])
                </aside>

                <form class="checkout__form" action="#" method="post" novalidate>
                    @csrf

                    <section class="checkout__section">
                        <div class="checkout__section-head">
                            <h2>Contact</h2>
                            @if ($account)
                                <span class="checkout__logged-in">{{ $account->email }}</span>
                            @else
                                <a href="{{ route('malefashion.account.login', ['redirect' => route('malefashion.checkout', absolute: false)]) }}">Log in</a>
                            @endif
                        </div>
                        <div class="checkout__field">
                            <label class="sr-only" for="checkout-email">Email</label>
                            <input id="checkout-email" type="email" name="email" placeholder="Email" autocomplete="email" value="{{ old('email', $customer['email']) }}" required>
                        </div>
                        <label class="checkout__check">
                            <input type="checkbox" name="newsletter" value="1" checked>
                            <span>Email me with news and offers</span>
                        </label>
                    </section>

                    <section class="checkout__section">
                        <h2>Delivery</h2>
                        <div class="checkout__field">
                            <label class="sr-only" for="checkout-country">Country / region</label>
                            <select id="checkout-country" name="country" required>
                                <option value="ID" @selected(old('country', $customer['country']) === 'ID')>Indonesia</option>
                                <option value="SG" @selected(old('country', $customer['country']) === 'SG')>Singapore</option>
                                <option value="MY" @selected(old('country', $customer['country']) === 'MY')>Malaysia</option>
                                <option value="US" @selected(old('country', $customer['country']) === 'US')>United States</option>
                                <option value="AU" @selected(old('country', $customer['country']) === 'AU')>Australia</option>
                            </select>
                        </div>
                        <div class="checkout__row checkout__row--2">
                            <div class="checkout__field">
                                <label class="sr-only" for="checkout-first-name">First name</label>
                                <input id="checkout-first-name" type="text" name="first_name" placeholder="First name" autocomplete="given-name" value="{{ old('first_name', $customer['first_name']) }}" required>
                            </div>
                            <div class="checkout__field">
                                <label class="sr-only" for="checkout-last-name">Last name</label>
                                <input id="checkout-last-name" type="text" name="last_name" placeholder="Last name" autocomplete="family-name" value="{{ old('last_name', $customer['last_name']) }}" required>
                            </div>
                        </div>
                        <div class="checkout__field">
                            <label class="sr-only" for="checkout-address">Address</label>
                            <input id="checkout-address" type="text" name="address" placeholder="Address" autocomplete="street-address" value="{{ old('address', $customer['address']) }}" required>
                        </div>
                        <div class="checkout__field">
                            <label class="sr-only" for="checkout-apartment">Apartment, suite, etc. (optional)</label>
                            <input id="checkout-apartment" type="text" name="apartment" placeholder="Apartment, suite, etc. (optional)" autocomplete="address-line2" value="{{ old('apartment', $customer['apartment']) }}">
                        </div>
                        <div class="checkout__row checkout__row--3">
                            <div class="checkout__field">
                                <label class="sr-only" for="checkout-city">City</label>
                                <input id="checkout-city" type="text" name="city" placeholder="City" autocomplete="address-level2" value="{{ old('city', $customer['city']) }}" required>
                            </div>
                            <div class="checkout__field">
                                <label class="sr-only" for="checkout-province">Province</label>
                                <input id="checkout-province" type="text" name="province" placeholder="Province" autocomplete="address-level1" value="{{ old('province', $customer['province']) }}" required>
                            </div>
                            <div class="checkout__field">
                                <label class="sr-only" for="checkout-postal">Postal code</label>
                                <input id="checkout-postal" type="text" name="postal" placeholder="Postal code" autocomplete="postal-code" value="{{ old('postal', $customer['postal']) }}" required>
                            </div>
                        </div>
                        <div class="checkout__field">
                            <label class="sr-only" for="checkout-phone">Phone</label>
                            <input id="checkout-phone" type="tel" name="phone" placeholder="Phone" autocomplete="tel" value="{{ old('phone', $customer['phone']) }}">
                        </div>
                    </section>

                    <section class="checkout__section">
                        <h2>Shipping method</h2>
                        <div class="checkout__methods">
                            <label class="checkout__method is-selected">
                                <input type="radio" name="shipping_method" value="standard" checked>
                                <span class="checkout__method-body">
                                    <span class="checkout__method-title">Standard Shipping</span>
                                    <span class="checkout__method-meta">5–10 business days</span>
                                </span>
                                <span class="checkout__method-price">Free</span>
                            </label>
                            <label class="checkout__method">
                                <input type="radio" name="shipping_method" value="express">
                                <span class="checkout__method-body">
                                    <span class="checkout__method-title">Express Shipping</span>
                                    <span class="checkout__method-meta">2–4 business days</span>
                                </span>
                                <span class="checkout__method-price">$12.00</span>
                            </label>
                        </div>
                    </section>

                    <section class="checkout__section">
                        <h2>Payment</h2>
                        <p class="checkout__hint">All transactions are secure and encrypted.</p>
                        <div class="checkout__card-box">
                            <div class="checkout__field">
                                <label class="sr-only" for="checkout-card-number">Card number</label>
                                <input id="checkout-card-number" type="text" name="card_number" placeholder="Card number" inputmode="numeric" autocomplete="cc-number">
                            </div>
                            <div class="checkout__row checkout__row--2">
                                <div class="checkout__field">
                                    <label class="sr-only" for="checkout-card-exp">Expiration date (MM / YY)</label>
                                    <input id="checkout-card-exp" type="text" name="card_exp" placeholder="Expiration date (MM / YY)" autocomplete="cc-exp">
                                </div>
                                <div class="checkout__field">
                                    <label class="sr-only" for="checkout-card-cvc">Security code</label>
                                    <input id="checkout-card-cvc" type="text" name="card_cvc" placeholder="Security code" inputmode="numeric" autocomplete="cc-csc">
                                </div>
                            </div>
                            <div class="checkout__field">
                                <label class="sr-only" for="checkout-card-name">Name on card</label>
                                <input id="checkout-card-name" type="text" name="card_name" placeholder="Name on card" autocomplete="cc-name">
                            </div>
                        </div>
                        <label class="checkout__check">
                            <input type="checkbox" name="billing_same" value="1" checked>
                            <span>Use shipping address as billing address</span>
                        </label>
                    </section>

                    <div class="checkout__actions">
                        <a href="{{ route('malefashion.cart') }}" class="checkout__back">‹ Return to cart</a>
                        <button type="submit" class="checkout__pay">Pay now</button>
                    </div>
                </form>

                <footer class="checkout__footer">
                    <a href="#">Privacy policy</a>
                    <a href="#">Terms of service</a>
                    <a href="{{ route('malefashion.contact') }}">Contact</a>
                </footer>
                </div>
        </div>

        <aside class="checkout__summary checkout__summary--desktop" aria-label="Order summary">
            <div class="checkout__summary-inner">
                @include('malefashion.partials.checkout-summary', [
                    'lineItems' => $lineItems,
                    'subtotal' => $subtotal,
                    'shipping' => $shipping,
                    'total' => $total,
                ])
            </div>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function ($) {
        $('[data-checkout-summary-toggle]').on('click', function () {
            var $panel = $('#checkout-summary-mobile');
            var open = !$panel.prop('hidden');
            $panel.prop('hidden', open);
            $(this).attr('aria-expanded', open ? 'false' : 'true');
            $('[data-checkout-summary-label]').text(open ? 'Show order summary' : 'Hide order summary');
            $(this).find('.fa').toggleClass('fa-chevron-down fa-chevron-up');
        });

        $('input[name="shipping_method"]').on('change', function () {
            $('.checkout__method').removeClass('is-selected');
            $(this).closest('.checkout__method').addClass('is-selected');
        });
    })(jQuery);
</script>
@endpush
