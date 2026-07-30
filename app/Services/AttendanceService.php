<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    /**
     * Crea una nueva jornada de trabajo.
     */
    public function create(array $data): AttendanceRecord
    {
        // Convertir horas de almuerzo a minutos
        $data['lunch_time'] = (int) round($data['lunch_time'] * 60);

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
            $data['exit_time']
        );

        // Validar tiempo de almuerzo
        if ($data['lunch_time'] >= $workedMinutes) {
            throw ValidationException::withMessages([
                'lunch_time' => 'El tiempo de almuerzo no puede ser mayor o igual al tiempo trabajado.',
            ]);
        }

        return AttendanceRecord::create($data);
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
            $data['exit_time']
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
     * Calcula los minutos trabajados descontando el almuerzo.
     */
    public function calculateWorkedMinutes(
        string $entryTime,
        string $exitTime,
        int $lunchMinutes = 0
    ): int {

        $minutes = Carbon::parse($entryTime)
            ->diffInMinutes(Carbon::parse($exitTime));

        return max(0, $minutes - $lunchMinutes);
    }

    /**
     * Calcula las horas ordinarias.
     *
     * Por ahora devuelve todo el tiempo trabajado.
     * Más adelante se ajustará con el cálculo semanal.
     */
    public function calculateOrdinaryMinutes(int $workedMinutes): int
    {
        return $workedMinutes;
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