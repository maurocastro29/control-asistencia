<?php

namespace Tests\Feature;

use App\Exports\EmployeeAttendanceSheet;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\InactivityRecord;
use App\Models\InactivityReason;
use App\Models\Position;
use App\Models\WeekDay;
use App\Models\WorkSchedule;
use App\Models\WorkScheduleDay;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\PositionSeeder;
use Database\Seeders\WeekDaySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AttendanceReportInactivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_paid_inactivity_counts_as_scheduled_work_in_report_and_excel(): void
    {
        [$employee, $reason] = $this->createEmployeeWithSchedule([1]);
        $this->createInactivity($employee, $reason, true);
        $this->assertDatabaseCount('inactivity_record_days', 1);
        $this->assertTrue($employee->inactivityRecords()->whereHas('days')->exists());
        $this->assertSame(1, $employee->workSchedule->days()->count());
        $service = app(AttendanceService::class);

        $records = $service->buildReportRecords(
            $employee,
            Carbon::parse('2026-11-02'),
            Carbon::parse('2026-11-02')
        );

        $this->assertCount(1, $records);
        $this->assertSame('inactivity_paid', $records[0]['status']);
        $this->assertSame(480, $records[0]['worked_minutes']);
        $this->assertSame(480, $records[0]['ordinary_minutes']);
        $this->assertSame(0, $records[0]['overtime_minutes']);
        $this->assertSame(0, $records[0]['missing_minutes']);

        $sheet = new EmployeeAttendanceSheet(
            $employee,
            ['date_from' => '2026-11-02', 'date_to' => '2026-11-02'],
            $service,
            'Reporte'
        );

        $this->assertSame(
            'Inactividad remunerada: Vacaciones',
            $sheet->map($records[0])[16]
        );
    }

    public function test_unpaid_inactivity_is_reported_without_worked_minutes(): void
    {
        [$employee, $reason] = $this->createEmployeeWithSchedule([1]);
        $this->createInactivity($employee, $reason, false);
        $records = app(AttendanceService::class)->buildReportRecords(
            $employee,
            Carbon::parse('2026-11-02'),
            Carbon::parse('2026-11-02')
        );

        $this->assertCount(1, $records);
        $this->assertSame('inactivity_unpaid', $records[0]['status']);
        $this->assertSame(0, $records[0]['worked_minutes']);
        $this->assertSame(0, $records[0]['ordinary_minutes']);
        $this->assertSame(480, $records[0]['missing_minutes']);
    }

    public function test_paid_inactivity_uses_the_employee_work_schedule_for_holiday_and_weekend_dates(): void
    {
        [$holidayEmployee, $holidayReason] = $this->createEmployeeWithSchedule([1]);
        Holiday::create([
            'date' => '2026-11-02',
            'name' => 'Festivo de prueba',
            'is_active' => true,
        ]);
        $this->createInactivity($holidayEmployee, $holidayReason, true, '2026-11-02');

        [$saturdayEmployee, $saturdayReason] = $this->createEmployeeWithSchedule([6]);
        $this->createInactivity($saturdayEmployee, $saturdayReason, true, '2026-11-07');

        [$restTuesdayEmployee, $restTuesdayReason] = $this->createEmployeeWithSchedule([3], [2]);
        $this->createInactivity($restTuesdayEmployee, $restTuesdayReason, true, '2026-11-03');

        [$weekendEmployee, $weekendReason] = $this->createEmployeeWithSchedule([1, 2, 3, 4, 5]);
        $this->createInactivityForDates(
            $weekendEmployee,
            $weekendReason,
            true,
            ['2026-11-07', '2026-11-08']
        );

        $service = app(AttendanceService::class);
        $holidayReport = $service->buildReportRecords(
            $holidayEmployee,
            Carbon::parse('2026-11-02'),
            Carbon::parse('2026-11-02')
        );
        $saturdayReport = $service->buildReportRecords(
            $saturdayEmployee,
            Carbon::parse('2026-11-07'),
            Carbon::parse('2026-11-07')
        );
        $restTuesdayReport = $service->buildReportRecords(
            $restTuesdayEmployee,
            Carbon::parse('2026-11-03'),
            Carbon::parse('2026-11-03')
        );
        $weekendReport = $service->buildReportRecords(
            $weekendEmployee,
            Carbon::parse('2026-11-07'),
            Carbon::parse('2026-11-08')
        );

        $this->assertCount(1, $holidayReport);
        $this->assertSame(480, $holidayReport[0]['worked_minutes']);
        $this->assertSame(480, $holidayReport[0]['ordinary_minutes']);
        $this->assertCount(1, $saturdayReport);
        $this->assertSame(480, $saturdayReport[0]['worked_minutes']);
        $this->assertSame(480, $saturdayReport[0]['ordinary_minutes']);
        $this->assertCount(0, $restTuesdayReport);
        $this->assertCount(0, $weekendReport);

        $sheet = new EmployeeAttendanceSheet(
            $weekendEmployee,
            ['date_from' => '2026-11-07', 'date_to' => '2026-11-08'],
            $service,
            'Reporte'
        );
        $this->assertCount(0, $sheet->collection());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate('reports.view', 'web'));
        $this->actingAs($user)
            ->get(route('reports.attendance', [
                'date_from' => '2026-11-07',
                'date_to' => '2026-11-08',
                'employee_id' => $weekendEmployee->id,
            ]))
            ->assertOk()
            ->assertSee('No se encontraron jornadas con los filtros seleccionados.');
    }

    private function createEmployeeWithSchedule(array $workingDayOrders, array $restDayOrders = []): array
    {
        $this->seed([
            DepartmentSeeder::class,
            DocumentTypeSeeder::class,
            PositionSeeder::class,
            WeekDaySeeder::class,
        ]);

        $schedule = WorkSchedule::create([
            'name' => 'Jornada de prueba',
            'weekly_minutes' => count($workingDayOrders) * 480,
            'is_active' => true,
        ]);
        foreach (array_merge($workingDayOrders, $restDayOrders) as $dayOrder) {
            $isWorkingDay = in_array($dayOrder, $workingDayOrders, true);
            WorkScheduleDay::create([
                'work_schedule_id' => $schedule->id,
                'week_day_id' => WeekDay::query()->where('order', $dayOrder)->value('id'),
                'entry_time' => $isWorkingDay ? '08:00' : null,
                'exit_time' => $isWorkingDay ? '17:00' : null,
                'lunch_minutes' => $isWorkingDay ? 60 : 0,
                'ordinary_minutes' => $isWorkingDay ? 480 : 0,
                'is_working_day' => $isWorkingDay,
            ]);
        }

        $employee = Employee::factory()->create();
        $employee->workSchedule()->associate($schedule)->save();
        $reason = InactivityReason::firstOrCreate(
            ['name' => 'Vacaciones'],
            ['is_active' => true]
        );

        return [$employee->fresh('workSchedule'), $reason];
    }

    private function createInactivity(
        Employee $employee,
        InactivityReason $reason,
        bool $isPaid,
        string $date = '2026-11-02'
    ): void
    {
        $this->createInactivityForDates($employee, $reason, $isPaid, [$date]);
    }

    private function createInactivityForDates(
        Employee $employee,
        InactivityReason $reason,
        bool $isPaid,
        array $dates
    ): void {
        $record = InactivityRecord::create([
            'employee_id' => $employee->id,
            'inactivity_reason_id' => $reason->id,
            'is_paid' => $isPaid,
            'is_active' => true,
        ]);

        $record->days()->createMany(array_map(
            fn (string $date) => ['date' => $date],
            $dates
        ));
    }
}