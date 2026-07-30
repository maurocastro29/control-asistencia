<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRecordRequest;
use App\Models\AttendanceRecord;
use App\Models\AttendanceType;
use App\Models\Employee;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AttendanceController extends Controller
{

    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }
    /**
     * Muestra la pantalla de registro de asistencia.
     */
    public function index(): View
    {
        return view('attendance.register', [
            'employees' => collect(),
            'selectedEmployee' => null,
        ]);
    }

    /**
     * Busca un empleado por número de documento.
     */
    public function search(Request $request): View
    {
        $request->validate([
            'search' => 'required|string|max:100',
        ]);

        $search = trim($request->search);

        $employees = Employee::with([
                'department',
                'position',
                'documentType',
            ])
            ->where('is_active', true)
            ->where(function ($query) use ($search) {

                $query->where('document_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('first_last_name', 'like', "%{$search}%")
                    ->orWhere('second_last_name', 'like', "%{$search}%");

            })
            ->orderBy('first_last_name')
            ->orderBy('first_name')
            ->get();

        return view('attendance.register', [
            'employees' => $employees,
            'selectedEmployee' => null,
        ]);
    }

    public function select(Employee $employee): View
    {
        $employee->load([
            'department',
            'position',
            'documentType',
        ]);

        return view('attendance.register', [
            'employees' => collect(),
            'selectedEmployee' => $employee,
        ]);
    }


    /**
     * Registra la marcación.
     */
    /**
     * Registra una jornada de trabajo.
     */
    public function store(StoreAttendanceRecordRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['created_by'] = Auth::id();

        $this->attendanceService->create($data);

        return redirect()
            ->route('attendance.register')
            ->with('success', 'Jornada registrada correctamente.');
    }
}