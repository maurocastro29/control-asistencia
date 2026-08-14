<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\WorkScheduleAdjustment;
use Illuminate\Http\Request;

class WorkScheduleAdjustmentController extends Controller
{
    public function index()
    {
        $adjustments = WorkScheduleAdjustment::query()
            ->with('employee')
            ->orderByDesc('adjustment_date')
            ->paginate(15);

        return view(
            'work-schedule-adjustments.index',
            [
                'adjustments' => $adjustments,
            ]
        );
    }

    public function create()
    {
        $employees = Employee::query()
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get();

        return view(
            'work-schedule-adjustments.create',
            [
                'employees' => $employees,
            ]
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => [
                'required',
                'exists:employees,id',
            ],

            'adjustment_date' => [
                'required',
                'date',
            ],

            'reduced_minutes' => [
                'required',
                'integer',
                'min:1',
            ],

            'compensation_date' => [
                'required',
                'date',
            ],

            'reason' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        WorkScheduleAdjustment::create([
            'employee_id' => $validated['employee_id'],
            'adjustment_date' => $validated['adjustment_date'],
            'reduced_minutes' => $validated['reduced_minutes'],
            'compensation_date' => $validated['compensation_date'],
            'reason' => $validated['reason'] ?? null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('work-schedule-adjustments.index')
            ->with(
                'success',
                'Ajuste de jornada creado correctamente.'
            );
    }

    public function edit(
        WorkScheduleAdjustment $workScheduleAdjustment
    ) {
        $employees = Employee::query()
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get();

        return view(
            'work-schedule-adjustments.edit',
            [
                'adjustment' => $workScheduleAdjustment,
                'employees' => $employees,
            ]
        );
    }

    public function update(
        Request $request,
        WorkScheduleAdjustment $workScheduleAdjustment
    ) {
        $validated = $request->validate([
            'employee_id' => [
                'required',
                'exists:employees,id',
            ],

            'adjustment_date' => [
                'required',
                'date',
            ],

            'reduced_minutes' => [
                'required',
                'integer',
                'min:1',
            ],

            'compensation_date' => [
                'nullable',
                'date',
                'after_or_equal:adjustment_date',
            ],

            'reason' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $workScheduleAdjustment->update([
            'employee_id' => $validated['employee_id'],
            'adjustment_date' => $validated['adjustment_date'],
            'reduced_minutes' => $validated['reduced_minutes'],
            'compensation_date' => $validated['compensation_date'],
            'reason' => $validated['reason'] ?? null,
        ]);

        return redirect()
            ->route('work-schedule-adjustments.index')
            ->with(
                'success',
                'Ajuste de jornada actualizado correctamente.'
            );
    }

    public function destroy(
        WorkScheduleAdjustment $workScheduleAdjustment
    ) {
        $workScheduleAdjustment->update([
            'is_active' => false,
            'status' => 'cancelled',
        ]);

        return redirect()
            ->route('work-schedule-adjustments.index')
            ->with(
                'success',
                'Ajuste de jornada cancelado correctamente.'
            );
    }
}