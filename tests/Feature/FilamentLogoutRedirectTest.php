<?php

use App\Models\User;
use Database\Seeders\TenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects filament logout to the storefront home page', function () {
    $this->seed(TenantSeeder::class);

    $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

    $this->actingAs($admin)
        ->post(route('filament.admin.auth.logout'))
        ->assertRedirect(route('malefashion.home'));

    $this->assertGuest();
});
