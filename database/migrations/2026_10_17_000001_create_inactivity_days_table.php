<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inactivity_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inactivity_reason_id')
                ->constrained('inactivity_reasons')
                ->restrictOnDelete();
            $table->date('date');
            $table->boolean('is_paid');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['employee_id', 'date', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inactivity_days');
    }
};