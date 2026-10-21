<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InactivityRecord extends Model
{
    use HasFactory;

    protected $table = 'inactivity_record';

    protected $fillable = [
        'employee_id',
        'inactivity_reason_id',
        'is_paid',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_paid' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reason(): BelongsTo
    {
        return $this->belongsTo(InactivityReason::class, 'inactivity_reason_id');
    }

    public function days(): HasMany
    {
        return $this->hasMany(InactivityRecordDay::class);
    }
}