<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRecordRequest;
use App\Http\Requests\UpdateAttendanceRecordRequest;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Services\AttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Routing\Controller;

class AttendanceRecordController extends Controller
{

    public function __construct(private readonly AttendanceService $attendanceService)
    {

        $this->middleware('permission:attendance-records.view')
            ->only(['index', 'show']);

        $this->middleware('permission:attendance-records.create')
            ->only(['create', 'store']);

        $this->middleware('permission:attendance-records.edit')
            ->only(['edit', 'update']);

        $this->middleware('permission:attendance-records.delete')
            ->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'position_id' => ['nullable', 'integer', 'exists:positions,id'],
        ]);

        $lastWorkDate = AttendanceRecord::query()
            ->orderByDesc('work_date')
            ->value('work_date');

        $hasFilters = collect($filters)->filter(fn ($value) => filled($value))->isNotEmpty();

        $attendanceRecords = AttendanceRecord::with('employee')
            ->when(!$hasFilters && $lastWorkDate, function ($query) use ($lastWorkDate) {
                $query->whereDate('work_date', $lastWorkDate);
            })
            ->when($filters['date_from'] ?? null, fn ($query, $date) =>
                $query->whereDate('work_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) =>
                $query->whereDate('work_date', '<=', $date))
            ->when($filters['employee_id'] ?? null, fn ($query, $employeeId) =>
                $query->where('employee_id', $employeeId))
            ->when($filters['department_id'] ?? null, fn ($query, $departmentId) =>
                $query->whereHas('employee', fn ($employeeQuery) =>
                    $employeeQuery->where('department_id', $departmentId)))
            ->when($filters['position_id'] ?? null, fn ($query, $positionId) =>
                $query->whereHas('employee', fn ($employeeQuery) =>
                    $employeeQuery->where('position_id', $positionId)))
            ->orderByDesc('work_date')
            ->orderBy('entry_time')
            ->paginate(15)
            ->withQueryString();

        return view('attendance-records.index', [
            'attendanceRecords' => $attendanceRecords,
            'lastWorkDate' => $lastWorkDate,
            'filters' => $filters,
            'employees' => Employee::query()
                ->orderBy('first_name')
                ->orderBy('first_last_name')
                ->get(),
            'departments' => \App\Models\Department::orderBy('name')->get(),
            'positions' => \App\Models\Position::orderBy('name')->get(),
            'hasFilters' => $hasFilters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view(
            'attendance-records.create',
            [
                'employees' => Employee::where('is_active', true)
                    ->orderBy('first_name')
                    ->orderBy('first_last_name')
                    ->get(),
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttendanceRecordRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['created_by'] = Auth::id();

        $this->attendanceService->create($data);

        return redirect()
            ->route('attendance-records.index')
            ->with('success', 'Jornada creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AttendanceRecord $attendanceRecord): View
    {
        $attendanceRecord->load([
            'employee',
            'createdBy',
        ]);

        return view('attendance-records.show', compact('attendanceRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AttendanceRecord $attendanceRecord): View
    {
        return view(
            'attendance-records.edit',
            [
                'attendanceRecord' => $attendanceRecord,
                'employees' => Employee::where('is_active', true)
                    ->orderBy('first_last_name')
                    ->orderBy('first_name')
                    ->get()
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateAttendanceRecordRequest $request,
        AttendanceRecord $attendanceRecord
    ): RedirectResponse {

        $this->attendanceService->update(
            $attendanceRecord,
            $request->validated()
        );

        return redirect()
            ->route('attendance-records.index')
            ->with('success', 'Jornada actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AttendanceRecord $attendanceRecord): RedirectResponse
    {
        return redirect()
                    ->route('attendance-records.index')
                    ->with('error', 'Las Jornadas no pueden eliminarse.');
    }
}
