<?php

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Policies\CompanyPolicy;
use Database\Seeders\TenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use TomatoPHP\FilamentEcommerce\Filament\Resources\CompanyResource;
use TomatoPHP\FilamentEcommerce\Models\Company;

uses(RefreshDatabase::class);

it('registers the company policy for the tomato company model', function () {
    expect(Gate::getPolicyFor(Company::class))->toBeInstanceOf(CompanyPolicy::class);
});

it('hides companies when the role lacks company permissions', function () {
    $this->seed(TenantSeeder::class);

    $team = Team::query()->where('slug', 'ali-charisma')->firstOrFail();

    setPermissionsTeamId($team->id);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $role = Role::query()->create([
        'name' => 'limited_admin',
        'guard_name' => 'web',
        'team_id' => $team->id,
    ]);

    $role->syncPermissions(
        Permission::query()
            ->where('name', 'not like', '%company%')
            ->pluck('id'),
    );

    $user = User::factory()->create([
        'email' => 'limited@example.com',
    ]);
    $user->teams()->attach($team);
    $user->assignRole($role);

    $this->actingAs($user);
    filament()->setCurrentPanel(filament()->getPanel('admin'));
    filament()->setTenant($team);

    setPermissionsTeamId($team->id);

    expect($user->can('view_any_company'))->toBeFalse()
        ->and(CompanyResource::canViewAny())->toBeFalse()
        ->and($this->get('/admin/ali-charisma/companies')->status())->toBe(403);
});
