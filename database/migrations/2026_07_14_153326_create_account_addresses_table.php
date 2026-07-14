<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->string('first_name')->default('');
            $table->string('last_name')->default('');
            $table->string('company')->default('');
            $table->string('phone', 40)->default('');
            $table->string('address1')->default('');
            $table->string('address2')->default('');
            $table->string('city')->default('');
            $table->string('zip', 32)->default('');
            $table->string('country')->default('Indonesia');
            $table->string('province')->default('');
            $table->boolean('is_default')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_addresses');
    }
};
