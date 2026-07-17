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
        Schema::table('roles', function (Blueprint $table) {
            $table->foreignId('parent_role_id')
                ->nullable()
                ->after('guard_name')
                ->constrained('roles')
                ->nullOnDelete();
            $table->enum('organizational_scope_level', ['all', 'business_entity', 'division', 'region', 'cluster'])
                ->default('cluster')
                ->after('parent_role_id')
                ->index();
            $table->boolean('can_access_web')->default(true)->after('organizational_scope_level');
            $table->boolean('can_access_mobile')->default(true)->after('can_access_web');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_role_id');
            $table->dropColumn([
                'organizational_scope_level',
                'can_access_web',
                'can_access_mobile',
            ]);
        });
    }
};
