<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkScheduleDay extends Model
{
    protected $fillable = [
        'work_schedule_id',
        'week_day_id',
        'entry_time',
        'exit_time',
        'lunch_minutes',
        'ordinary_minutes',
        'is_working_day',
    ];

    protected function casts(): array
    {
        return [
            'entry_time' => 'datetime:H:i',
            'exit_time' => 'datetime:H:i',
            'is_working_day' => 'boolean',
        ];
    }

    public function workSchedule(): BelongsTo
    {
        return $this->belongsTo(WorkSchedule::class);
    }

    public function weekDay(): BelongsTo
    {
        return $this->belongsTo(WeekDay::class);
    }
}