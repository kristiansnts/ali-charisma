<div class="offcanvas-menu-overlay"></div>
<div class="offcanvas-menu-wrapper">
    <div class="offcanvas__option">
        <div class="offcanvas__links">
            <a href="#">Sign in</a>
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
        <a href="{{ route('malefashion.wishlist') }}" class="header__wishlist" aria-label="Wishlist">
            <img src="{{ asset('malefashion/img/icon/heart.png') }}" alt="">
            <span class="header__wishlist-count" data-wishlist-count>{{ $wishlistCount ?? 0 }}</span>
        </a>
        <a href="{{ route('malefashion.cart') }}">
            <img src="{{ asset('malefashion/img/icon/cart.png') }}" alt="">
            <span data-cart-count>{{ $cartCount ?? 0 }}</span>
        </a>
        <div class="price" data-cart-total>{{ $cartTotal ?? '$0.00' }}</div>
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
