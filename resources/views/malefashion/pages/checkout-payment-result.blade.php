@extends('layouts.malefashion-checkout')

@section('title', 'Payment — Ali Charisma')

@section('content')
<div class="checkout-shell">
    <div class="checkout">
        <div class="checkout__main">
            <div class="checkout__main-inner">
                <section class="checkout__section">
                    @if ($status === 'success')
                        <h2>Payment received</h2>
                        <p class="checkout__hint">Thank you. Your payment is being processed and your order is confirmed.</p>
                    @else
                        <h2>Payment pending</h2>
                        <p class="checkout__hint">Your payment was not completed yet. You can return to checkout to try again.</p>
                    @endif

                    @if ($orderUuid)
                        <p class="checkout__hint">Order reference: <strong>{{ $orderUuid }}</strong></p>
                    @endif

                    <div class="checkout__actions">
                        <a href="{{ route('malefashion.checkout') }}" class="checkout__back">Return to checkout</a>
                        <a href="{{ route('malefashion.account') }}" class="checkout__pay">View account</a>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
@endsection
