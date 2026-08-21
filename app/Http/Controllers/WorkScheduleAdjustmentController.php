<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\WorkScheduleAdjustment;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
            'status' => WorkScheduleAdjustment::STATUS_PENDING,
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

    /**
     * Display the specified resource.
     */
    public function show(WorkScheduleAdjustment $workScheduleAdjustment): View
    {
        return view(
            'work-schedule-adjustments.show',
            [
                'adjustment' => $workScheduleAdjustment,
            ]
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

        if($workScheduleAdjustment->status !== WorkScheduleAdjustment::STATUS_PENDING){
            return redirect()
                ->route('work-schedule-adjustments.index')
                ->with(
                    'error',
                    'El estado actual del ajuste no permite modificaciones. (Solo se modifican los ajustes en estado Pendiente)'
                );
        }

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

    public function complete(
        WorkScheduleAdjustment $workScheduleAdjustment
    ) {
        if ($workScheduleAdjustment->status !== WorkScheduleAdjustment::STATUS_PENDING &&
            $workScheduleAdjustment->status !== WorkScheduleAdjustment::STATUS_CANCELLED) {
            return redirect()
                ->route('work-schedule-adjustments.index')
                ->with('error', 'El ajuste seleccionado no está pendiente.');
        }
        $workScheduleAdjustment->update([
            'status' => WorkScheduleAdjustment::STATUS_COMPLETED,
        ]);
        return redirect()
            ->route('work-schedule-adjustments.index')
            ->with(
                'success',
                'Ajuste de jornada marcado como cumplido correctamente.'
            );
    }

    public function canceled(
        WorkScheduleAdjustment $workScheduleAdjustment
    ) {
        if ($workScheduleAdjustment->status !== WorkScheduleAdjustment::STATUS_PENDING &&
            $workScheduleAdjustment->status !== WorkScheduleAdjustment::STATUS_COMPLETED) {
            return redirect()
                ->route('work-schedule-adjustments.index')
                ->with('error', 'El ajuste seleccionado no está pendiente.');
        }
        $workScheduleAdjustment->update([
            'status' => WorkScheduleAdjustment::STATUS_CANCELLED,
        ]);
        return redirect()
            ->route('work-schedule-adjustments.index')
            ->with(
                'success',
                'Ajuste de jornada marcado como cancelado correctamente.'
            );
    }
}