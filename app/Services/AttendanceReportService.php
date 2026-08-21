<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttendanceReportService
{
    public function __construct(
        private readonly AttendanceService $attendanceService
    ) {
    }

    /**
     * Genera las jornadas del empleado dentro de un rango de fechas.
     */
    public function getEmployeeReport(
        Employee $employee,
        Carbon $dateFrom,
        Carbon $dateTo
    ): Collection {
        return collect($this->attendanceService->buildReportRecords(
            $employee,
            $dateFrom,
            $dateTo
        ));
    }

    /**
     * Obtiene los minutos programados para un día.
     */
    private function getScheduledMinutes($scheduleDay): int
    {
        if (
            !$scheduleDay->start_time ||
            !$scheduleDay->end_time
        ) {
            return 0;
        }

        $start = Carbon::parse($scheduleDay->start_time);
        $end = Carbon::parse($scheduleDay->end_time);

        return $start->diffInMinutes($end)
            - $scheduleDay->lunch_minutes;
    }
}
