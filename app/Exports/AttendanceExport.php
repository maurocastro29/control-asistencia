<?php

namespace App\Exports;

use App\Models\AttendanceRecord;
use App\Services\AttendanceService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private readonly array $filters,
        private readonly AttendanceService $attendanceService
    ) {
    }

    /**
     * Obtiene los registros que serán exportados.
     */
    public function collection(): Collection
    {
        return AttendanceRecord::query()
            ->with([
                'employee.department',
                'employee.position',
                'employee.workSchedule',
            ])

            ->when($this->filters['date_from'] ?? null, function ($query, $date) {
                $query->whereDate('work_date', '>=', $date);
            })

            ->when($this->filters['date_to'] ?? null, function ($query, $date) {
                $query->whereDate('work_date', '<=', $date);
            })

            ->when($this->filters['employee_id'] ?? null, function ($query, $employeeId) {
                $query->where('employee_id', $employeeId);
            })

            ->when($this->filters['department_id'] ?? null, function ($query, $departmentId) {
                $query->whereHas('employee', function ($query) use ($departmentId) {
                    $query->where('department_id', $departmentId);
                });
            })

            ->when($this->filters['position_id'] ?? null, function ($query, $positionId) {
                $query->whereHas('employee', function ($query) use ($positionId) {
                    $query->where('position_id', $positionId);
                });
            })

            ->orderByDesc('work_date')
            ->orderBy('entry_time')
            ->get();
    }

    /**
     * Encabezados del archivo Excel.
     */
    public function headings(): array
    {
        return [
            'Fecha',
            'Empleado',
            'Departamento',
            'Cargo',
            'Entrada',
            'Salida',
            'Almuerzo',
            'Trabajado',
            'Ordinario',
            'Extra',
        ];
    }

    /**
     * Mapea cada jornada a una fila de Excel.
     */
    public function map($attendance): array
    {
        $calculation = $this->attendanceService
            ->calculateAttendance($attendance);

        return [
            $attendance->work_date->format('d/m/Y'),
            $attendance->employee->full_name,
            $attendance->employee->department?->name,
            $attendance->employee->position?->name,
            $attendance->entry_time?->format('H:i'),
            $attendance->exit_time?->format('H:i'),

            $this->attendanceService->formatMinutes(
                $attendance->lunch_time
            ),

            $this->attendanceService->formatMinutes(
                $calculation['worked_minutes']
            ),

            $this->attendanceService->formatMinutes(
                $calculation['ordinary_minutes']
            ),

            $this->attendanceService->formatMinutes(
                $calculation['overtime_minutes']
            ),
        ];
    }
}
