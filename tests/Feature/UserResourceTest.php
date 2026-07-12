<?php

use App\Filament\Resources\UserResource\Pages\ManageUsers;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\TenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

it('lists admin users for the active tenant', function () {
    $this->seed(TenantSeeder::class);

    $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
    $aliCharisma = Team::query()->where('slug', 'ali-charisma')->firstOrFail();
    $djarumHijau = Team::query()->where('slug', 'djarum-hijau')->firstOrFail();

    $aliOnly = User::factory()->create([
        'name' => 'Ali Staff',
        'email' => 'ali-staff@example.com',
    ]);
    $aliOnly->teams()->attach($aliCharisma);

    $djarumOnly = User::factory()->create([
        'name' => 'Djarum Staff',
        'email' => 'djarum-staff@example.com',
    ]);
    $djarumOnly->teams()->attach($djarumHijau);

    $this->actingAs($admin)
        ->get('/admin/ali-charisma/users')
        ->assertSuccessful()
        ->assertSee('Ali Staff')
        ->assertSee('Super Admin')
        ->assertDontSee('Djarum Staff');
});

it('creates an admin user for the active tenant with a role', function () {
    $this->seed(TenantSeeder::class);

    $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
    $aliCharisma = Team::query()->where('slug', 'ali-charisma')->firstOrFail();

    setPermissionsTeamId($aliCharisma->id);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $role = Role::query()
        ->where('name', config('filament-shield.super_admin.name'))
        ->where('team_id', $aliCharisma->id)
        ->firstOrFail();

    $this->actingAs($admin);
    filament()->setCurrentPanel(filament()->getPanel('admin'));
    filament()->setTenant($aliCharisma);

    Livewire::actingAs($admin)
        ->test(ManageUsers::class)
        ->callAction('create', [
            'name' => 'New Admin',
            'email' => 'new-admin@example.com',
            'password' => 'password',
            'roles' => [$role->id],
        ])
        ->assertHasNoActionErrors();

    $created = User::query()->where('email', 'new-admin@example.com')->firstOrFail();

    expect($created->teams()->whereKey($aliCharisma->id)->exists())->toBeTrue();

    setPermissionsTeamId($aliCharisma->id);
    expect($created->hasRole($role->name))->toBeTrue();
});
