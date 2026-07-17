<?php

namespace Tests\Feature;

use App\Models\BusinessEntity;
use App\Models\Cluster;
use App\Models\Division;
use App\Models\Outlet;
use App\Models\OutletRegistration;
use App\Models\Region;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ExporterPermissionTest extends TestCase
{
    use RefreshDatabase;

    private function createHierarchy(): array
    {
        $businessEntity = BusinessEntity::factory()->create();
        $division = Division::factory()->create(['business_entity_id' => $businessEntity->id]);
        $region = Region::factory()->create(['business_entity_id' => $businessEntity->id, 'division_id' => $division->id]);
        $cluster = Cluster::factory()->create(['business_entity_id' => $businessEntity->id, 'division_id' => $division->id, 'region_id' => $region->id]);

        return [$businessEntity, $division, $region, $cluster];
    }

    public function test_user_with_export_outlet_registration_permission_can_export(): void
    {
        [$businessEntity, $division, $region, $cluster] = $this->createHierarchy();

        $user = User::factory()->create();
        $permission = Permission::firstOrCreate(['name' => 'Export:OutletRegistration', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);

        $outletRegistration = OutletRegistration::factory()->create([
            'business_entity_id' => $businessEntity->id,
            'division_id' => $division->id,
            'region_id' => $region->id,
            'cluster_id' => $cluster->id,
            'code' => 'REG-1',
            'name' => 'Outlet Registration 1',
            'owner_name' => 'Owner Name',
            'phone_number' => '0812345678',
            'address' => 'Alamat Test',
            'representative_phone_number' => '0812345678',
            'owner_identity_number' => '1234567890123456',
            'district' => 'District Test',
        ]);

        $this->assertTrue(Gate::forUser($user)->check('export', $outletRegistration));
    }

    public function test_user_without_export_outlet_registration_permission_cannot_export(): void
    {
        [$businessEntity, $division, $region, $cluster] = $this->createHierarchy();

        $user = User::factory()->create();
        $outletRegistration = OutletRegistration::factory()->create([
            'business_entity_id' => $businessEntity->id,
            'division_id' => $division->id,
            'region_id' => $region->id,
            'cluster_id' => $cluster->id,
            'code' => 'REG-1',
            'name' => 'Outlet Registration 1',
            'owner_name' => 'Owner Name',
            'phone_number' => '0812345678',
            'address' => 'Alamat Test',
            'representative_phone_number' => '0812345678',
            'owner_identity_number' => '1234567890123456',
            'district' => 'District Test',
        ]);

        $this->assertFalse(Gate::forUser($user)->check('export', $outletRegistration));
    }

    public function test_user_with_export_visit_permission_can_export(): void
    {
        [$businessEntity, $division, $region, $cluster] = $this->createHierarchy();

        $user = User::factory()->create();
        $permission = Permission::firstOrCreate(['name' => 'Export:Visit', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);

        $outlet = Outlet::factory()->create([
            'name' => 'Outlet Test',
            'owner_name' => 'Owner Name',
            'phone_number' => '0812345678',
            'business_entity_id' => $businessEntity->id,
            'division_id' => $division->id,
            'region_id' => $region->id,
            'cluster_id' => $cluster->id,
        ]);

        $visit = Visit::factory()->create([
            'user_id' => $user->id,
            'visitable_type' => Outlet::class,
            'visitable_id' => $outlet->id,
            'occurred_at' => now(),
        ]);

        $this->assertTrue(Gate::forUser($user)->check('export', $visit));
    }

    public function test_user_without_export_visit_permission_cannot_export(): void
    {
        [$businessEntity, $division, $region, $cluster] = $this->createHierarchy();

        $user = User::factory()->create();

        $outlet = Outlet::factory()->create([
            'name' => 'Outlet Test',
            'owner_name' => 'Owner Name',
            'phone_number' => '0812345678',
            'business_entity_id' => $businessEntity->id,
            'division_id' => $division->id,
            'region_id' => $region->id,
            'cluster_id' => $cluster->id,
        ]);

        $visit = Visit::factory()->create([
            'user_id' => $user->id,
            'visitable_type' => Outlet::class,
            'visitable_id' => $outlet->id,
            'occurred_at' => now(),
        ]);

        $this->assertFalse(Gate::forUser($user)->check('export', $visit));
    }
}
