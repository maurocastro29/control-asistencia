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
        // Convertir horas a minutos
        $data['lunch_minutes'] = (int) round($data['lunch_time'] * 60);

        unset($data['lunch_time']);

        // Validar jornada duplicada
        $exists = AttendanceRecord::where('employee_id', $data['employee_id'])
            ->whereDate('work_date', $data['work_date'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'work_date' => 'Ya existe una jornada registrada para este empleado en esa fecha.',
            ]);
        }

        // Calcular duración de la jornada
        $workedMinutes = $this->calculateWorkedMinutes(
            $data['entry_time'],
            $data['exit_time'],
            $data['lunch_minutes']
        );

        // Validar tiempo de almuerzo
        if ($data['lunch_minutes'] >= $workedMinutes) {
            throw ValidationException::withMessages([
                'lunch_time' => 'El tiempo de almuerzo no puede ser mayor o igual al tiempo trabajado.',
            ]);
        }

        return AttendanceRecord::create($data);
    }

    /**
     * Obtiene el horario del empleado para una fecha determinada.
     */
    public function getEmployeeScheduleForDate( Employee $employee, Carbon $date): ?WorkScheduleDay
    {
        if (!$employee->workSchedule) {
            return null;
        }

        return $employee->workSchedule
            ->days()
            ->where('week_day_id', $date->dayOfWeekIso)
            ->first();
    }

    /**
     * Calcula los minutos trabajados.
     */
    public function calculateWorkedMinutes(
        string $entryTime,
        string $exitTime,
        int $lunchMinutes
    ): int {

        $entry = Carbon::parse($entryTime);

        $exit = Carbon::parse($exitTime);

        $minutes = $entry->diffInMinutes($exit);

        return max(
            0,
            $minutes - $lunchMinutes
        );

    }

    /**
     * Calcula los minutos ordinarios.
     */
    public function calculateOrdinaryMinutes(
        int $workedMinutes,
        int $ordinaryMinutes
    ): int {

        return min(
            $workedMinutes,
            $ordinaryMinutes
        );
    }

    /**
     * Calcula minutos extras.
     */
    public function calculateOvertimeMinutes(
        int $workedMinutes,
        int $ordinaryMinutes
    ): int {

        return max(
            0,
            $workedMinutes - $ordinaryMinutes
        );
    }

    /**
     * Calcula la jornada completa de una marcación.
     */
    public function calculateAttendance(
        AttendanceRecord $attendance
    ): array {

        $scheduleDay = $this->getEmployeeScheduleForDate(
            $attendance->employee,
            Carbon::parse($attendance->work_date)
        );

        if (!$scheduleDay) {

            return [
                'worked_minutes'   => 0,
                'ordinary_minutes' => 0,
                'overtime_minutes' => 0,
                'lunch_minutes'    => 0,
                'scheduled_minutes'=> 0,
            ];

        }

        $workedMinutes = $this->calculateWorkedMinutes(
            $attendance->entry_time,
            $attendance->exit_time,
            $attendance->lunch_minutes
        );

        $ordinaryMinutes = $this->calculateOrdinaryMinutes(
            $workedMinutes,
            $scheduleDay->ordinary_minutes
        );

        $overtimeMinutes = $this->calculateOvertimeMinutes(
            $workedMinutes,
            $scheduleDay->ordinary_minutes
        );

        return [
            'employee_id' => $attendance->employee_id,
            'date' => $attendance->work_date,
            'entry_time' => $attendance->entry_time,
            'exit_time' => $attendance->exit_time,
            'lunch_minutes' => $attendance->lunch_minutes,
            'worked_minutes' => $workedMinutes,
            'scheduled_minutes' => $scheduleDay->ordinary_minutes,
            'ordinary_minutes' => $ordinaryMinutes,
            'overtime_minutes' => $overtimeMinutes,
        ];

    }

    /**
     * Actualiza una jornada de trabajo.
     */
    public function update(AttendanceRecord $attendanceRecord, array $data): AttendanceRecord
    {
        // Convertir horas de almuerzo a minutos
        $data['lunch_time'] = (int) round($data['lunch_time'] * 60);

        // Validar jornada duplicada
        $exists = AttendanceRecord::where('employee_id', $data['employee_id'])
            ->whereDate('work_date', $data['work_date'])
            ->where('id', '!=', $attendanceRecord->id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'work_date' => 'Ya existe una jornada registrada para este empleado en esa fecha.',
            ]);
        }

        // Calcular duración de la jornada
        $workedMinutes = $this->calculateWorkedMinutes(
            $data['entry_time'],
            $data['exit_time'],
            $data['lunch_minutes']
        );

        // Validar tiempo de almuerzo
        if ($data['lunch_time'] >= $workedMinutes) {
            throw ValidationException::withMessages([
                'lunch_time' => 'El tiempo de almuerzo no puede ser mayor o igual al tiempo trabajado.',
            ]);
        }

        $attendanceRecord->update($data);

        return $attendanceRecord->fresh();
    }

    /**
     * Calcula las horas extras.
     *
     * Inicialmente siempre será 0.
     * Cuando implementemos el cálculo semanal se modificará este método.
     */
    public function calculateExtraMinutes(int $workedMinutes): int
    {
        return 0;
    }

    /**
     * Convierte minutos a formato HH:MM.
     */
    public function formatMinutes(int $minutes): string
    {
        $hours = intdiv($minutes, 60);
        $minutes = $minutes % 60;

        return sprintf('%02d:%02d', $hours, $minutes);
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
}