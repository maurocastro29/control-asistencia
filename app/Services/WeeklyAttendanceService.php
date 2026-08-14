<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\WorkScheduleAdjustment;

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

        $adjustments = WorkScheduleAdjustment::query()
            ->where('employee_id', $employee->id)
            ->where('is_active', true)
            ->where(function ($query) use ($weekStart, $weekEnd) {
                $query
                    ->whereBetween('adjustment_date', [
                        $weekStart,
                        $weekEnd,
                    ])
                    ->orWhereBetween('compensation_date', [
                        $weekStart,
                        $weekEnd,
                    ]);
            })
            ->get();

        $totalScheduledMinutes = 0;
        $totalWorkedMinutes = 0;

        $days = [];

        foreach (
            CarbonPeriod::create($weekStart, $weekEnd) as $date
        ) {
            $scheduleDay = $this->attendanceService
                ->getEmployeeScheduleForDate(
                    $employee,
                    $date
                );

            $attendances = $attendanceRecords->filter(
                function ($record) use ($date) {
                    return $record->work_date->isSameDay($date);
                }
            );

            $adjustmentsForDate = $adjustments->filter(
                function ($adjustment) use ($date) {
                    return (
                        $adjustment->adjustment_date->isSameDay($date)
                        ||
                        (
                            $adjustment->compensation_date
                            && $adjustment->compensation_date->isSameDay($date)
                        )
                    );
                }
            );

            $scheduledMinutes = 0;
            $workedMinutes = 0;

            /*
            |--------------------------------------------------------------------------
            | Horas programadas normales
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
            | Aplicar ajustes de reducción
            |--------------------------------------------------------------------------
            */
            $reducedMinutes = $adjustmentsForDate
                ->filter(function ($adjustment) use ($date) {
                    return $adjustment->adjustment_date->isSameDay($date);
                })
                ->sum('reduced_minutes');

            $scheduledMinutes -= $reducedMinutes;

            /*
            |--------------------------------------------------------------------------
            | Aplicar compensaciones
            |--------------------------------------------------------------------------
            */
            $compensationMinutes = $adjustmentsForDate
                ->filter(function ($adjustment) use ($date) {
                    return (
                        $adjustment->compensation_date
                        && $adjustment->compensation_date->isSameDay($date)
                    );
                })
                ->sum('reduced_minutes');

            $scheduledMinutes += $compensationMinutes;

            /*
            |--------------------------------------------------------------------------
            | Evitar valores negativos
            |--------------------------------------------------------------------------
            */
            $scheduledMinutes = max(0, $scheduledMinutes);

            /*
            |--------------------------------------------------------------------------
            | Horas realmente trabajadas
            |--------------------------------------------------------------------------
            |
            | Puede existir más de una jornada el mismo día.
            |
            */
            if ($attendances->isNotEmpty()) {
                foreach ($attendances as $attendance) {
                    $workedMinutes +=
                        $this->attendanceService
                            ->calculateWorkedMinutes(
                                $attendance->entry_time->format('H:i'),
                                $attendance->exit_time->format('H:i'),
                                $attendance->lunch_time
                            );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Compensación realmente realizada
            |--------------------------------------------------------------------------
            |
            | Si existe una compensación programada para este día,
            | las horas trabajadas se utilizan primero para cubrir
            | dicha compensación.
            |
            */
            $compensatedMinutes = 0;

            if ($compensationMinutes > 0) {
                $compensatedMinutes = min(
                    $workedMinutes,
                    $compensationMinutes
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
            $overtimeMinutes = 0;

            /*
            |--------------------------------------------------------------------------
            | Déficit del día
            |--------------------------------------------------------------------------
            */
            if ($differenceMinutes < 0) {

                /*
                |--------------------------------------------------------------------------
                | Si existe una reducción autorizada, el déficit generado
                | por dicha reducción no es un déficit real del trabajador.
                |--------------------------------------------------------------------------
                */
                if ($reducedMinutes > 0) {

                    $deficitMinutes = max(
                        0,
                        abs($differenceMinutes) - $reducedMinutes
                    );

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | Déficit real del trabajador
                    |--------------------------------------------------------------------------
                    */
                    $deficitMinutes =
                        abs($differenceMinutes);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Excedente del día
            |--------------------------------------------------------------------------
            |
            | Todo tiempo trabajado por encima del horario efectivo
            | del día se considera hora extra.
            |
            */
            elseif ($differenceMinutes > 0) {

                $overtimeMinutes =
                    $differenceMinutes;
            }

            /*
            |--------------------------------------------------------------------------
            | Acumulados semanales
            |--------------------------------------------------------------------------
            */
            $totalScheduledMinutes +=
                $scheduledMinutes;

            $totalWorkedMinutes +=
                $workedMinutes;

            $days[] = [
                'date' =>
                    $date->copy(),

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
                    $attendances->isNotEmpty(),

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
                collect($days)->sum('deficit_minutes'),

            'overtime_minutes' =>
                collect($days)->sum('overtime_minutes'),

            'days' =>
                $days,
        ];
    }
}
