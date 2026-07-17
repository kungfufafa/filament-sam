<?php

use App\Enums\OutletRegistrationStatus;
use App\Enums\OutletRegistrationType;
use App\Enums\OutletStatus;
use App\Enums\ScheduleScope;
use App\Enums\SystemSettingScopeLevel;
use App\Enums\TransactionStatus;
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
        Schema::create('outlet_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_entity_id')->constrained('business_entities')->cascadeOnDelete();
            $table->foreignId('division_id')->constrained('divisions')->cascadeOnDelete();
            $table->foreignId('region_id')->constrained('regions')->cascadeOnDelete();
            $table->foreignId('cluster_id')->constrained('clusters')->cascadeOnDelete();
            $table->foreignId('tm_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('code')->nullable();
            $table->enum('type', array_column(OutletRegistrationType::cases(), 'value'))
                ->default(OutletRegistrationType::Noo->value);
            $table->string('name');
            $table->text('address');
            $table->string('owner_name');
            $table->string('phone_number');
            $table->string('representative_phone_number')->nullable();
            $table->string('owner_identity_number')->nullable();
            $table->string('district')->nullable();
            $table->string('shop_sign_photo')->nullable();
            $table->string('storefront_photo')->nullable();
            $table->string('left_side_photo')->nullable();
            $table->string('right_side_photo')->nullable();
            $table->string('owner_identity_photo')->nullable();
            $table->string('video')->nullable();
            $table->string('oppo')->nullable();
            $table->string('vivo')->nullable();
            $table->string('realme')->nullable();
            $table->string('samsung')->nullable();
            $table->string('xiaomi')->nullable();
            $table->string('fl')->nullable();
            $table->string('coordinates')->nullable();
            $table->bigInteger('limit')->nullable();
            $table->enum('status', array_column(OutletRegistrationStatus::cases(), 'value'))
                ->default(OutletRegistrationStatus::Pending->value);
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('rejected_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignId('confirmed_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_entity_id')->constrained('business_entities')->cascadeOnDelete();
            $table->foreignId('division_id')->constrained('divisions')->cascadeOnDelete();
            $table->foreignId('region_id')->constrained('regions')->cascadeOnDelete();
            $table->foreignId('cluster_id')->constrained('clusters')->cascadeOnDelete();
            $table->foreignId('outlet_registration_id')->nullable()->constrained('outlet_registrations')->nullOnDelete();
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('owner_name');
            $table->string('phone_number');
            $table->string('owner_identity_photo')->nullable();
            $table->bigInteger('limit')->nullable();
            $table->enum('status', array_column(OutletStatus::cases(), 'value'));
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('outlet_geotags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('district')->nullable();
            $table->text('address')->nullable();
            $table->string('coordinates');
            $table->unsignedInteger('radius')->default(100);
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('shop_sign_photo')->nullable();
            $table->string('storefront_photo')->nullable();
            $table->string('left_side_photo')->nullable();
            $table->string('right_side_photo')->nullable();
            $table->string('video')->nullable();
            $table->timestamps();

            $table->index(['outlet_id', 'is_active']);
        });

        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->nullableMorphs('visitable');
            $table->timestamp('occurred_at');
            $table->string('purpose')->nullable();
            $table->string('check_in_coordinates')->nullable();
            $table->string('check_out_coordinates')->nullable();
            $table->timestamp('check_in_time')->nullable();
            $table->timestamp('check_out_time')->nullable();
            $table->text('report')->nullable();
            $table->enum('transaction_status', array_column(TransactionStatus::cases(), 'value'))->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->text('check_in_photo')->nullable();
            $table->text('check_out_photo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('plan_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->nullableMorphs('visitable');
            $table->timestamp('scheduled_at');
            $table->enum('schedule_scope', array_column(ScheduleScope::cases(), 'value'))
                ->default(ScheduleScope::Daily->value);
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->tinyInteger('schedule_week')->nullable();
            $table->smallInteger('schedule_year')->nullable();
            $table->timestamp('realized_at')->nullable();
            $table->foreignId('realized_visit_id')->nullable()->constrained('visits')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('whatsapp_otps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('whatsapp_number', 20);
            $table->string('purpose', 32);
            $table->string('otp_hash');
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->tinyInteger('attempt_count')->default(0);
            $table->timestamps();
        });

        Schema::create('outlet_change_archives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->nullable()->constrained('outlets')->nullOnDelete();
            $table->string('code')->nullable();
            $table->string('action', 40);
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_name')->nullable();
            $table->json('old_values');
            $table->json('new_values')->nullable();
            $table->json('changed_fields')->nullable();
            $table->json('request_meta')->nullable();
            $table->foreignId('restored_from_id')->nullable()->constrained('outlet_change_archives')->nullOnDelete();
            $table->foreignId('restored_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('restored_at')->nullable();
            $table->timestamps();
        });

        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('scope_level', array_column(SystemSettingScopeLevel::cases(), 'value'))
                ->default(SystemSettingScopeLevel::Global->value);
            $table->foreignId('business_entity_id')->nullable()->constrained('business_entities')->nullOnDelete();
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->foreignId('region_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->foreignId('cluster_id')->nullable()->constrained('clusters')->nullOnDelete();
            $table->boolean('allow_outlet_registration_visits')->default(true);
            $table->integer('default_outlet_registration_radius')->default(100);
            $table->tinyInteger('plan_visit_min_days')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('outlet_change_archives');
        Schema::dropIfExists('whatsapp_otps');
        Schema::dropIfExists('plan_visits');
        Schema::dropIfExists('visits');
        Schema::dropIfExists('outlet_geotags');
        Schema::dropIfExists('outlets');
        Schema::dropIfExists('outlet_registrations');
    }
};
