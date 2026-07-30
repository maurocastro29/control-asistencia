<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttendanceReportService
{
    /**
     * Minutos máximos ordinarios por semana.
     * 42 horas = 2520 minutos.
     */
    private const WEEKLY_ORDINARY_MINUTES = 2520;

    /**
     * Obtiene todas las jornadas de una semana.
     */
    public function getWeeklyRecords(Employee $employee, Carbon $date): Collection
    {
        $start = $date->copy()->startOfWeek(Carbon::MONDAY);
        $end = $date->copy()->endOfWeek(Carbon::SUNDAY);

        return AttendanceRecord::where('employee_id', $employee->id)
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('work_date')
            ->get();
    }

    /**
     * Calcula los minutos trabajados de una jornada.
     */
    public function calculateWorkedMinutes(AttendanceRecord $record): int
    {
        $entry = Carbon::parse($record->entry_time);
        $exit = Carbon::parse($record->exit_time);

        $minutes = $entry->diffInMinutes($exit);

        return max(0, $minutes - $record->lunch_time);
    }

    /**
     * Calcula el total de minutos trabajados en una semana.
     */
    public function calculateWeeklyMinutes(Collection $records): int
    {
        return $records->sum(function (AttendanceRecord $record) {
            return $this->calculateWorkedMinutes($record);
        });
    }

    /**
     * Calcula los minutos ordinarios.
     */
    public function calculateOrdinaryMinutes(Collection $records): int
    {
        return min(
            $this->calculateWeeklyMinutes($records),
            self::WEEKLY_ORDINARY_MINUTES
        );
    }

    /**
     * Calcula los minutos extras.
     */
    public function calculateExtraMinutes(Collection $records): int
    {
        $worked = $this->calculateWeeklyMinutes($records);

        return max(
            0,
            $worked - self::WEEKLY_ORDINARY_MINUTES
        );
    }

    /**
     * Convierte minutos a HH:MM.
     */
    public function formatMinutes(int $minutes): string
    {
        $hours = intdiv($minutes, 60);

        $minutes = $minutes % 60;

        return sprintf('%02d:%02d', $hours, $minutes);
    }
}