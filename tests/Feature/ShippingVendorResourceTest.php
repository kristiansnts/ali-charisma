<?php

use App\Models\ShippingVendor;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\ShippingCarrierSeeder;
use Database\Seeders\TenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('seeds the dhl express carrier per tenant', function () {
    $this->seed(TenantSeeder::class);
    $this->seed(ShippingCarrierSeeder::class);

    $team = Team::query()->where('slug', 'ali-charisma')->firstOrFail();

    expect(ShippingVendor::query()->where('team_id', $team->id)->count())->toBe(1)
        ->and(config('shipstation.carriers'))->toHaveCount(1)
        ->and(config('shipstation.carriers.0.carrier_id'))->toBe('se-6345411')
        ->and(config('shipstation.carriers.0.service_codes'))->toBe(['dhl_express_mydhl_express_worldwide_nondoc']);
});

it('lists shipping vendors without a create action for tenant admins', function () {
    $this->seed(TenantSeeder::class);
    $this->seed(ShippingCarrierSeeder::class);

    $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

    $this->actingAs($admin)
        ->get('/admin/ali-charisma/shipping-vendors')
        ->assertSuccessful()
        ->assertSee('DHL Express Worldwide')
        ->assertDontSee('Create', false);
});

it('syncs the dhl carrier when admin opens shipping vendors without a prior seed', function () {
    $this->seed(TenantSeeder::class);

    $team = Team::query()->where('slug', 'ali-charisma')->firstOrFail();

    expect(ShippingVendor::query()->where('team_id', $team->id)->count())->toBe(0);

    $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

    $this->actingAs($admin)
        ->get('/admin/ali-charisma/shipping-vendors')
        ->assertSuccessful()
        ->assertSee('DHL Express Worldwide');

    expect(ShippingVendor::query()->where('team_id', $team->id)->count())->toBe(1);
});
