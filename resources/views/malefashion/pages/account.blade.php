@extends('layouts.malefashion')

@section('title', 'Your account — Ali Charisma')

@section('content')
<section class="account-page">
    <div class="container">
        <form action="{{ route('malefashion.account.logout') }}" method="post" class="d-inline">
            @csrf
            <button type="submit" class="account-page__back">&lt; Logout</button>
        </form>

        <div class="row account-page__grid">
            <div class="col-lg-7">
                <h1 class="account-page__title">Your account</h1>
                <p class="account-page__intro">
                    @if ($account)
                        Signed in as {{ $account->name }} ({{ $account->email }}).
                    @endif
                    View all your orders and manage your account information.
                </p>

                <div class="account-page__section">
                    <h2 class="account-page__heading">Orders</h2>
                    <p class="account-page__empty">You haven't placed any orders yet.</p>
                    <a href="{{ route('malefashion.shop') }}" class="account-page__btn">Continue shopping</a>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="account-page__section">
                    <h2 class="account-page__heading">Primary address</h2>
                    <div class="account-page__address">
                        @if ($primaryAddress)
                            @include('malefashion.partials.account-address-lines', ['address' => $primaryAddress])
                        @else
                            <p>No address saved.</p>
                        @endif
                    </div>
                    <a href="{{ route('malefashion.account.addresses') }}" class="account-page__btn">Manage</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
