<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('business_entity_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('business_entity_id')->constrained('business_entities')->cascadeOnDelete();
            $table->unique(['user_id', 'business_entity_id']);
            $table->timestamps();
        });

        Schema::create('division_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('division_id')->constrained('divisions')->cascadeOnDelete();
            $table->unique(['user_id', 'division_id']);
            $table->timestamps();
        });

        Schema::create('region_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('region_id')->constrained('regions')->cascadeOnDelete();
            $table->unique(['user_id', 'region_id']);
            $table->timestamps();
        });

        Schema::create('cluster_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('cluster_id')->constrained('clusters')->cascadeOnDelete();
            $table->unique(['user_id', 'cluster_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cluster_user');
        Schema::dropIfExists('region_user');
        Schema::dropIfExists('division_user');
        Schema::dropIfExists('business_entity_user');
    }
};
