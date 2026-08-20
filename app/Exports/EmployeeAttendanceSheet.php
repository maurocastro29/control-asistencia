<?php

namespace App\Exports;

use App\Models\Employee;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class EmployeeAttendanceSheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    public function __construct(
        private readonly Employee $employee,
        private readonly array $filters,
        private readonly AttendanceService $attendanceService,
        private readonly string $sheetTitle
    ) {
    }

    public function collection(): Collection
    {
        return collect($this->attendanceService->buildReportRecords(
            $this->employee,
            Carbon::parse($this->filters['date_from'] ?? now()->startOfMonth()),
            Carbon::parse($this->filters['date_to'] ?? now())
        ));
    }

    public function headings(): array
    {
        return [
            'Nombre y apellidos',
            'Fecha',
            'Departamento',
            'Cargo',
            'Hora entrada',
            'Hora salida',
            'Almuerzo',
            'Horas laboradas',
            'Ordinario',
            'Horas extras',
            'H.E.D',
            'H.E.N',
            'H.E.F.D',
            'H.E.F.N',
            'R.N',
            'R.N.F',
        ];
    }

    public function map($record): array
    {
        $attendance = $record['attendance'];

        return [
            $record['employee']->full_name,
            $record['work_date']->format('d/m/Y'),
            $record['employee']->department?->name,
            $record['employee']->position?->name,
            $attendance?->entry_time?->format('H:i'),
            $attendance?->exit_time?->format('H:i'),
            $this->formatMinutes($attendance?->lunch_time ?? 0),
            $this->formatMinutes($record['worked_minutes']),
            $this->formatMinutes($record['ordinary_minutes']),
            $this->formatMinutes($record['overtime_minutes']),
            $this->formatMinutes($record['hed_minutes'] ?? 0),
            $this->formatMinutes($record['hen_minutes'] ?? 0),
            $this->formatMinutes($record['hefd_minutes'] ?? 0),
            $this->formatMinutes($record['hefn_minutes'] ?? 0),
            $this->formatMinutes($record['rn_minutes'] ?? 0),
            $this->formatMinutes($record['rnf_minutes'] ?? 0),
        ];
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }

    private function formatMinutes(int $minutes): string
    {
        return $this->attendanceService->formatMinutes($minutes);
    }
}