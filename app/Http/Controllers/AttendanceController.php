<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\AttendanceType;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Muestra la pantalla de registro de asistencia.
     */
    public function index(): View
    {
        return view('attendance.register', [
            'attendanceTypes' => AttendanceType::where('is_active', true)
                ->orderBy('id')
                ->get(),

            'employee' => null,
        ]);
    }

    /**
     * Busca un empleado por número de documento.
     */
    public function search(Request $request): View|RedirectResponse
    {
        $request->validate([
            'document_number' => 'required|string|max:20',
        ]);

        $employee = Employee::with([
                'department',
                'position',
                'documentType',
            ])
            ->where('document_number', $request->document_number)
            ->where('is_active', true)
            ->first();

        if (!$employee) {
            return redirect()
                ->route('attendance.register')
                ->withInput()
                ->with('error', 'No se encontró ningún empleado con ese número de documento.');
        }

        return view('attendance.register', [
            'attendanceTypes' => AttendanceType::where('is_active', true)
                ->orderBy('id')
                ->get(),

            'employee' => $employee,
        ]);
    }


    /**
     * Registra la marcación.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'attendance_type_id' => 'required|exists:attendance_types,id',
            'observations' => 'nullable|string|max:255',
        ]);

        AttendanceRecord::create([
            'employee_id' => $request->employee_id,
            'attendance_type_id' => $request->attendance_type_id,
            'attendance_datetime' => now(),
            'observations' => $request->observations,
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('attendance.register')
            ->with('success', 'Marcación registrada correctamente.');
    }
}
