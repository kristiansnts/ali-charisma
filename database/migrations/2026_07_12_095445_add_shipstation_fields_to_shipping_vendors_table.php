<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('shipping_vendors')) {
            return;
        }

        Schema::table('shipping_vendors', function (Blueprint $table): void {
            if (! Schema::hasColumn('shipping_vendors', 'code')) {
                $table->string('code')->nullable()->after('team_id');
            }

            if (! Schema::hasColumn('shipping_vendors', 'carrier_id')) {
                $table->string('carrier_id')->nullable()->after('code');
            }

            if (! Schema::hasColumn('shipping_vendors', 'service_codes')) {
                $table->json('service_codes')->nullable()->after('carrier_id');
            }
        });

        Schema::table('shipping_vendors', function (Blueprint $table): void {
            $table->unique(['team_id', 'code']);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('shipping_vendors')) {
            return;
        }

        Schema::table('shipping_vendors', function (Blueprint $table): void {
            $table->dropUnique(['team_id', 'code']);
        });

        Schema::table('shipping_vendors', function (Blueprint $table): void {
            if (Schema::hasColumn('shipping_vendors', 'service_codes')) {
                $table->dropColumn('service_codes');
            }

            if (Schema::hasColumn('shipping_vendors', 'carrier_id')) {
                $table->dropColumn('carrier_id');
            }

            if (Schema::hasColumn('shipping_vendors', 'code')) {
                $table->dropColumn('code');
            }
        });
    }
};
