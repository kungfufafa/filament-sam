<?php

namespace Tests\Feature;

use App\Enums\OutletStatus;
use App\Models\BusinessEntity;
use App\Models\Cluster;
use App\Models\Division;
use App\Models\Outlet;
use App\Models\OutletGeotag;
use App\Models\Region;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OutletGeotagTest extends TestCase
{
    use RefreshDatabase;

    public function test_outlet_can_have_multiple_geotags_and_only_one_primary_geotag(): void
    {
        $businessEntity = BusinessEntity::factory()->create();
        $division = Division::factory()->create(['business_entity_id' => $businessEntity->id]);
        $region = Region::factory()->create([
            'business_entity_id' => $businessEntity->id,
            'division_id' => $division->id,
        ]);
        $cluster = Cluster::factory()->create([
            'business_entity_id' => $businessEntity->id,
            'division_id' => $division->id,
            'region_id' => $region->id,
        ]);
        $outlet = Outlet::query()->create([
            'name' => 'Outlet Test',
            'owner_name' => 'Pemilik',
            'phone_number' => '08123456789',
            'business_entity_id' => $businessEntity->id,
            'division_id' => $division->id,
            'region_id' => $region->id,
            'cluster_id' => $cluster->id,
            'status' => OutletStatus::Maintain,
        ]);

        $outlet->geotags()->create([
            'name' => 'Toko Utama',
            'district' => 'Kecamatan A',
            'address' => 'Alamat A',
            'coordinates' => '-6.200000, 106.816666',
            'radius' => 100,
            'is_primary' => true,
            'is_active' => true,
        ]);
        $secondGeotag = $outlet->geotags()->create([
            'name' => 'Gudang',
            'district' => 'Kecamatan B',
            'address' => 'Alamat B',
            'coordinates' => '-6.201000, 106.817666',
            'radius' => 50,
            'is_primary' => true,
            'is_active' => true,
            'shop_sign_photo' => 'outlets/shop_signs/test.jpg',
            'storefront_photo' => 'outlets/depan/test.jpg',
            'video' => 'outlets/videos/test.mp4',
        ]);

        $this->assertCount(2, $outlet->geotags);
        $this->assertSame(1, $outlet->geotags()->where('is_primary', true)->count());
        $this->assertTrue($secondGeotag->is_primary);
        $this->assertSame('Kecamatan B', $secondGeotag->district);
        $this->assertSame('Alamat B', $secondGeotag->address);
        $this->assertSame('outlets/shop_signs/test.jpg', $secondGeotag->shop_sign_photo);
        $this->assertSame('outlets/depan/test.jpg', $secondGeotag->storefront_photo);
        $this->assertSame('outlets/videos/test.mp4', $secondGeotag->video);
        $this->assertInstanceOf(OutletGeotag::class, $outlet->geotags()->first());
    }
}
