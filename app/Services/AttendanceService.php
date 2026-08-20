<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\WorkScheduleAdjustment;
use App\Models\WorkScheduleDay;
use Carbon\Carbon;
use Illuminate\Support\Collection;
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
            $data['work_date'],
            $data['entry_time'],
            $data['exit_time']
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
            $data['entry_time'],
            $data['exit_time'],
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
     * Valida que la jornada no se solape con otra jornada
     * del mismo empleado en la misma fecha.
     */
    private function validateDuplicateAttendance(
        int $employeeId,
        string $workDate,
        string $entryTime,
        string $exitTime,
        ?int $ignoreId = null
    ): void {
        $query = AttendanceRecord::query()
            ->where('employee_id', $employeeId)
            ->whereDate('work_date', $workDate);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $existingRecords = $query->get();

        $newEntry = Carbon::parse($entryTime);
        $newExit = Carbon::parse($exitTime);

        /*
        * Si la jornada termina después de medianoche,
        * la salida pertenece al día siguiente.
        */
        if ($newExit->lessThanOrEqualTo($newEntry)) {
            $newExit->addDay();
        }

        foreach ($existingRecords as $existingRecord) {
            $existingEntry = $existingRecord->entry_time->copy();
            $existingExit = $existingRecord->exit_time->copy();

            if ($existingExit->lessThanOrEqualTo($existingEntry)) {
                $existingExit->addDay();
            }

            /*
            * Existe solapamiento cuando:
            *
            * nueva entrada < salida existente
            * Y
            * nueva salida > entrada existente
            */
            $hasOverlap =
                $newEntry->lessThan($existingExit)
                && $newExit->greaterThan($existingEntry);

            if ($hasOverlap) {
                throw ValidationException::withMessages([
                    'entry_time' =>
                        'La jornada se cruza con otra jornada registrada para este empleado en esa fecha.',
                ]);
            }
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

    /**
     * Construye la información de asistencia de un empleado
     * para un rango de fechas.
     */
    public function buildReportRecords(
        Employee $employee,
        Carbon $dateFrom,
        Carbon $dateTo
    ): array {
        $calculationFrom = $dateFrom->copy()->startOfWeek(Carbon::MONDAY);
        $attendances = AttendanceRecord::query()
            ->where('employee_id', $employee->id)
            ->whereBetween('work_date', [
                $calculationFrom->copy()->subDay()->toDateString(),
                $dateTo->toDateString(),
            ])
            ->orderBy('work_date')
            ->orderBy('entry_time')
            ->get()
            ->groupBy(fn (AttendanceRecord $record) => $record->work_date->format('Y-m-d'));

        $adjustments = WorkScheduleAdjustment::query()
            ->where('employee_id', $employee->id)
            ->where('is_active', true)
            ->where('status', WorkScheduleAdjustment::STATUS_COMPLETED)
            ->where(function ($query) use ($calculationFrom, $dateTo) {
                $query->whereBetween('adjustment_date', [
                    $calculationFrom->toDateString(),
                    $dateTo->toDateString(),
                ])->orWhereBetween('compensation_date', [
                    $calculationFrom->toDateString(),
                    $dateTo->toDateString(),
                ]);
            })
            ->get();

        $holidays = Holiday::query()
            ->where('is_active', true)
            ->whereBetween('date', [
                $calculationFrom->toDateString(),
                $dateTo->toDateString(),
            ])
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->flip();

        $report = [];
        $weeklyBudget = $employee->workSchedule?->weekly_minutes ?? 2520;
        for ($date = $calculationFrom->copy(); $date->lte($dateTo); $date->addDay()) {
            if ($date->isMonday() && !$date->isSameDay($calculationFrom)) {
                $weeklyBudget = $employee->workSchedule?->weekly_minutes ?? 2520;
            }
            $dateKey = $date->toDateString();
            $dayRecords = $attendances->get($dateKey, collect());
            $overnightRecords = $attendances
                ->get($date->copy()->subDay()->toDateString(), collect())
                ->filter(fn (AttendanceRecord $record) =>
                    $record->exit_time->format('H:i') <= $record->entry_time->format('H:i'));
            $dayRecords = $dayRecords->merge($overnightRecords)->unique('id')->values();
            $scheduleDay = $this->getEmployeeScheduleForDate($employee, $date);
            $dayResult = $this->calculateReportDay(
                $employee,
                $date,
                $dayRecords,
                $scheduleDay,
                $adjustments,
                $holidays->has($dateKey),
                $weeklyBudget
            );

            if ($dayRecords->isEmpty()) {
                if ($holidays->has($dateKey)) {
                    $weeklyBudget = max(
                        0,
                        $weeklyBudget - $dayResult['scheduled_minutes']
                    );
                }
                continue;
            }

            $dayOrdinaryMinutes = 0;
            foreach ($dayRecords as $attendance) {
                $part = $dayResult['by_attendance'][$attendance->id] ?? $this->emptyTimePart();
                $dayOrdinaryMinutes += $part['ordinary_minutes'];
                if ($date->greaterThanOrEqualTo($dateFrom)) {
                    $report[] = $this->reportRow(
                        $employee,
                        $date,
                        $attendance,
                        $scheduleDay,
                        $dayResult,
                        $part['worked_minutes'],
                        $part['ordinary_minutes'],
                        $part
                    );
                }
            }
            $weeklyBudget = max(0, $weeklyBudget - $dayOrdinaryMinutes);
        }

        return $report;
    }

    private function calculateReportDay(
        Employee $employee,
        Carbon $date,
        Collection $attendances,
        ?WorkScheduleDay $scheduleDay,
        Collection $adjustments,
        bool $isHoliday,
        int $weeklyBudget
    ): array {
        $baseMinutes = $scheduleDay?->is_working_day
            ? $scheduleDay->ordinary_minutes
            : 0;
        $reducedMinutes = $adjustments
            ->filter(fn (WorkScheduleAdjustment $adjustment) =>
                $adjustment->adjustment_date?->isSameDay($date))
            ->sum('reduced_minutes');
        $compensationMinutes = $adjustments
            ->filter(fn (WorkScheduleAdjustment $adjustment) =>
                $adjustment->compensation_date?->isSameDay($date))
            ->sum('reduced_minutes');
        $scheduledMinutes = min(
            $weeklyBudget,
            max(0, $baseMinutes - $reducedMinutes + $compensationMinutes)
        );
        $segments = $this->attendanceSegmentsForDate($attendances, $date);
        $parts = [];
        $workedMinutes = 0;
        foreach ($segments as $segment) {
            $parts[$segment['attendance']->id] ??= $this->emptyTimePart();
            $parts[$segment['attendance']->id]['worked_minutes'] += $segment['minutes'];
            $workedMinutes += $segment['minutes'];
        }

        $scheduleWindow = $this->scheduleWindowForDate($employee, $date, $scheduleDay);
        $ordinaryRemaining = $scheduledMinutes;
        $normalOrdinaryRemaining = max(0, $baseMinutes - $reducedMinutes);
        $result = [
            'scheduled_minutes' => $scheduledMinutes,
            'worked_minutes' => $workedMinutes,
            'ordinary_minutes' => 0,
            'by_attendance' => $parts,
        ];

        foreach ($segments as $segment) {
            $current = $segment['start']->copy();
            for ($minute = 0; $minute < $segment['minutes']; $minute++) {
                $insideSchedule = $scheduleWindow
                    && $current->greaterThanOrEqualTo($scheduleWindow[0])
                    && $current->lessThan($scheduleWindow[1]);
                $afterSchedule = $scheduleWindow
                    && $current->greaterThanOrEqualTo($scheduleWindow[1]);
                $isOrdinary = false;

                if (!$isHoliday && $ordinaryRemaining > 0) {
                    $isOrdinary = $insideSchedule
                        || ($afterSchedule && $normalOrdinaryRemaining <= 0)
                        || (!$scheduleWindow && $normalOrdinaryRemaining <= 0)
                        || ($scheduleWindow
                            && $current->lessThan($scheduleWindow[0])
                            && $normalOrdinaryRemaining > 0);
                }

                if ($isHoliday) {
                    $category = $this->isNightTime($current) ? 'hefn_minutes' : 'hefd_minutes';
                } elseif ($isOrdinary) {
                    $ordinaryRemaining--;
                    if ($normalOrdinaryRemaining > 0) {
                        $normalOrdinaryRemaining--;
                    }
                    $category = $current->isSunday()
                        ? ($this->isNightTime($current) ? 'rnf_minutes' : 'rn_minutes')
                        : null;
                } else {
                    $category = $current->isSunday()
                        ? ($this->isNightTime($current) ? 'hefn_minutes' : 'hefd_minutes')
                        : ($this->isNightTime($current) ? 'hen_minutes' : 'hed_minutes');
                }

                $part = &$result['by_attendance'][$segment['attendance']->id];
                if ($isOrdinary) {
                    $part['ordinary_minutes']++;
                    $result['ordinary_minutes']++;
                } else {
                    $part['overtime_minutes']++;
                }
                if ($category) {
                    $part[$category]++;
                }
                unset($part);
                $current->addMinute();
            }
        }

        return $result;
    }

    private function attendanceSegmentsForDate(Collection $attendances, Carbon $date): array
    {
        $segments = [];
        foreach ($attendances as $attendance) {
            $start = $date->copy()->setTimeFromTimeString($attendance->entry_time->format('H:i'));
            $end = $date->copy()->setTimeFromTimeString($attendance->exit_time->format('H:i'));
            if ($end->lessThanOrEqualTo($start)) {
                $end->addDay();
            }
            $end->subMinutes($attendance->lunch_time);
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $dayStart->copy()->addDay();
            $segmentStart = $start->max($dayStart);
            $segmentEnd = $end->min($dayEnd);
            if ($segmentEnd->greaterThan($segmentStart)) {
                $segments[] = [
                    'attendance' => $attendance,
                    'start' => $segmentStart,
                    'minutes' => $segmentStart->diffInMinutes($segmentEnd),
                ];
            }
        }
        return $segments;
    }

    private function scheduleWindowForDate(
        Employee $employee,
        Carbon $date,
        ?WorkScheduleDay $scheduleDay
    ): ?array {
        if (!$scheduleDay?->is_working_day || !$scheduleDay->entry_time || !$scheduleDay->exit_time) {
            $previousDay = $date->copy()->subDay();
            $previousSchedule = $this->getEmployeeScheduleForDate($employee, $previousDay);
            if (!$previousSchedule?->is_working_day || !$previousSchedule->entry_time || !$previousSchedule->exit_time) {
                return null;
            }
            $start = $previousDay->copy()->setTimeFromTimeString($previousSchedule->entry_time->format('H:i'));
            $end = $previousDay->copy()->setTimeFromTimeString($previousSchedule->exit_time->format('H:i'));
            if ($end->lessThanOrEqualTo($start)) {
                $end->addDay();
            }
            return [$start->max($date->copy()->startOfDay()), $end];
        }

        $start = $date->copy()->setTimeFromTimeString($scheduleDay->entry_time->format('H:i'));
        $end = $date->copy()->setTimeFromTimeString($scheduleDay->exit_time->format('H:i'));
        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay();
        }
        return [$start, $end];
    }

    private function emptyTimePart(): array
    {
        return array_fill_keys([
            'worked_minutes', 'ordinary_minutes', 'overtime_minutes',
            'hed_minutes', 'hen_minutes', 'hefd_minutes', 'hefn_minutes',
            'rn_minutes', 'rnf_minutes',
        ], 0);
    }

    private function reportRow(
        Employee $employee,
        Carbon $date,
        ?AttendanceRecord $attendance,
        ?WorkScheduleDay $scheduleDay,
        array $dayResult,
        int $workedMinutes,
        int $ordinaryMinutes,
        ?array $part = null
    ): array {
        $part ??= $this->emptyTimePart();
        return array_merge([
            'employee' => $employee,
            'work_date' => $date->copy(),
            'attendance' => $attendance,
            'schedule_day' => $scheduleDay,
            'scheduled_minutes' => $dayResult['scheduled_minutes'],
            'worked_minutes' => $workedMinutes,
            'ordinary_minutes' => $ordinaryMinutes,
            'overtime_minutes' => $part['overtime_minutes'],
            'missing_minutes' => max(0, $dayResult['scheduled_minutes'] - $dayResult['worked_minutes']),
            'status' => $attendance ? 'registered' : 'not_registered',
        ], array_intersect_key($part, array_flip([
            'hed_minutes', 'hen_minutes', 'hefd_minutes', 'hefn_minutes', 'rn_minutes', 'rnf_minutes',
        ])));
    }
}