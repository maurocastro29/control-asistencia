<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRecordRequest;
use App\Http\Requests\UpdateAttendanceRecordRequest;
use App\Models\AttendanceRecord;
use App\Models\AttendanceType;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AttendanceRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $attendanceRecords = AttendanceRecord::with([
                'employee',
                'attendanceType',
                'createdBy',
            ])
            ->orderByDesc('attendance_datetime')
            ->paginate(15);

        return view('attendance-records.index', compact('attendanceRecords'));
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
                    ->orderBy('first_last_name')
                    ->orderBy('first_name')
                    ->get(),
                'attendanceTypes' => AttendanceType::where('is_active', true)
                    ->orderBy('id')
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
        AttendanceRecord::create($data);
        return redirect()
                    ->route('attendance-records.index')
                    ->with('success', 'Marcación registrada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AttendanceRecord $attendanceRecord): View
    {
        $attendanceRecord->load([
            'employee',
            'attendanceType',
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
                    ->get(),
                'attendanceTypes' => AttendanceType::where('is_active', true)
                    ->orderBy('id')
                    ->get(),
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

        $attendanceRecord->update($request->validated());

        return redirect()
                ->route('attendance-records.index')
                ->with('success', 'Marcación actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AttendanceRecord $attendanceRecord): RedirectResponse
    {
        return redirect()
                    ->route('attendance-records.index')
                    ->with('error', 'Las marcaciones no pueden eliminarse.');
    }
}
