<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="{{ $metaDescription ?? 'Ali Charisma' }}">
    <meta name="keywords" content="{{ $metaKeywords ?? 'Ali Charisma, fashion, ecommerce' }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', config('app.name', 'Ali Charisma'))</title>

    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('malefashion/css/bootstrap.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('malefashion/css/font-awesome.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('malefashion/css/elegant-icons.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('malefashion/css/magnific-popup.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('malefashion/css/nice-select.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('malefashion/css/owl.carousel.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('malefashion/css/slicknav.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('malefashion/css/style.css') }}" type="text/css">

    @stack('styles')
</head>

<body>
    <div id="preloder">
        <div class="loader"></div>
    </div>

    @include('malefashion.partials.offcanvas')
    @include('malefashion.partials.header')

    @yield('content')

    @include('malefashion.partials.footer')
    @include('malefashion.partials.search')
    @include('malefashion.partials.compare-modal')
    @include('malefashion.partials.cart-upsell-modal')

    <script>
        window.malefashionCompare = {
            indexUrl: @json(route('malefashion.compare.index')),
            storeUrlTemplate: @json(url('/compare')),
            clearUrl: @json(route('malefashion.compare.clear')),
            csrfToken: @json(csrf_token()),
        };
        window.malefashionWishlist = {
            storeUrl: @json(route('malefashion.wishlist.store')),
            destroyUrlTemplate: @json(url('/wishlist')),
            csrfToken: @json(csrf_token()),
        };
        window.malefashionCart = {
            storeUrl: @json(route('malefashion.storefront-cart.store')),
            csrfToken: @json(csrf_token()),
        };
        window.malefashionSearch = {
            predictiveUrl: @json(route('malefashion.search.predictive')),
        };
    </script>

    <script src="{{ asset('malefashion/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('malefashion/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('malefashion/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('malefashion/js/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('malefashion/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('malefashion/js/jquery.countdown.min.js') }}"></script>
    <script src="{{ asset('malefashion/js/jquery.slicknav.js') }}"></script>
    <script src="{{ asset('malefashion/js/mixitup.min.js') }}"></script>
    <script src="{{ asset('malefashion/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('malefashion/js/main.js') }}"></script>

    @stack('scripts')
</body>

</html>
