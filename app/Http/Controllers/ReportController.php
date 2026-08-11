<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceReportRequest;
use Illuminate\Http\Request;
use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Services\AttendanceService;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class ReportController extends Controller
{

     public function __construct(
        private readonly AttendanceService $attendanceService
    ) {
        $this->middleware('permission:reports.view');
    }

    /**
     * Reporte de asistencia.
     */
    public function attendance(Request $request): View
{
    $filters = [
        'date_from' => $request->input('date_from'),
        'date_to' => $request->input('date_to'),
        'employee_id' => $request->input('employee_id'),
        'department_id' => $request->input('department_id'),
        'position_id' => $request->input('position_id'),
    ];

    /*
    |--------------------------------------------------------------------------
    | Consulta principal
    |--------------------------------------------------------------------------
    */

    $query = AttendanceRecord::query()
        ->with([
            'employee.department',
            'employee.position',
            'employee.workSchedule',
        ])

        ->when($filters['date_from'], function ($query, $date) {
            $query->whereDate('work_date', '>=', $date);
        })

        ->when($filters['date_to'], function ($query, $date) {
            $query->whereDate('work_date', '<=', $date);
        })

        ->when($filters['employee_id'], function ($query, $employeeId) {
            $query->where('employee_id', $employeeId);
        })

        ->when($filters['department_id'], function ($query, $departmentId) {
            $query->whereHas('employee', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            });
        })

        ->when($filters['position_id'], function ($query, $positionId) {
            $query->whereHas('employee', function ($query) use ($positionId) {
                $query->where('position_id', $positionId);
            });
        });

    /*
    |--------------------------------------------------------------------------
    | Calcular resumen
    |--------------------------------------------------------------------------
    */

    $summaryRecords = (clone $query)->get();

    $totalWorkedMinutes = 0;
    $totalOrdinaryMinutes = 0;
    $totalOvertimeMinutes = 0;

    foreach ($summaryRecords as $attendanceRecord) {

        $calculation = $this->attendanceService
            ->calculateAttendance($attendanceRecord);

        $totalWorkedMinutes += $calculation['worked_minutes'];

        $totalOrdinaryMinutes += $calculation['ordinary_minutes'];

        $totalOvertimeMinutes += $calculation['overtime_minutes'];
    }

    /*
    |--------------------------------------------------------------------------
    | Formatear resumen
    |--------------------------------------------------------------------------
    */

    $workedTimeFormatted = $this->attendanceService
        ->formatMinutes($totalWorkedMinutes);

    $ordinaryTimeFormatted = $this->attendanceService
        ->formatMinutes($totalOrdinaryMinutes);

    $overtimeTimeFormatted = $this->attendanceService
        ->formatMinutes($totalOvertimeMinutes);

    /*
    |--------------------------------------------------------------------------
    | Resultados paginados
    |--------------------------------------------------------------------------
    */

    $attendanceRecords = $query
        ->orderByDesc('work_date')
        ->orderBy('entry_time')
        ->paginate(15)
        ->withQueryString();

    /*
    |--------------------------------------------------------------------------
    | Calcular información de cada jornada para la tabla
    |--------------------------------------------------------------------------
    */

    $attendanceRecords->getCollection()->transform(function ($attendanceRecord) {

        $calculation = $this->attendanceService
            ->calculateAttendance($attendanceRecord);

        $attendanceRecord->worked_minutes = $calculation['worked_minutes'];
        $attendanceRecord->ordinary_minutes = $calculation['ordinary_minutes'];
        $attendanceRecord->overtime_minutes = $calculation['overtime_minutes'];

        return $attendanceRecord;
    });

    /*
    |--------------------------------------------------------------------------
    | Opciones de filtros
    |--------------------------------------------------------------------------
    */

    $employees = Employee::query()
        ->where('is_active', true)
        ->orderBy('first_name')
        ->orderBy('first_last_name')
        ->get();

    $departments = Department::query()
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    $positions = Position::query()
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    return view('reports.attendance', compact(
        'attendanceRecords',
        'employees',
        'departments',
        'positions',
        'filters',
        'totalWorkedMinutes',
        'totalOrdinaryMinutes',
        'totalOvertimeMinutes',
        'workedTimeFormatted',
        'ordinaryTimeFormatted',
        'overtimeTimeFormatted'
    ));
}
}