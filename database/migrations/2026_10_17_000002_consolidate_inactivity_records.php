<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inactivity_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inactivity_reason_id')
                ->constrained('inactivity_reasons')
                ->restrictOnDelete();
            $table->boolean('is_paid');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['employee_id', 'is_active']);
        });

        Schema::create('inactivity_record_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inactivity_record_id')
                ->constrained('inactivity_records')
                ->cascadeOnDelete();
            $table->date('date');
            $table->timestamps();
            $table->unique(['inactivity_record_id', 'date']);
        });

        DB::table('inactivity_days')
            ->orderBy('id')
            ->chunkById(100, function ($legacyDays) {
                foreach ($legacyDays as $legacyDay) {
                    $recordId = DB::table('inactivity_records')->insertGetId([
                        'employee_id' => $legacyDay->employee_id,
                        'inactivity_reason_id' => $legacyDay->inactivity_reason_id,
                        'is_paid' => $legacyDay->is_paid,
                        'is_active' => $legacyDay->is_active,
                        'created_at' => $legacyDay->created_at,
                        'updated_at' => $legacyDay->updated_at,
                    ]);

                    DB::table('inactivity_record_days')->insert([
                        'id' => $legacyDay->id,
                        'inactivity_record_id' => $recordId,
                        'date' => $legacyDay->date,
                        'created_at' => $legacyDay->created_at,
                        'updated_at' => $legacyDay->updated_at,
                    ]);
                }
            });

        Schema::drop('inactivity_days');
    }

    public function down(): void
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

        DB::table('inactivity_records')
            ->orderBy('id')
            ->chunkById(100, function ($records) {
                foreach ($records as $record) {
                    $days = DB::table('inactivity_record_days')
                        ->where('inactivity_record_id', $record->id)
                        ->orderBy('date')
                        ->get();

                    foreach ($days as $day) {
                        DB::table('inactivity_days')->insert([
                            'id' => $day->id,
                            'employee_id' => $record->employee_id,
                            'inactivity_reason_id' => $record->inactivity_reason_id,
                            'date' => $day->date,
                            'is_paid' => $record->is_paid,
                            'is_active' => $record->is_active,
                            'created_at' => $day->created_at,
                            'updated_at' => $day->updated_at,
                        ]);
                    }
                }
            });

        Schema::drop('inactivity_record_days');
        Schema::drop('inactivity_records');
    }
};