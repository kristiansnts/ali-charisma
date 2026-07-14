<div class="announcement-bar" role="region" aria-label="Promotions">
    <p class="announcement-bar__text">FINAL CLEARANCE: Take 20% off ‘Sale Must-Haves’</p>
</div>
<header class="header header--checkout">
    <div class="header--checkout__inner">
        <div class="header__logo">
            <a href="{{ route('malefashion.home') }}">
                <img src="{{ asset('malefashion/img/about/logo_alicharisma.png') }}" alt="Ali Charisma">
            </a>
        </div>

        <div class="header--checkout__actions">
            <a href="{{ auth('account')->check() ? route('malefashion.account') : route('malefashion.account.login', ['redirect' => route('malefashion.checkout', absolute: false)]) }}" class="header__account" aria-label="Account">
                <i class="fa fa-user-o" aria-hidden="true"></i>
            </a>
            <a href="{{ route('malefashion.cart') }}" class="header--checkout__cart" aria-label="Return to cart">
                <span class="header--checkout__cart-icon">
                    <img src="{{ asset('malefashion/img/icon/cart.png') }}" alt="">
                    <span data-cart-count>{{ $cartCount ?? 0 }}</span>
                </span>
                <span class="header--checkout__cart-total" data-cart-total>{{ $cartTotal ?? '$0.00' }}</span>
            </a>
        </div>
    </div>
</header>
