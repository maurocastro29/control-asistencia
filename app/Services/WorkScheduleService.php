<?php

namespace App\Services;

use App\Models\WorkSchedule;
use Illuminate\Support\Facades\DB;

class WorkScheduleService
{
    /**
     * Crear un horario junto con sus días.
     */
    public function create(array $data): WorkSchedule
    {
        return DB::transaction(function () use ($data) {

            $schedule = WorkSchedule::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'],
            ]);

            foreach ($data['days'] as $day) {

                $schedule->days()->create(
                    $this->prepareDayData($day)
                );

            }

            return $schedule->load('days.weekDay');

        });
    }

    /**
     * Actualizar un horario junto con sus días.
     */
    public function update(
        WorkSchedule $schedule,
        array $data
    ): WorkSchedule {

        return DB::transaction(function () use ($schedule, $data) {

            $schedule->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'],
            ]);

            $existingDays = $schedule->days
                ->keyBy('week_day_id');

            foreach ($data['days'] as $day) {

                $scheduleDay = $existingDays->get($day['week_day_id']);

                if ($scheduleDay) {

                    $scheduleDay->update(
                        $this->prepareDayData($day)
                    );

                } else {

                    $schedule->days()->create(
                        $this->prepareDayData($day)
                    );

                }

            }

            return $schedule->fresh()->load('days.weekDay');

        });
    }

    /**
     * Eliminar un horario.
     */
    public function delete(WorkSchedule $schedule): void
    {
        DB::transaction(function () use ($schedule) {

            $schedule->days()->delete();

            $schedule->delete();

        });
    }

    /**
     * Prepara la información de un día del horario.
     */
    private function prepareDayData(array $day): array
    {
        return [

            'week_day_id' => $day['week_day_id'],

            'entry_time' => $day['is_working_day']
                ? $day['entry_time']
                : null,

            'exit_time' => $day['is_working_day']
                ? $day['exit_time']
                : null,

            'lunch_minutes' => $day['is_working_day']
                ? $day['lunch_minutes']
                : 0,

            'ordinary_minutes' => $this->calculateOrdinaryMinutes($day),

            'is_working_day' => $day['is_working_day'],

        ];
    }

    private function calculateOrdinaryMinutes(array $day): int
    {
        if (!$day['is_working_day']) {
            return 0;
        }

        if (empty($day['entry_time']) || empty($day['exit_time'])) {
            return 0;
        }

        $entry = strtotime($day['entry_time']);
        $exit = strtotime($day['exit_time']);

        $minutes = intval(($exit - $entry) / 60);

        $minutes -= intval($day['lunch_minutes'] ?? 0);

        return max($minutes, 0);
    }
}