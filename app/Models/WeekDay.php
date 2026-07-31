<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeekDay extends Model
{
    protected $fillable = [
        'name',
        'code',
        'order',
        'is_working_day_default',
    ];

    protected function casts(): array
    {
        return [
            'is_working_day_default' => 'boolean',
        ];
    }

    public function workScheduleDays(): HasMany
    {
        return $this->hasMany(WorkScheduleDay::class);
    }
}