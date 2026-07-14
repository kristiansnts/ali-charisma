@extends('layouts.malefashion')

@section('title', 'Login — Ali Charisma')

@section('content')
<section class="account-auth">
    <div class="container">
        <div class="account-auth__panel" data-account-panel="login">
            <h1 class="account-auth__title">Login</h1>
            <p class="account-auth__intro">Enter your email and password to login:</p>

            @if ($errors->any())
                <div class="account-auth__errors" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form class="account-auth__form" action="{{ route('malefashion.account.login.store') }}" method="post">
                @csrf
                <div class="account-auth__field">
                    <label for="login-email">E-mail</label>
                    <input id="login-email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
                </div>
                <div class="account-auth__field">
                    <label for="login-password">Password</label>
                    <input id="login-password" type="password" name="password" autocomplete="current-password" required>
                </div>
                <p class="account-auth__help">
                    <button type="button" class="account-auth__link" data-account-show="recover">Forgot your password?</button>
                </p>
                <button type="submit" class="account-auth__submit">Login</button>
            </form>

            <p class="account-auth__switch">
                Don't have an account?
                <a href="{{ route('malefashion.account.register') }}">Sign up</a>
            </p>
        </div>

        <div class="account-auth__panel" data-account-panel="recover" hidden>
            <h1 class="account-auth__title">Recover password</h1>
            <p class="account-auth__intro">Enter your email to recover your password:</p>

            <form class="account-auth__form" action="#" method="post" onsubmit="return false;">
                @csrf
                <div class="account-auth__field">
                    <label for="recover-email">E-mail</label>
                    <input id="recover-email" type="email" name="email" autocomplete="email" required>
                </div>
                <button type="submit" class="account-auth__submit">Recover</button>
            </form>

            <p class="account-auth__switch">
                Remember your password?
                <button type="button" class="account-auth__link" data-account-show="login">Back to login</button>
            </p>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
(function ($) {
    $(document).on('click', '[data-account-show]', function () {
        var panel = $(this).data('account-show');
        $('[data-account-panel]').prop('hidden', true);
        $('[data-account-panel="' + panel + '"]').prop('hidden', false);
    });
})(jQuery);
</script>
@endpush
