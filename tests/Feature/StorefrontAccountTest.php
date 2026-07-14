<?php

use App\Models\Account;
use App\Models\AccountAddress;
use App\Support\CustomerAddressList;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the customer account page with primary address and manage link', function () {
    $account = Account::factory()->create([
        'name' => 'Nadia Customer',
        'email' => 'nadia@example.com',
    ]);

    $this->actingAs($account, 'account')
        ->get(route('malefashion.account'))
        ->assertSuccessful()
        ->assertSee('Your account', false)
        ->assertSee('Nadia Customer', false)
        ->assertSee('Orders', false)
        ->assertSee("You haven't placed any orders yet.", false)
        ->assertSee('Primary address', false)
        ->assertSee('Indonesia', false)
        ->assertSee('Manage', false)
        ->assertSee(route('malefashion.account.addresses', absolute: false), false)
        ->assertSee('Logout', false);
});

it('renders the addresses page with add address modal', function () {
    $account = Account::factory()->create();

    $this->actingAs($account, 'account')
        ->get(route('malefashion.account.addresses'))
        ->assertSuccessful()
        ->assertSee('Addresses', false)
        ->assertSee('Add address', false)
        ->assertSee('Default address', false)
        ->assertSee('customer-address-new', false)
        ->assertSee('Save address', false)
        ->assertSee('First name', false)
        ->assertSee('Set as default address', false)
        ->assertSee('Back to account', false);
});

it('saves and deletes customer addresses in the database', function () {
    $account = Account::factory()->create();

    $this->actingAs($account, 'account')
        ->post(route('malefashion.account.addresses.store'), [
            'first_name' => 'Ali',
            'last_name' => 'Charisma',
            'phone' => '08123456789',
            'address1' => 'Jl. Melati 1',
            'city' => 'Malang',
            'zip' => '65141',
            'country' => 'Indonesia',
            'province' => 'Jawa Timur',
            'default' => '1',
        ])
        ->assertRedirect(route('malefashion.account.addresses'));

    $this->assertDatabaseHas('account_addresses', [
        'account_id' => $account->id,
        'first_name' => 'Ali',
        'city' => 'Malang',
        'province' => 'Jawa Timur',
        'is_default' => true,
    ]);

    $default = CustomerAddressList::default();
    expect($default)->not->toBeNull()
        ->and($default['first_name'])->toBe('Ali')
        ->and($default['city'])->toBe('Malang')
        ->and($default['province'])->toBe('Jawa Timur');

    $this->actingAs($account, 'account')
        ->get(route('malefashion.account.addresses'))
        ->assertSuccessful()
        ->assertSee('Ali Charisma', false)
        ->assertSee('Jl. Melati 1', false);

    $this->actingAs($account, 'account')
        ->delete(route('malefashion.account.addresses.destroy', $default['id']))
        ->assertRedirect(route('malefashion.account.addresses'));

    expect(AccountAddress::query()->where('account_id', $account->id)->count())->toBe(0)
        ->and(CustomerAddressList::default()['country'])->toBe('Indonesia');
});

it('keeps customer addresses after a new session for the same account', function () {
    $account = Account::factory()->create();

    AccountAddress::factory()->for($account)->default()->create([
        'first_name' => 'Nadia',
        'last_name' => 'Customer',
        'address1' => 'Jl. Merdeka 10',
        'city' => 'Surabaya',
        'province' => 'Jawa Timur',
    ]);

    $this->actingAs($account, 'account')
        ->get(route('malefashion.account.addresses'))
        ->assertSuccessful()
        ->assertSee('Nadia Customer', false)
        ->assertSee('Jl. Merdeka 10', false)
        ->assertSee('Surabaya', false);
});

it('links the header account icon to the login page for guests', function () {
    $this->get('/')
        ->assertSuccessful()
        ->assertSee(route('malefashion.account.login', absolute: false), false)
        ->assertSee('header__account', false);
});
