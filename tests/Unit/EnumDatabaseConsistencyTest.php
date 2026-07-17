<?php

namespace Tests\Unit;

use App\Enums\OrganizationalScopeLevel;
use App\Enums\OutletRegistrationStatus;
use App\Enums\OutletRegistrationType;
use App\Enums\OutletStatus;
use App\Enums\ScheduleScope;
use App\Enums\SystemSettingScopeLevel;
use App\Enums\TransactionStatus;
use App\Models\Outlet;
use App\Models\OutletRegistration;
use App\Models\PlanVisit;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

class EnumDatabaseConsistencyTest extends TestCase
{
    public function test_enum_values_match_the_existing_mysql_schema(): void
    {
        $this->assertSame(
            ['PENDING', 'CONFIRMED', 'APPROVED', 'REJECTED'],
            array_column(OutletRegistrationStatus::cases(), 'value'),
        );
        $this->assertSame(['NOO', 'LEAD'], array_column(OutletRegistrationType::cases(), 'value'));
        $this->assertSame(['YES', 'NO'], array_column(TransactionStatus::cases(), 'value'));
        $this->assertSame(
            ['all', 'business_entity', 'division', 'region', 'cluster'],
            array_column(OrganizationalScopeLevel::cases(), 'value'),
        );
        $this->assertSame(
            ['global', 'business_entity', 'division', 'region', 'cluster'],
            array_column(SystemSettingScopeLevel::cases(), 'value'),
        );
        $this->assertSame(
            ['MAINTAIN', 'UNMAINTAIN', 'UNPRODUCTIVE'],
            array_column(OutletStatus::cases(), 'value'),
        );
        $this->assertSame(
            ['daily', 'weekly', 'monthly'],
            array_column(ScheduleScope::cases(), 'value'),
        );
    }

    public function test_models_cast_database_values_to_their_enum_types(): void
    {
        $this->assertSame(OutletRegistrationStatus::Approved, $this->modelWithRawAttribute(OutletRegistration::class, 'status', 'APPROVED')->status);
        $this->assertSame(OutletRegistrationType::Lead, $this->modelWithRawAttribute(OutletRegistration::class, 'type', 'LEAD')->type);
        $this->assertSame(TransactionStatus::Yes, $this->modelWithRawAttribute(Visit::class, 'transaction_status', 'YES')->transaction_status);
        $this->assertSame(OrganizationalScopeLevel::Divisi, $this->modelWithRawAttribute(Role::class, 'organizational_scope_level', 'division')->organizational_scope_level);
        $this->assertSame(SystemSettingScopeLevel::Division, $this->modelWithRawAttribute(SystemSetting::class, 'scope_level', 'division')->scope_level);
        $this->assertSame(OutletStatus::Unproductive, $this->modelWithRawAttribute(Outlet::class, 'status', 'UNPRODUCTIVE')->status);
        $this->assertSame(ScheduleScope::Weekly, $this->modelWithRawAttribute(PlanVisit::class, 'schedule_scope', 'weekly')->schedule_scope);
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    private function modelWithRawAttribute(string $modelClass, string $attribute, string $value): Model
    {
        $model = new $modelClass;
        $model->setRawAttributes([$attribute => $value]);

        return $model;
    }
}
