<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRecordRequest;
use App\Http\Requests\UpdateAttendanceRecordRequest;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Services\AttendanceService;
use Illuminate\Http\RedirectResponse;
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
    public function index(): View
{
    $lastWorkDate = AttendanceRecord::query()
        ->orderByDesc('work_date')
        ->value('work_date');

    $attendanceRecords = AttendanceRecord::with('employee')
        ->when($lastWorkDate, function ($query) use ($lastWorkDate) {
            $query->whereDate('work_date', $lastWorkDate);
        })
        ->orderBy('entry_time')
        ->paginate(15);

    return view('attendance-records.index', compact(
        'attendanceRecords',
        'lastWorkDate'
    ));
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
