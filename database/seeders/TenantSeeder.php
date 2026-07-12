<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use BezhanSalleh\FilamentShield\FilamentShield;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $aliCharisma = Team::query()->create([
            'name' => 'Ali Charisma',
            'slug' => 'ali-charisma',
        ]);

        $djarumHijau = Team::query()->create([
            'name' => 'Djarum Hijau',
            'slug' => 'djarum-hijau',
        ]);

        $admin = User::query()->create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $admin->teams()->attach([$aliCharisma->id, $djarumHijau->id]);

        Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 'admin',
            '--relationships' => true,
        ]);

        foreach ([$aliCharisma, $djarumHijau] as $team) {
            setPermissionsTeamId($team->id);

            $role = FilamentShield::createRole(tenantId: $team->id);
            $role->syncPermissions(Permission::query()->pluck('id'));

            $admin->assignRole($role);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
