<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table): void {
            $table->foreignId('user_id')
                ->nullable()
                ->after('team_id')
                ->constrained()
                ->nullOnDelete();
        });

        DB::table('accounts')
            ->whereNull('type')
            ->orWhere('type', 'account')
            ->update(['type' => 'customer']);
    }

    public function down(): void
    {
        DB::table('accounts')
            ->where('type', 'customer')
            ->update(['type' => 'account']);

        Schema::table('accounts', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
