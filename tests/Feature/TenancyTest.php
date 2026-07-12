<?php

use App\Models\Team;
use App\Models\User;
use Database\Seeders\TenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('seeds ali charisma and djarum hijau tenants', function () {
    $this->seed(TenantSeeder::class);

    expect(Team::query()->pluck('slug')->all())->toBe([
        'ali-charisma',
        'djarum-hijau',
    ]);
});

it('lets the super admin access both tenant dashboards', function () {
    $this->seed(TenantSeeder::class);

    $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

    $this->actingAs($admin)
        ->get('/admin/ali-charisma')
        ->assertSuccessful();

    $this->actingAs($admin)
        ->get('/admin/djarum-hijau')
        ->assertSuccessful();
});

it('lets the super admin access tenant orders without import export actions', function () {
    $this->seed(TenantSeeder::class);

    $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

    $this->actingAs($admin)
        ->get('/admin/ali-charisma/orders')
        ->assertSuccessful()
        ->assertDontSee('Import Orders', false);
});
it('lets the super admin access tenant accounts', function () {
    $this->seed(TenantSeeder::class);

    $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

    $this->actingAs($admin)
        ->get('/admin/ali-charisma/accounts')
        ->assertSuccessful();
});
it('scopes products to the active tenant', function () {
    $this->seed(TenantSeeder::class);

    $aliCharisma = Team::query()->where('slug', 'ali-charisma')->firstOrFail();
    $djarumHijau = Team::query()->where('slug', 'djarum-hijau')->firstOrFail();

    $aliProduct = \TomatoPHP\FilamentEcommerce\Models\Product::query()->create([
        'team_id' => $aliCharisma->id,
        'name' => 'Ali Product',
        'slug' => 'ali-product',
        'price' => 10,
    ]);

    $djarumProduct = \TomatoPHP\FilamentEcommerce\Models\Product::query()->create([
        'team_id' => $djarumHijau->id,
        'name' => 'Djarum Product',
        'slug' => 'djarum-product',
        'price' => 20,
    ]);

    expect(\TomatoPHP\FilamentEcommerce\Models\Product::query()->whereBelongsTo($aliCharisma)->pluck('id')->all())
        ->toBe([$aliProduct->id]);

    expect(\TomatoPHP\FilamentEcommerce\Models\Product::query()->whereBelongsTo($djarumHijau)->pluck('id')->all())
        ->toBe([$djarumProduct->id]);
});
