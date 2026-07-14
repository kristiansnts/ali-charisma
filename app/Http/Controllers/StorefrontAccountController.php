<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Team;
use App\Support\AccountUserLinker;
use App\Support\CustomerAddressList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class StorefrontAccountController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        if (Auth::guard('account')->check()) {
            return $this->redirectAfterAccountAuth(Auth::guard('account')->user());
        }

        self::rememberIntendedRedirect($request);

        return view('malefashion.pages.account-login');
    }

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::guard('account')->check()) {
            return $this->redirectAfterAccountAuth(Auth::guard('account')->user());
        }

        return view('malefashion.pages.account-register');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('account')->attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'is_active' => true,
            'is_login' => true,
        ], $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $request->session()->regenerate();

        /** @var Account $account */
        $account = Auth::guard('account')->user();

        return $this->redirectAfterAccountAuth($account);
    }

    public function register(Request $request, AccountUserLinker $linker): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:accounts,email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $account = $linker->registerCustomer(
            $validated['name'],
            $validated['email'],
            $validated['password'],
        );

        Auth::guard('account')->login($account);
        $request->session()->regenerate();

        return redirect()
            ->route('malefashion.account')
            ->with('status', 'Welcome! Your account is ready.');
    }

    public function index(): View
    {
        return view('malefashion.pages.account', [
            'account' => Auth::guard('account')->user(),
            'primaryAddress' => CustomerAddressList::default(),
        ]);
    }

    public function addresses(): View
    {
        return view('malefashion.pages.account-addresses', [
            'account' => Auth::guard('account')->user(),
            'addresses' => CustomerAddressList::items(),
        ]);
    }

    public function storeAddress(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id' => ['nullable', 'string', 'max:64'],
            'first_name' => ['nullable', 'string', 'max:120'],
            'last_name' => ['nullable', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'address1' => ['nullable', 'string', 'max:255'],
            'address2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'zip' => ['nullable', 'string', 'max:32'],
            'country' => ['required', 'string', 'max:120'],
            'province' => ['nullable', 'string', 'max:120'],
            'default' => ['nullable', 'boolean'],
        ]);

        CustomerAddressList::store([
            ...$validated,
            'default' => $request->boolean('default'),
        ], $validated['id'] ?? null);

        return redirect()
            ->route('malefashion.account.addresses')
            ->with('status', 'Address saved.');
    }

    public function destroyAddress(string $id): RedirectResponse
    {
        CustomerAddressList::remove($id);

        return redirect()
            ->route('malefashion.account.addresses')
            ->with('status', 'Address deleted.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('account')->logout();
        Auth::guard('web')->logout();
        $request->session()->regenerateToken();

        return redirect()->route('malefashion.home');
    }

    private function redirectAfterAccountAuth(Account $account): RedirectResponse
    {
        if ($account->isAdmin() && $account->user) {
            Auth::guard('web')->login($account->user, true);

            $team = $account->team
                ?? $account->user->teams()->orderBy('teams.id')->first()
                ?? Team::query()->orderBy('id')->first();

            if ($team !== null) {
                return redirect()->intended(url('/admin/'.$team->slug));
            }

            return redirect()->intended(url('/admin'));
        }

        return redirect()->intended(route('malefashion.account'));
    }

    private static function rememberIntendedRedirect(Request $request): void
    {
        $redirect = $request->query('redirect');

        if (! is_string($redirect) || ! str_starts_with($redirect, '/') || str_starts_with($redirect, '//')) {
            return;
        }

        $request->session()->put('url.intended', $redirect);
    }
}
