<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\WorkScheduleDay;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    /**
     * Crea una nueva jornada de trabajo.
     */
    public function create(array $data): AttendanceRecord
    {
        $lunchMinutes = $this->hoursToMinutes((float) $data['lunch_time']);

        $this->validateDuplicateAttendance(
            $data['employee_id'],
            $data['work_date']
        );

        $this->validateLunchTime(
            $data['entry_time'],
            $data['exit_time'],
            $lunchMinutes
        );

        $data['lunch_time'] = $lunchMinutes;

        return AttendanceRecord::create($data);
    }

    /**
     * Actualiza una jornada.
     */
    public function update(
        AttendanceRecord $attendanceRecord,
        array $data
    ): AttendanceRecord {
        $lunchMinutes = $this->hoursToMinutes((float) $data['lunch_time']);

        $this->validateDuplicateAttendance(
            $data['employee_id'],
            $data['work_date'],
            $attendanceRecord->id
        );

        $this->validateLunchTime(
            $data['entry_time'],
            $data['exit_time'],
            $lunchMinutes
        );

        $data['lunch_time'] = $lunchMinutes;

        $attendanceRecord->update($data);

        return $attendanceRecord->fresh();
    }

    /**
     * Obtiene el horario correspondiente a una fecha.
     */
    public function getEmployeeScheduleForDate(
        Employee $employee,
        Carbon $date
    ): ?WorkScheduleDay {
        if (!$employee->workSchedule) {
            return null;
        }

        return $employee->workSchedule
            ->days()
            ->whereHas('weekDay', function ($query) use ($date) {
                $query->where('order', $date->dayOfWeekIso);
            })
            ->first();
    }

    /**
     * Calcula los minutos brutos entre entrada y salida.
     */
    public function calculateGrossMinutes(
        string $entryTime,
        string $exitTime
    ): int {
        $entry = Carbon::parse($entryTime);
        $exit = Carbon::parse($exitTime);

        return $entry->diffInMinutes($exit);
    }

    /**
     * Calcula minutos realmente trabajados.
     */
    public function calculateWorkedMinutes(
        string $entryTime,
        string $exitTime,
        int $lunchMinutes
    ): int {
        return max(
            0,
            $this->calculateGrossMinutes($entryTime, $exitTime)
                - $lunchMinutes
        );
    }

    /**
     * Calcula minutos ordinarios.
     */
    public function calculateOrdinaryMinutes(
        int $workedMinutes,
        int $scheduledMinutes
    ): int {
        return min($workedMinutes, $scheduledMinutes);
    }

    /**
     * Calcula minutos extras diarios.
     */
    public function calculateOvertimeMinutes(
        int $workedMinutes,
        int $scheduledMinutes
    ): int {
        return max(0, $workedMinutes - $scheduledMinutes);
    }

    /**
     * Calcula toda la jornada.
     */
    public function calculateAttendance(
        AttendanceRecord $attendance
    ): array {
        $scheduleDay = $this->getEmployeeScheduleForDate(
            $attendance->employee,
            Carbon::parse($attendance->work_date)
        );

        $workedMinutes = $this->calculateWorkedMinutes(
            $attendance->entry_time->format('H:i'),
            $attendance->exit_time->format('H:i'),
            $attendance->lunch_time
        );

        if (!$scheduleDay || !$scheduleDay->is_working_day) {
            return [
                'employee_id' => $attendance->employee_id,
                'date' => $attendance->work_date,
                'entry_time' => $attendance->entry_time,
                'exit_time' => $attendance->exit_time,
                'lunch_time' => $attendance->lunch_time,
                'worked_minutes' => $workedMinutes,
                'scheduled_minutes' => 0,
                'ordinary_minutes' => 0,
                'overtime_minutes' => 0,
            ];
        }

        $scheduledMinutes = $scheduleDay->ordinary_minutes;

        $ordinaryMinutes = $this->calculateOrdinaryMinutes(
            $workedMinutes,
            $scheduledMinutes
        );

        $overtimeMinutes = $this->calculateOvertimeMinutes(
            $workedMinutes,
            $scheduledMinutes
        );

        return [
            'employee_id' => $attendance->employee_id,
            'date' => $attendance->work_date,
            'entry_time' => $attendance->entry_time,
            'exit_time' => $attendance->exit_time,
            'lunch_time' => $attendance->lunch_time,
            'worked_minutes' => $workedMinutes,
            'scheduled_minutes' => $scheduledMinutes,
            'ordinary_minutes' => $ordinaryMinutes,
            'overtime_minutes' => $overtimeMinutes,
        ];
    }

    /**
     * Valida duplicidad.
     */
    private function validateDuplicateAttendance(
        int $employeeId,
        string $workDate,
        ?int $ignoreId = null
    ): void {
        $query = AttendanceRecord::where('employee_id', $employeeId)
            ->whereDate('work_date', $workDate);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'work_date' => 'Ya existe una jornada registrada para este empleado en esa fecha.',
            ]);
        }
    }

    /**
     * Valida el tiempo de almuerzo.
     */
    private function validateLunchTime(
        string $entryTime,
        string $exitTime,
        int $lunchMinutes
    ): void {
        $grossMinutes = $this->calculateGrossMinutes(
            $entryTime,
            $exitTime
        );

        if ($lunchMinutes >= $grossMinutes) {
            throw ValidationException::withMessages([
                'lunch_time' => 'El tiempo de almuerzo no puede ser mayor o igual a la duración de la jornada.',
            ]);
        }
    }

    /**
     * Convierte horas decimales a minutos.
     */
    public function hoursToMinutes(float $hours): int
    {
        return (int) round($hours * 60);
    }

    /**
     * Convierte minutos a horas decimales.
     */
    public function minutesToHours(int $minutes): float
    {
        return round($minutes / 60, 2);
    }

    /**
     * Formatea minutos como HH:MM.
     */
    public function formatMinutes(int $minutes): string
    {
        return sprintf(
            '%02d:%02d',
            intdiv($minutes, 60),
            $minutes % 60
        );
    }
}