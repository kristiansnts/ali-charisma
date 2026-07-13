<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="footer__about">
                    <div class="footer__logo">
                        <a href="{{ route('malefashion.home') }}">
                            <img src="{{ asset('malefashion/img/about/logo_alicharisma.png') }}" alt="Ali Charisma">
                        </a>
                    </div>
                    <a href="#"><img src="{{ asset('malefashion/img/payment.png') }}" alt=""></a>
                </div>
            </div>
            <div class="col-lg-2 offset-lg-1 col-md-3 col-sm-6">
                <div class="footer__widget">
                    <h6>Shop</h6>
                    <ul>
                        <li><a href="{{ route('malefashion.shop') }}">New In</a></li>
                        <li><a href="{{ route('malefashion.shop') }}">Sale & Special Offers</a></li>
                        <li><a href="{{ route('malefashion.shop') }}">Women's</a></li>
                        <li><a href="{{ route('malefashion.shop') }}">Men's</a></li>
                        <li><a href="{{ route('malefashion.shop') }}">Shoes</a></li>
                        <li><a href="{{ route('malefashion.shop') }}">Bags & Accessories</a></li>
                        <li><a href="{{ route('malefashion.shop') }}">Top Brands</a></li>
                        <li><a href="{{ route('malefashion.shop') }}">Lookbook</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-6">
                <div class="footer__widget">
                    <h6>Information</h6>
                    <ul>
                        <li><a href="{{ route('malefashion.about') }}">About Us</a></li>
                        <li><a href="{{ route('malefashion.contact') }}">Customer Service</a></li>
                        <li><a href="{{ route('malefashion.blog') }}">Blog</a></li>
                        <li><a href="#">Sizing Guide</a></li>
                        <li><a href="#">FAQs</a></li>
                        <li><a href="{{ route('malefashion.contact') }}">Contact Us</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 offset-lg-1 col-md-6 col-sm-6">
                <div class="footer__widget">
                    <h6>Order</h6>
                    <ul>
                        <li><a href="#">My Account</a></li>
                        <li><a href="{{ route('malefashion.cart') }}">View Bag</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="footer__copyright__text">
                    <p>
                        © {{ date('Y') }} Fashion Store Ali Charisma. All Rights Reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>
