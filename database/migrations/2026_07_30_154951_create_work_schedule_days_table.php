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
        Schema::create('work_schedule_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_schedule_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('week_day_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->time('entry_time')->nullable();
            $table->time('exit_time')->nullable();
            $table->unsignedSmallInteger('lunch_minutes')->default(60);
            $table->unsignedSmallInteger('ordinary_minutes')->default(0);
            $table->boolean('is_working_day')->default(true);
            $table->timestamps();
            $table->unique([
                'work_schedule_id',
                'week_day_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_schedule_days');
    }
};