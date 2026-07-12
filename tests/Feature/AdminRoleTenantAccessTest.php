<?php

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\TenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use TomatoPHP\FilamentEcommerce\Filament\Resources\ProductResource;

uses(RefreshDatabase::class);

it('grants product access when the admin role matches the active tenant', function () {
    $this->seed(TenantSeeder::class);

    $team = Team::query()->where('slug', 'ali-charisma')->firstOrFail();
    $adminRole = Role::query()->create([
        'name' => 'admin',
        'guard_name' => 'web',
        'team_id' => $team->id,
    ]);

    setPermissionsTeamId($team->id);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    $adminRole->syncPermissions(['view_any_product', 'view_product']);

    $user = User::factory()->create(['email' => 'tenant-admin@example.com']);
    $user->teams()->attach($team);
    $user->assignRole($adminRole);

    $this->actingAs($user);
    filament()->setCurrentPanel(filament()->getPanel('admin'));
    filament()->setTenant($team);
    setPermissionsTeamId($team->id);

    expect(ProductResource::canViewAny())->toBeTrue();

    $this->get('/admin/ali-charisma/products')->assertSuccessful();
});

it('denies access when a role from another tenant is attached', function () {
    $this->seed(TenantSeeder::class);

    $ali = Team::query()->where('slug', 'ali-charisma')->firstOrFail();
    $djarum = Team::query()->where('slug', 'djarum-hijau')->firstOrFail();

    $djarumAdmin = Role::query()->create([
        'name' => 'admin',
        'guard_name' => 'web',
        'team_id' => $djarum->id,
    ]);

    setPermissionsTeamId($djarum->id);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    $djarumAdmin->syncPermissions(['view_any_product', 'view_product']);

    $user = User::factory()->create(['email' => 'mismatched@example.com']);
    $user->teams()->attach($ali);

    // Broken assignment: Djarum role under Ali Charisma team context.
    DB::table('model_has_roles')->insert([
        'role_id' => $djarumAdmin->id,
        'model_type' => User::class,
        'model_id' => $user->id,
        'team_id' => $ali->id,
    ]);

    $this->actingAs($user);
    filament()->setCurrentPanel(filament()->getPanel('admin'));
    filament()->setTenant($ali);
    setPermissionsTeamId($ali->id);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    expect($user->getAllPermissions())->toHaveCount(0)
        ->and(ProductResource::canViewAny())->toBeFalse();
});
