<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\InactivityRecord;
use App\Models\InactivityReason;
use App\Models\Position;
use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\PositionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class InactivityDayTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_view_permission_cannot_access_inactivity_days(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('inactivity-records.index'))
            ->assertForbidden();
    }

    public function test_parent_records_use_the_main_route_and_days_only_have_a_detail_route(): void
    {
        $this->assertTrue(Route::has('inactivity-records.index'));
        $this->assertTrue(Route::has('inactivity-days.show'));
        $this->assertFalse(Route::has('inactivity-days.index'));
    }

    public function test_user_can_assign_and_manage_a_grouped_date_range(): void
    {
        $user = $this->userWithPermissions(['view', 'create', 'edit', 'delete']);
        $employee = $this->createEmployee();
        $reason = InactivityReason::create(['name' => 'Vacaciones', 'is_active' => true]);

        $this->actingAs($user)
            ->get(route('inactivity-records.create'))
            ->assertOk()
            ->assertSee('Fecha inicial')
            ->assertSee('Remunerado');

        $this->post(route('inactivity-records.store'), [
            'employee_id' => $employee->id,
            'inactivity_reason_id' => $reason->id,
            'date_from' => '2026-10-30',
            'date_to' => '2026-11-02',
            'is_paid' => '1',
        ])->assertRedirect(route('inactivity-records.index'));

        $this->assertDatabaseCount('inactivity_record', 1);
        $this->assertDatabaseCount('inactivity_record_days', 4);
        $this->assertTrue(InactivityRecord::query()
            ->where('employee_id', $employee->id)
            ->where('inactivity_reason_id', $reason->id)
            ->where('is_paid', true)
            ->where('is_active', true)
            ->exists());

        $this->get(route('inactivity-records.index'))
            ->assertOk()
            ->assertSee('Inactividad')
            ->assertSee('Ver detalle')
            ->assertSee('Vacaciones')
            ->assertSee($employee->full_name);

        $record = InactivityRecord::query()->with('days')->firstOrFail();
        $this->get(route('inactivity-days.show', $record))
            ->assertOk()
            ->assertSee('ID del registro')
            ->assertSee('Cantidad de días')
            ->assertSee('Calendario de días asignados')
            ->assertSee('Día asignado')
            ->assertSee('octubre 2026')
            ->assertSee('noviembre 2026')
            ->assertSee('Vacaciones')
            ->assertSee('30/10/2026')
            ->assertSee('02/11/2026');
        $this->get(route('inactivity-records.edit', $record))->assertOk()->assertSee('Fecha inicial');

        $this->put(route('inactivity-records.update', $record), [
            'employee_id' => $employee->id,
            'inactivity_reason_id' => $reason->id,
            'date_from' => '2026-11-05',
            'date_to' => '2026-11-06',
            'is_paid' => '0',
            'is_active' => '1',
        ])->assertRedirect(route('inactivity-records.index'));

        $this->assertDatabaseCount('inactivity_record', 1);
        $this->assertDatabaseCount('inactivity_record_days', 2);
        $this->assertTrue(InactivityRecord::query()
            ->whereKey($record->id)
            ->where('is_paid', false)
            ->exists());

        $this->delete(route('inactivity-records.destroy', $record))
            ->assertRedirect(route('inactivity-records.index'));
        $this->assertDatabaseHas('inactivity_record', [
            'id' => $record->id,
            'is_active' => false,
        ]);
        $this->assertDatabaseCount('inactivity_record_days', 2);
    }

    public function test_range_is_rejected_when_an_active_day_already_exists_for_employee(): void
    {
        $user = $this->userWithPermissions(['create']);
        $employee = $this->createEmployee();
        $reason = InactivityReason::create(['name' => 'Permiso médico', 'is_active' => true]);
        $record = InactivityRecord::create([
            'employee_id' => $employee->id,
            'inactivity_reason_id' => $reason->id,
            'is_paid' => false,
            'is_active' => true,
        ]);
        $record->days()->create(['date' => '2026-11-03']);

        $this->actingAs($user)
            ->post(route('inactivity-records.store'), [
                'employee_id' => $employee->id,
                'inactivity_reason_id' => $reason->id,
                'date_from' => '2026-11-02',
                'date_to' => '2026-11-04',
                'is_paid' => '0',
            ])
            ->assertSessionHasErrors('date_from');

        $this->assertDatabaseCount('inactivity_record', 1);
        $this->assertDatabaseCount('inactivity_record_days', 1);
    }

    private function userWithPermissions(array $actions): User
    {
        $user = User::factory()->create();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($actions as $action) {
            $user->givePermissionTo(Permission::findOrCreate("inactivity-days.{$action}", 'web'));
        }

        return $user;
    }

    private function createEmployee(): Employee
    {
        $this->seed([
            DocumentTypeSeeder::class,
            DepartmentSeeder::class,
            PositionSeeder::class,
        ]);

        return Employee::factory()->create();
    }
}