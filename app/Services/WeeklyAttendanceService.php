<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class WeeklyAttendanceService
{
    public function __construct(
        private readonly AttendanceService $attendanceService
    ) {
    }

    /**
     * Calcula el resumen semanal de un empleado.
     */
    public function calculate(
        Employee $employee,
        Carbon $weekStart
    ): array {
        $weekStart = $weekStart
            ->copy()
            ->startOfWeek(Carbon::MONDAY);

        $weekEnd = $weekStart
            ->copy()
            ->endOfWeek(Carbon::SUNDAY);

        $attendanceRecords = AttendanceRecord::query()
            ->where('employee_id', $employee->id)
            ->whereBetween('work_date', [
                $weekStart,
                $weekEnd,
            ])
            ->orderBy('work_date')
            ->get();

        $totalScheduledMinutes = 0;
        $totalWorkedMinutes = 0;

        /*
        |--------------------------------------------------------------------------
        | Déficit acumulado pendiente de compensar
        |--------------------------------------------------------------------------
        */
        $pendingDeficitMinutes = 0;

        $days = [];

        foreach (
            CarbonPeriod::create($weekStart, $weekEnd) as $date
        ) {
            $scheduleDay = $this->attendanceService
                ->getEmployeeScheduleForDate(
                    $employee,
                    $date
                );

            $attendance = $attendanceRecords->first(
                function ($record) use ($date) {
                    return $record->work_date->isSameDay($date);
                }
            );

            $scheduledMinutes = 0;
            $workedMinutes = 0;

            /*
            |--------------------------------------------------------------------------
            | Horas programadas
            |--------------------------------------------------------------------------
            */
            if (
                $scheduleDay &&
                $scheduleDay->is_working_day
            ) {
                $scheduledMinutes =
                    $scheduleDay->ordinary_minutes;
            }

            /*
            |--------------------------------------------------------------------------
            | Horas realmente trabajadas
            |--------------------------------------------------------------------------
            */
            if ($attendance) {
                $workedMinutes =
                    $this->attendanceService
                        ->calculateWorkedMinutes(
                            $attendance->entry_time->format('H:i'),
                            $attendance->exit_time->format('H:i'),
                            $attendance->lunch_time
                        );
            }

            /*
            |--------------------------------------------------------------------------
            | Diferencia del día
            |--------------------------------------------------------------------------
            */
            $differenceMinutes =
                $workedMinutes - $scheduledMinutes;

            $deficitMinutes = 0;
            $compensatedMinutes = 0;
            $overtimeMinutes = 0;

            /*
            |--------------------------------------------------------------------------
            | Caso 1: trabajó menos de lo programado
            |--------------------------------------------------------------------------
            */
            if ($differenceMinutes < 0) {

                $deficitMinutes = abs($differenceMinutes);

                $pendingDeficitMinutes += $deficitMinutes;

            }
            /*
            |--------------------------------------------------------------------------
            | Caso 2: trabajó más de lo programado
            |--------------------------------------------------------------------------
            */
            elseif ($differenceMinutes > 0) {

                $availableMinutes = $differenceMinutes;

                $compensatedMinutes = min(
                    $availableMinutes,
                    $pendingDeficitMinutes
                );

                $pendingDeficitMinutes -= $compensatedMinutes;

                $overtimeMinutes =
                    $availableMinutes - $compensatedMinutes;
            }

            $totalScheduledMinutes +=
                $scheduledMinutes;

            $totalWorkedMinutes +=
                $workedMinutes;

            $days[] = [
                'date' => $date->copy(),

                'scheduled_minutes' =>
                    $scheduledMinutes,

                'worked_minutes' =>
                    $workedMinutes,

                'difference_minutes' =>
                    $differenceMinutes,

                'deficit_minutes' =>
                    $deficitMinutes,

                'compensated_minutes' =>
                    $compensatedMinutes,

                'overtime_minutes' =>
                    $overtimeMinutes,

                'has_attendance' =>
                    (bool) $attendance,

                'is_scheduled_day' =>
                    $scheduledMinutes > 0,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Balance semanal
        |--------------------------------------------------------------------------
        */
        $balanceMinutes =
            $totalWorkedMinutes
            - $totalScheduledMinutes;

        return [
            'employee_id' =>
                $employee->id,

            'week_start' =>
                $weekStart,

            'week_end' =>
                $weekEnd,

            'scheduled_minutes' =>
                $totalScheduledMinutes,

            'worked_minutes' =>
                $totalWorkedMinutes,

            'balance_minutes' =>
                $balanceMinutes,

            'deficit_minutes' =>
                $pendingDeficitMinutes,

            'overtime_minutes' =>
                collect($days)->sum('overtime_minutes'),

            'days' =>
                $days,
        ];
    }
}