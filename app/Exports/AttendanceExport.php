<?php

namespace App\Exports;

use App\Models\Employee;
use App\Services\AttendanceService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AttendanceExport implements WithMultipleSheets
{
    public function __construct(
        private readonly array $filters,
        private readonly AttendanceService $attendanceService
    ) {
    }

    public function sheets(): array
    {
        $employees = Employee::query()
            ->with(['department', 'position', 'workSchedule.days.weekDay'])
            ->when($this->filters['employee_id'] ?? null, function ($query, $employeeId) {
                $query->where('id', $employeeId);
            })

            ->when($this->filters['department_id'] ?? null, function ($query, $departmentId) {
                $query->where('department_id', $departmentId);
            })

            ->when($this->filters['position_id'] ?? null, function ($query, $positionId) {
                $query->where('position_id', $positionId);
            })
            ->get();

        return $employees
            ->map(function (Employee $employee) {
                $title = preg_replace(
                    '/[\\\\\/\?\*\[\]:]/',
                    '',
                    trim($employee->full_name)
                ) ?: 'Empleado';
                $title = mb_substr($title, 0, 25);

                return new EmployeeAttendanceSheet(
                    $employee,
                    $this->filters,
                    $this->attendanceService,
                    mb_substr($title, 0, 31)
                );
            })
            ->all();
    }
}