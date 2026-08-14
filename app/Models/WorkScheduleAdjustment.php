<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkScheduleAdjustment extends Model
{
    protected $fillable = [
        'employee_id',
        'adjustment_date',
        'reduced_minutes',
        'compensation_date',
        'reason',
        'status',
        'is_active',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
        'compensation_date' => 'date',
        'is_active' => 'boolean',
        'reduced_minutes' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
