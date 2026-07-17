<?php

namespace Tests\Feature;

use App\Filament\Exports\OutletExporter;
use App\Filament\Exports\PlanVisitExporter;
use App\Filament\Exports\UserExporter;
use App\Filament\Imports\OutletImporter;
use App\Filament\Imports\PlanVisitImporter;
use App\Filament\Imports\UserImporter;
use App\Filament\Resources\Outlets\Pages\ListOutlets;
use App\Filament\Resources\PlanVisits\Pages\ListPlanVisits;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentImportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_export_infrastructure_is_available(): void
    {
        $this->assertTrue(Schema::hasTable('job_batches'));
        $this->assertTrue(Schema::hasTable('notifications'));
        $this->assertTrue(Schema::hasTable('imports'));
        $this->assertTrue(Schema::hasTable('exports'));
        $this->assertTrue(Schema::hasTable('failed_import_rows'));

        Filament::setCurrentPanel('admin');

        $this->assertTrue(Filament::getCurrentPanel()->hasDatabaseNotifications());
        $this->assertSame('30s', Filament::getCurrentPanel()->getDatabaseNotificationsPollingInterval());
    }

    public function test_importers_and_exporters_define_columns(): void
    {
        $this->assertNotEmpty(UserImporter::getColumns());
        $this->assertNotEmpty(UserExporter::getColumns());
        $this->assertNotEmpty(OutletImporter::getColumns());
        $this->assertNotEmpty(OutletExporter::getColumns());
        $this->assertNotEmpty(PlanVisitImporter::getColumns());
        $this->assertNotEmpty(PlanVisitExporter::getColumns());
    }

    public function test_authorized_user_can_see_import_and_export_actions(): void
    {
        $this->seed(AccessControlSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(Role::query()->where('name', 'ADMIN')->firstOrFail());

        $this->actingAs($user);
        Filament::setCurrentPanel('admin');

        foreach ([ListUsers::class, ListOutlets::class, ListPlanVisits::class] as $page) {
            Livewire::test($page)
                ->assertOk()
                ->assertActionVisible('import')
                ->assertActionVisible('export')
                ->assertActionVisible('create');
        }
    }
}
