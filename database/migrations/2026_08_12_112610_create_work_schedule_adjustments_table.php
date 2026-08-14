<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('work_schedule_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();
            $table->date('adjustment_date');
            $table->unsignedInteger('reduced_minutes');
            $table->date('compensation_date')
                ->nullable();
            $table->text('reason')
                ->nullable();
            $table->string('status')
                ->default('pending');
            $table->boolean('is_active')
                ->default(true);
            $table->timestamps();
            $table->index([
                'employee_id',
                'adjustment_date',
            ]);
            $table->index([
                'employee_id',
                'compensation_date',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_schedule_adjustments');
    }
};