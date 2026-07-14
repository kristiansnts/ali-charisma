<div class="announcement-bar" role="region" aria-label="Promotions">
    <p class="announcement-bar__text">FINAL CLEARANCE: Take 20% off ‘Sale Must-Haves’</p>
</div>
<header class="header">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-3">
                <div class="header__logo">
                    <a href="{{ route('malefashion.home') }}">
                        <img src="{{ asset('malefashion/img/about/logo_alicharisma.png') }}" alt="Ali Charisma">
                    </a>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <nav class="header__menu mobile-menu">
                    <ul>
                        <li class="{{ request()->routeIs('malefashion.home') ? 'active' : '' }}">
                            <a href="{{ route('malefashion.home') }}">Home</a>
                        </li>
                        <li class="{{ request()->routeIs('malefashion.about') ? 'active' : '' }}">
                            <a href="{{ route('malefashion.about') }}">About</a>
                        </li>
                        <li class="{{ request()->routeIs('malefashion.work') ? 'active' : '' }}">
                            <a href="{{ route('malefashion.work') }}">Work</a>
                        </li>
                        <li class="{{ request()->routeIs('malefashion.contact') ? 'active' : '' }}">
                            <a href="{{ route('malefashion.contact') }}">Contacts</a>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="col-lg-3 col-md-3">
                <div class="header__nav__option">
                    <a href="#" class="search-switch">
                        <img src="{{ asset('malefashion/img/icon/search.png') }}" alt="">
                    </a>
                    <a href="{{ route('malefashion.wishlist') }}" class="header__wishlist" aria-label="Wishlist">
                        <img src="{{ asset('malefashion/img/icon/heart.png') }}" alt="">
                        <span class="header__wishlist-count" data-wishlist-count>{{ $wishlistCount ?? 0 }}</span>
                    </a>
                    <button type="button" class="header__cart" data-cart-open aria-label="Open cart">
                        <img src="{{ asset('malefashion/img/icon/cart.png') }}" alt="">
                        <span data-cart-count>{{ $cartCount ?? 0 }}</span>
                    </button>
                    <div class="price" data-cart-total>{{ $cartTotal ?? '$0.00' }}</div>
                    <button type="button" class="header__compare" id="compare-trigger" data-compare-open aria-label="Compare products">
                        <img src="{{ asset('malefashion/img/icon/compare.png') }}" alt="">
                        <span class="header__compare-count" data-compare-count>{{ $compareCount ?? 0 }}</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="canvas__open"><i class="fa fa-bars"></i></div>
    </div>
</header>
