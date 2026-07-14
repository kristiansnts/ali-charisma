<?php

namespace Database\Seeders;

use App\Support\AccountUserLinker;
use Illuminate\Database\Seeder;

class AccountUserSyncSeeder extends Seeder
{
    public function run(): void
    {
        $synced = app(AccountUserLinker::class)->syncAllUsers();

        $this->command?->info("Synced {$synced} user(s) to accounts (admin/superadmin types applied).");
    }
}
