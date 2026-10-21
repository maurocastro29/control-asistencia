<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InactivityRecordDay extends Model
{
    protected $fillable = [
        'date',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function record(): BelongsTo
    {
        return $this->belongsTo(InactivityRecord::class, 'inactivity_record_id');
    }
}