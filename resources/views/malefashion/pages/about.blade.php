@extends('layouts.malefashion')

@section('title', 'About Us — Ali Charisma')

@section('content')
<!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h4>About Us</h4>
                        <div class="breadcrumb__links">
                            <a href="{{ route('malefashion.home') }}">Home</a>
                            <span>About Us</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- About Section Begin -->
    <section class="about spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="about__pic">
                        <img src="{{ asset('malefashion/img/about/owner_charisma.jpg') }}" alt="Owner Charisma">
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8 text-center">
                    <div class="about__item">
                        <h4>Our Story</h4>
                        <p>A great About Us page helps builds trust between you and your customers. The more content you
                            provide about you and your business, the more confident people will be when purchasing from
                            your store.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="about__pic">
                        <img src="{{ asset('malefashion/img/about/anne_gutenberg.jpg') }}" alt="Anne Gutenberg">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- About Section End -->
@endsection
