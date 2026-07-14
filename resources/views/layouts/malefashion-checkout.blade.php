<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Checkout — Ali Charisma">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Checkout — Ali Charisma')</title>

    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('malefashion/css/bootstrap.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('malefashion/css/font-awesome.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('malefashion/css/elegant-icons.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('malefashion/css/magnific-popup.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('malefashion/css/nice-select.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('malefashion/css/slicknav.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('malefashion/css/style.css') }}" type="text/css">

    @stack('styles')
</head>

<body class="checkout-page">
    @include('malefashion.partials.header-checkout')

    @yield('content')

    @include('malefashion.partials.cart-drawer')

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
            drawerUrl: @json(route('malefashion.storefront-cart.drawer')),
            syncUrl: @json(route('malefashion.storefront-cart.sync')),
            destroyUrlTemplate: @json(url('/storefront-cart')),
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
    <script src="{{ asset('malefashion/js/jquery.slicknav.js') }}"></script>
    <script src="{{ asset('malefashion/js/main.js') }}"></script>

    @stack('scripts')
</body>

</html>
