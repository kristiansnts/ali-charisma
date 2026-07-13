@extends('layouts.malefashion')

@section('title', 'Our Work — Ali Charisma')

@section('content')
@php
    $workItems = [
        [
            'image' => 'malefashion/img/work/ali_charisa_indonesian_fashion.png',
            'eyebrow' => 'TEXT LINE #1',
            'title' => 'Text line #2',
        ],
        [
            'image' => 'malefashion/img/work/ali_charisma_1.jpg',
            'eyebrow' => 'VINTAGE',
            'title' => 'Lorem ipsum dolor sit amet',
        ],
        [
            'image' => 'malefashion/img/work/farhan_alif_dafa_kanan_desainer_asal_malang_berusia_19_tahun.png',
            'eyebrow' => 'SUMMER',
            'title' => 'Lorem ipsum dolor sit amet',
        ],
        [
            'image' => 'malefashion/img/work/people_surrounding.jpeg',
            'eyebrow' => 'BEACHWEAR',
            'title' => 'Lorem ipsum dolor sit amet',
        ],
        [
            'image' => 'malefashion/img/work/speech_man.jpeg',
            'eyebrow' => 'SUNGLASSES',
            'title' => 'Lorem ipsum dolor sit amet',
        ],
        [
            'image' => 'malefashion/img/work/ali_charisma_model_comp.jpeg',
            'eyebrow' => 'WINTER',
            'title' => 'Lorem ipsum dolor sit amet',
        ],
        [
            'image' => 'malefashion/img/work/whiteshirt_man.jpg',
            'eyebrow' => 'SHORTS',
            'title' => 'Lorem ipsum dolor sit amet',
        ],
    ];
@endphp

<!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h4>Our Work</h4>
                        <div class="breadcrumb__links">
                            <a href="{{ route('malefashion.home') }}">Home</a>
                            <span>Work</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Work Gallery Begin -->
    <section class="work spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="section-title">
                        <h2>Our Work</h2>
                    </div>
                </div>
            </div>
            <div class="work__gallery">
                @foreach ($workItems as $item)
                    <a href="{{ asset($item['image']) }}" class="work__item">
                        <img src="{{ asset($item['image']) }}" alt="{{ $item['eyebrow'] }}">
                        <div class="work__overlay">
                            <div class="work__overlay__text">
                                <h6>{{ $item['eyebrow'] }}</h6>
                                <h4>{{ $item['title'] }}</h4>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    <!-- Work Gallery End -->
@endsection
