@extends('layouts.malefashion')

@section('title', 'Sign up — Ali Charisma')

@section('content')
<section class="account-auth">
    <div class="container">
        <div class="account-auth__panel">
            <h1 class="account-auth__title">Create account</h1>
            <p class="account-auth__intro">Enter your details to create an account:</p>

            @if ($errors->any())
                <div class="account-auth__errors" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="account-auth__form" action="{{ route('malefashion.account.register.store') }}" method="post">
                @csrf
                <div class="account-auth__field">
                    <label for="register-name">Name</label>
                    <input id="register-name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" required>
                </div>
                <div class="account-auth__field">
                    <label for="register-email">E-mail</label>
                    <input id="register-email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
                </div>
                <div class="account-auth__field">
                    <label for="register-password">Password</label>
                    <input id="register-password" type="password" name="password" autocomplete="new-password" required>
                </div>
                <div class="account-auth__field">
                    <label for="register-password-confirmation">Confirm password</label>
                    <input id="register-password-confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
                </div>
                <button type="submit" class="account-auth__submit">Sign up</button>
            </form>

            <p class="account-auth__switch">
                Already have an account?
                <a href="{{ route('malefashion.account.login') }}">Login</a>
            </p>
        </div>
    </div>
</section>
@endsection
