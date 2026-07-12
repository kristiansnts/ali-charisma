<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('accounts') || Schema::hasColumn('accounts', 'team_id')) {
            return;
        }

        Schema::table('accounts', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('accounts') || ! Schema::hasColumn('accounts', 'team_id')) {
            return;
        }

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('team_id');
        });
    }
};
