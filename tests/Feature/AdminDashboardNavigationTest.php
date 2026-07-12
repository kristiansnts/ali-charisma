<?php

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\TenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

it('shows ecommerce menus for a tenant admin with product permissions', function () {
    $this->seed(TenantSeeder::class);

    $team = Team::query()->where('slug', 'ali-charisma')->firstOrFail();

    setPermissionsTeamId($team->id);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $role = Role::query()->create([
        'name' => 'admin',
        'guard_name' => 'web',
        'team_id' => $team->id,
    ]);
    $role->syncPermissions([
        'view_any_product',
        'view_product',
        'view_any_order',
        'view_order',
        'view_any_shipping::vendor',
        'view_shipping::vendor',
    ]);

    $user = User::factory()->create([
        'name' => 'Tenant Admin',
        'email' => 'tenant-admin@example.com',
    ]);
    $user->teams()->attach($team);
    $user->assignRole($role);

    $this->actingAs($user)
        ->get('/admin/ali-charisma')
        ->assertSuccessful()
        ->assertSee('Products')
        ->assertSee('Orders')
        ->assertDontSee('Companies', false);
});
