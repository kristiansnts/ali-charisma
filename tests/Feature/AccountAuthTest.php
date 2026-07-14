<?php

use App\Enums\AccountType;
use App\Models\Account;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Support\AccountUserLinker;
use Database\Seeders\TenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

it('registers a customer into the accounts table and logs them in', function () {
    Team::query()->create(['name' => 'Ali Charisma', 'slug' => 'ali-charisma']);

    $this->post(route('malefashion.account.register.store'), [
        'name' => 'Nadia Customer',
        'email' => 'nadia@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('malefashion.account'));

    $account = Account::query()->where('email', 'nadia@example.com')->first();

    expect($account)->not->toBeNull()
        ->and($account->type)->toBe(AccountType::Customer)
        ->and($account->user_id)->toBeNull()
        ->and($account->is_active)->toBeTrue()
        ->and(Hash::check('password', $account->password))->toBeTrue();

    $this->assertAuthenticatedAs($account, 'account');
});

it('logs customers in through the account guard', function () {
    $account = Account::factory()->create([
        'email' => 'login@example.com',
        'password' => 'password',
    ]);

    $this->post(route('malefashion.account.login.store'), [
        'email' => 'login@example.com',
        'password' => 'password',
    ])->assertRedirect(route('malefashion.account'));

    $this->assertAuthenticatedAs($account, 'account');
});

it('redirects admin and superadmin storefront logins to the filament dashboard', function () {
    $this->seed(TenantSeeder::class);
    app(AccountUserLinker::class)->syncAllUsers();

    $team = Team::query()->where('slug', 'ali-charisma')->firstOrFail();
    $superAccount = Account::query()
        ->where('type', AccountType::SuperAdmin)
        ->where('email', 'admin@example.com')
        ->firstOrFail();

    $this->post(route('malefashion.account.login.store'), [
        'email' => 'admin@example.com',
        'password' => 'password',
    ])->assertRedirect(url('/admin/'.$team->slug));

    $this->assertAuthenticatedAs($superAccount, 'account');
    $this->assertAuthenticatedAs($superAccount->user, 'web');

    setPermissionsTeamId($team->id);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $adminRole = Role::query()->create([
        'name' => 'admin',
        'guard_name' => 'web',
        'team_id' => $team->id,
    ]);
    $admin = User::factory()->create([
        'name' => 'Tenant Admin',
        'email' => 'tenant-admin@example.com',
        'password' => 'password',
    ]);
    $admin->teams()->attach($team);
    $admin->assignRole($adminRole);
    $adminAccount = app(AccountUserLinker::class)->syncUser($admin);

    Auth::guard('account')->logout();
    Auth::guard('web')->logout();

    $this->post(route('malefashion.account.login.store'), [
        'email' => 'tenant-admin@example.com',
        'password' => 'password',
    ])->assertRedirect(url('/admin/'.$team->slug));

    $this->assertAuthenticatedAs($adminAccount->fresh(), 'account');
    $this->assertAuthenticatedAs($admin, 'web');
});

it('syncs current admin and super admin users onto linked accounts', function () {
    $this->seed(TenantSeeder::class);

    $team = Team::query()->where('slug', 'ali-charisma')->firstOrFail();
    setPermissionsTeamId($team->id);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $adminRole = Role::query()->create([
        'name' => 'admin',
        'guard_name' => 'web',
        'team_id' => $team->id,
    ]);

    $admin = User::factory()->create([
        'name' => 'Tenant Admin',
        'email' => 'tenant-admin@example.com',
        'password' => 'password',
    ]);
    $admin->teams()->attach($team);
    $admin->assignRole($adminRole);

    $superAdmin = User::query()->where('email', 'admin@example.com')->firstOrFail();

    app(AccountUserLinker::class)->syncAllUsers();

    $adminAccount = Account::query()->where('user_id', $admin->id)->first();
    $superAccount = Account::query()->where('user_id', $superAdmin->id)->first();

    expect($adminAccount)->not->toBeNull()
        ->and($adminAccount->type)->toBe(AccountType::Admin)
        ->and($adminAccount->email)->toBe('tenant-admin@example.com')
        ->and($superAccount)->not->toBeNull()
        ->and($superAccount->type)->toBe(AccountType::SuperAdmin)
        ->and($superAdmin->account)->not->toBeNull();
});

it('redirects guests away from the account page to login', function () {
    $this->get(route('malefashion.account'))
        ->assertRedirect(route('malefashion.account.login'));
});

it('logs out admin accounts from both guards and redirects home', function () {
    $this->seed(TenantSeeder::class);
    app(AccountUserLinker::class)->syncAllUsers();

    $account = Account::query()
        ->where('type', AccountType::SuperAdmin)
        ->where('email', 'admin@example.com')
        ->firstOrFail();

    $this->actingAs($account, 'account')
        ->actingAs($account->user, 'web')
        ->post(route('malefashion.account.logout'))
        ->assertRedirect(route('malefashion.home'));

    $this->assertGuest('account');
    $this->assertGuest('web');
});
