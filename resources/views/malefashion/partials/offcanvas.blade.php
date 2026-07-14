<div class="offcanvas-menu-overlay"></div>
<div class="offcanvas-menu-wrapper">
    <div class="offcanvas__option">
        <div class="offcanvas__links">
            <a href="{{ auth('account')->check() ? route('malefashion.account') : route('malefashion.account.login') }}">Sign in</a>
            <a href="#">FAQs</a>
        </div>
        <div class="offcanvas__top__hover">
            <span>Usd <i class="arrow_carrot-down"></i></span>
            <ul>
                <li>USD</li>
                <li>EUR</li>
                <li>USD</li>
            </ul>
        </div>
    </div>
    <div class="offcanvas__nav__option">
        <a href="#" class="search-switch">
            <img src="{{ asset('malefashion/img/icon/search.png') }}" alt="">
        </a>
        <a href="{{ auth('account')->check() ? route('malefashion.account') : route('malefashion.account.login') }}" class="header__account" aria-label="Account">
            <i class="fa fa-user-o" aria-hidden="true"></i>
        </a>
        <a href="{{ route('malefashion.wishlist') }}" class="header__wishlist" aria-label="Wishlist">
            <img src="{{ asset('malefashion/img/icon/heart.png') }}" alt="">
            <span class="header__wishlist-count" data-wishlist-count>{{ $wishlistCount ?? 0 }}</span>
        </a>
        <button type="button" class="header__cart" data-cart-open aria-label="Open cart">
            <img src="{{ asset('malefashion/img/icon/cart.png') }}" alt="">
            <span data-cart-count>{{ $cartCount ?? 0 }}</span>
        </button>
        <button type="button" class="header__compare" data-compare-open aria-label="Compare products">
            <img src="{{ asset('malefashion/img/icon/compare.png') }}" alt="">
            <span class="header__compare-count" data-compare-count>{{ $compareCount ?? 0 }}</span>
        </button>
    </div>
    <div id="mobile-menu-wrap"></div>
    <div class="offcanvas__text">
        <p>Free shipping, 30-day return or refund guarantee.</p>
    </div>
</div>
