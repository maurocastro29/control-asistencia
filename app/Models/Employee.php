<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeFactory> */
    use HasFactory;

    protected $fillable = [
        'document_type_id',
        'department_id',
        'position_id',
        'document_number',
        'first_name',
        'middle_name',
        'first_last_name',
        'second_last_name',
        'birth_date',
        'phone',
        'email',
        'address',
        'hire_date',
        'termination_date',
        'work_schedule_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'hire_date' => 'date',
            'termination_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => trim(
                implode(' ', array_filter([
                    $this->first_name,
                    $this->middle_name,
                    $this->first_last_name,
                    $this->second_last_name,
                ]))
            ),
        );
    }

    public function workSchedule(): BelongsTo
    {
        return $this->belongsTo(WorkSchedule::class);
    }

    public function workScheduleAdjustments()
    {
        return $this->hasMany(WorkScheduleAdjustment::class);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }
}