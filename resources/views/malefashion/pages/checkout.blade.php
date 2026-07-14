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
                    <strong data-checkout-grand-total>${{ number_format($total, 2) }}</strong>
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
                            <input id="checkout-phone" type="tel" name="phone" placeholder="Phone" autocomplete="tel" value="{{ old('phone', $customer['phone']) }}" required>
                        </div>
                    </section>

                    <section class="checkout__section">
                        <h2>Shipping method</h2>
                        <p class="checkout__hint" data-shipping-status>Enter your delivery address to calculate DHL Express rates.</p>
                        <div class="checkout__methods" data-shipping-methods>
                            <div class="checkout__method checkout__method--placeholder is-selected">
                                <span class="checkout__method-body">
                                    <span class="checkout__method-title">DHL Express</span>
                                    <span class="checkout__method-meta">Rates appear after address is complete</span>
                                </span>
                                <span class="checkout__method-price">—</span>
                            </div>
                        </div>
                        <input type="hidden" name="shipping_rate_id" value="" data-shipping-rate-id>
                        <input type="hidden" name="shipping_amount" value="0" data-shipping-amount>
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
        var ratesUrl = @json(route('malefashion.checkout.shipping-rates'));
        var csrf = @json(csrf_token());
        var subtotal = {{ json_encode($subtotal) }};
        var debounceTimer = null;
        var lastPayload = '';

        function money(amount) {
            return '$' + Number(amount).toFixed(2);
        }

        function setTotals(shipping) {
            var total = subtotal + Number(shipping || 0);
            $('[data-checkout-shipping]').text(shipping > 0 ? money(shipping) : 'Calculated at next step');
            $('[data-checkout-total]').html('<span class="checkout-summary__currency">USD</span> ' + money(total));
            $('[data-checkout-grand-total]').text(money(total));
            $('[data-shipping-amount]').val(Number(shipping || 0).toFixed(2));
        }

        function addressPayload() {
            return {
                first_name: $('#checkout-first-name').val(),
                last_name: $('#checkout-last-name').val(),
                phone: $('#checkout-phone').val(),
                address: $('#checkout-address').val(),
                apartment: $('#checkout-apartment').val(),
                city: $('#checkout-city').val(),
                province: $('#checkout-province').val(),
                postal: $('#checkout-postal').val(),
                country: $('#checkout-country').val(),
            };
        }

        function addressComplete(payload) {
            return Boolean(
                payload.first_name &&
                payload.last_name &&
                payload.phone &&
                payload.address &&
                payload.city &&
                payload.province &&
                payload.postal &&
                payload.country
            );
        }

        function renderRates(rates) {
            var $methods = $('[data-shipping-methods]').empty();

            if (!rates.length) {
                $('[data-shipping-status]').text('No DHL Express rates available for this address.');
                $methods.append(
                    '<div class="checkout__method checkout__method--placeholder is-selected">' +
                    '<span class="checkout__method-body"><span class="checkout__method-title">DHL Express</span>' +
                    '<span class="checkout__method-meta">Try a different address</span></span>' +
                    '<span class="checkout__method-price">—</span></div>'
                );
                $('[data-shipping-rate-id]').val('');
                setTotals(0);
                return;
            }

            $('[data-shipping-status]').text('Select a DHL Express service.');

            rates.forEach(function (rate, index) {
                var selected = index === 0 ? ' is-selected' : '';
                var checked = index === 0 ? ' checked' : '';
                $methods.append(
                    '<label class="checkout__method' + selected + '">' +
                    '<input type="radio" name="shipping_method" value="' + rate.rate_id + '"' + checked +
                    ' data-rate-amount="' + rate.amount + '" data-rate-id="' + rate.rate_id + '">' +
                    '<span class="checkout__method-body">' +
                    '<span class="checkout__method-title">' + (rate.service_type || rate.carrier_friendly_name) + '</span>' +
                    '<span class="checkout__method-meta">' + (rate.meta || 'Express delivery') + '</span>' +
                    '</span>' +
                    '<span class="checkout__method-price">' + money(rate.amount) + '</span>' +
                    '</label>'
                );
            });

            var first = rates[0];
            $('[data-shipping-rate-id]').val(first.rate_id);
            setTotals(first.amount);
        }

        function fetchRates() {
            var payload = addressPayload();

            if (!addressComplete(payload)) {
                $('[data-shipping-status]').text('Enter your delivery address to calculate DHL Express rates.');
                return;
            }

            var serialized = JSON.stringify(payload);
            if (serialized === lastPayload) {
                return;
            }
            lastPayload = serialized;

            $('[data-shipping-status]').text('Calculating DHL Express rates…');

            $.ajax({
                url: ratesUrl,
                method: 'POST',
                data: payload,
                headers: { 'X-CSRF-TOKEN': csrf },
            }).done(function (data) {
                renderRates(data.rates || []);
            }).fail(function (xhr) {
                lastPayload = '';
                var message = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Unable to calculate shipping right now.';
                $('[data-shipping-status]').text(message);
                $('[data-shipping-methods]').html(
                    '<div class="checkout__method checkout__method--placeholder is-selected">' +
                    '<span class="checkout__method-body"><span class="checkout__method-title">DHL Express</span>' +
                    '<span class="checkout__method-meta">Retry after fixing the address</span></span>' +
                    '<span class="checkout__method-price">—</span></div>'
                );
                $('[data-shipping-rate-id]').val('');
                setTotals(0);
            });
        }

        function scheduleRates() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchRates, 400);
        }

        $('[data-checkout-summary-toggle]').on('click', function () {
            var $panel = $('#checkout-summary-mobile');
            var open = !$panel.prop('hidden');
            $panel.prop('hidden', open);
            $(this).attr('aria-expanded', open ? 'false' : 'true');
            $('[data-checkout-summary-label]').text(open ? 'Show order summary' : 'Hide order summary');
            $(this).find('.fa').toggleClass('fa-chevron-down fa-chevron-up');
        });

        $(document).on('change', 'input[name="shipping_method"]', function () {
            $('.checkout__method').removeClass('is-selected');
            $(this).closest('.checkout__method').addClass('is-selected');
            $('[data-shipping-rate-id]').val($(this).data('rate-id'));
            setTotals($(this).data('rate-amount'));
        });

        $('#checkout-first-name, #checkout-last-name, #checkout-phone, #checkout-address, #checkout-apartment, #checkout-city, #checkout-province, #checkout-postal, #checkout-country')
            .on('input change', scheduleRates);

        if (addressComplete(addressPayload())) {
            fetchRates();
        }
    })(jQuery);
</script>
@endpush
