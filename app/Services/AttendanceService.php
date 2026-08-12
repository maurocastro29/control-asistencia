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
     * Crea una nueva jornada.
     */
    public function create(array $data): AttendanceRecord
    {
        $lunchMinutes = $this->hoursToMinutes(
            (float) $data['lunch_time']
        );

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
        $lunchMinutes = $this->hoursToMinutes(
            (float) $data['lunch_time']
        );

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
     * Calcula minutos brutos entre entrada y salida.
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
        return min(
            $workedMinutes,
            $scheduledMinutes
        );
    }

    /**
     * Calcula minutos extras diarios.
     */
    public function calculateOvertimeMinutes(
        int $workedMinutes,
        int $scheduledMinutes
    ): int {
        return max(
            0,
            $workedMinutes - $scheduledMinutes
        );
    }

    /**
     * Calcula toda la jornada.
     */
    public function calculateAttendance(
        AttendanceRecord $attendance
    ): array {
        $workDate = Carbon::parse($attendance->work_date);

        $scheduleDay = $this->getEmployeeScheduleForDate(
            $attendance->employee,
            $workDate
        );

        $entryTime = $attendance->entry_time->format('H:i');
        $exitTime = $attendance->exit_time->format('H:i');

        $workedMinutes = $this->calculateWorkedMinutes(
            $entryTime,
            $exitTime,
            $attendance->lunch_time
        );

        /*
        |--------------------------------------------------------------------------
        | Sin horario
        |--------------------------------------------------------------------------
        */

        if (!$scheduleDay) {
            return $this->emptyCalculation(
                $attendance,
                $workedMinutes
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Día no laboral
        |--------------------------------------------------------------------------
        */

        if (!$scheduleDay->is_working_day) {
            return $this->emptyCalculation(
                $attendance,
                $workedMinutes
            );
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

        /*
        |--------------------------------------------------------------------------
        | Clasificación de horas
        |--------------------------------------------------------------------------
        */

        $classification = $this->classifyWorkedTime(
            $attendance,
            $workDate,
            $ordinaryMinutes,
            $overtimeMinutes
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

            'hed_minutes' => $classification['hed_minutes'],
            'hen_minutes' => $classification['hen_minutes'],
            'hefd_minutes' => $classification['hefd_minutes'],
            'hefn_minutes' => $classification['hefn_minutes'],
            'rn_minutes' => $classification['rn_minutes'],
            'rnf_minutes' => $classification['rnf_minutes'],
        ];
    }

    /**
     * Clasifica el tiempo trabajado.
     *
     * Se analiza minuto por minuto para poder separar correctamente
     * jornada ordinaria, horas extras, horario nocturno y días festivos.
     */
    private function classifyWorkedTime(
        AttendanceRecord $attendance,
        Carbon $workDate,
        int $ordinaryMinutes,
        int $overtimeMinutes
    ): array {
        $result = [
            'hed_minutes' => 0,
            'hen_minutes' => 0,
            'hefd_minutes' => 0,
            'hefn_minutes' => 0,
            'rn_minutes' => 0,
            'rnf_minutes' => 0,
        ];

        $entry = $attendance->entry_time->copy();
        $exit = $attendance->exit_time->copy();

        /*
         * Si la hora de salida es menor que la entrada,
         * asumimos que la jornada termina al día siguiente.
         */
        if ($exit->lessThanOrEqualTo($entry)) {
            $exit->addDay();
        }

        /*
         * Para no contar el almuerzo como tiempo trabajado,
         * primero calculamos el intervalo de trabajo.
         *
         * Actualmente el almuerzo se almacena en minutos,
         * pero no tenemos registrada su posición exacta dentro
         * de la jornada. Por eso, para la clasificación,
         * se descontará al final del intervalo.
         */

        $totalMinutes = $entry->diffInMinutes($exit);

        $effectiveMinutes = max(
            0,
            $totalMinutes - $attendance->lunch_time
        );

        /*
         * Recorremos únicamente los minutos efectivamente trabajados.
         */
        for ($minute = 0; $minute < $effectiveMinutes; $minute++) {
            $current = $entry->copy()->addMinutes($minute);

            $isNight = $this->isNightTime($current);

            $isHoliday = $this->isSundayOrHoliday($current);

            /*
             * Los primeros minutos corresponden a jornada ordinaria.
             */
            $isOvertime = $minute >= $ordinaryMinutes;

            if ($isOvertime) {
                if ($isHoliday && $isNight) {
                    $result['hefn_minutes']++;
                } elseif ($isHoliday) {
                    $result['hefd_minutes']++;
                } elseif ($isNight) {
                    $result['hen_minutes']++;
                } else {
                    $result['hed_minutes']++;
                }

                continue;
            }

            /*
             * Tiempo ordinario nocturno.
             */
            if ($isHoliday && $isNight) {
                $result['rnf_minutes']++;
            } elseif ($isNight) {
                $result['rn_minutes']++;
            }
        }

        return $result;
    }

    /**
     * Determina si un momento pertenece al horario nocturno.
     *
     * Nocturno:
     * 19:00 - 06:00
     */
    private function isNightTime(Carbon $dateTime): bool
    {
        $minutes = (
            ((int) $dateTime->format('H')) * 60
        ) + (int) $dateTime->format('i');

        return $minutes >= (19 * 60)
            || $minutes < (6 * 60);
    }

    /**
     * Determina si la fecha corresponde a domingo.
     *
     * Los festivos nacionales se incorporarán posteriormente
     * mediante el módulo de festivos.
     */
    private function isSundayOrHoliday(Carbon $dateTime): bool
    {
        return $dateTime->isSunday();
    }

    /**
     * Resultado vacío cuando no existe una jornada programada.
     */
    private function emptyCalculation(
        AttendanceRecord $attendance,
        int $workedMinutes
    ): array {
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

            'hed_minutes' => 0,
            'hen_minutes' => 0,
            'hefd_minutes' => 0,
            'hefn_minutes' => 0,
            'rn_minutes' => 0,
            'rnf_minutes' => 0,
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
                'work_date' =>
                    'Ya existe una jornada registrada para este empleado en esa fecha.',
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
                'lunch_time' =>
                    'El tiempo de almuerzo no puede ser mayor o igual a la duración de la jornada.',
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
