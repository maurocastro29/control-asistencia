<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkScheduleRequest;
use App\Http\Requests\UpdateWorkScheduleRequest;
use App\Models\WeekDay;
use App\Models\WorkSchedule;
use App\Services\WorkScheduleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkScheduleController extends Controller
{
    protected WorkScheduleService $workScheduleService;

    public function __construct(WorkScheduleService $workScheduleService)
    {
        $this->workScheduleService = $workScheduleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $workSchedules = WorkSchedule::withCount('employees')
            ->latest()
            ->paginate(15);

        return view(
            'work-schedules.index',
            compact('workSchedules')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $weekDays = WeekDay::orderBy('order')->get();

        return view('work-schedules.create', compact('weekDays'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkScheduleRequest $request): RedirectResponse
    {
        $this->workScheduleService->create(
            $request->validated()
        );

        return redirect()
            ->route('work-schedules.index')
            ->with('success', 'Horario creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkSchedule $workSchedule): View
    {
        $workSchedule->load('days.weekDay');

        return view('work-schedules.show', compact('workSchedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkSchedule $workSchedule): View
    {
        $workSchedule->load('days.weekDay');

        $weekDays = WeekDay::orderBy('order')->get();

        return view('work-schedules.edit', compact(
            'workSchedule',
            'weekDays'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateWorkScheduleRequest $request,
        WorkSchedule $workSchedule
    ): RedirectResponse {

        $this->workScheduleService->update(
            $workSchedule,
            $request->validated()
        );

        return redirect()
            ->route('work-schedules.index')
            ->with('success', 'Horario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        WorkSchedule $workSchedule
    ): RedirectResponse {

        if ($workSchedule->employees()->exists()) {

            return back()->with(
                'error',
                'No es posible eliminar el horario porque está asignado a uno o más empleados.'
            );
        }

        $this->workScheduleService->delete($workSchedule);

        return redirect()
            ->route('work-schedules.index')
            ->with('success', 'Horario eliminado correctamente.');
    }
}