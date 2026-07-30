<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceRecordFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'work_date',
        'entry_time',
        'exit_time',
        'lunch_time',
        'observations',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'work_date' => 'date',
            'entry_time' => 'datetime:H:i',
            'exit_time' => 'datetime:H:i',
            'lunch_time' => 'integer',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}