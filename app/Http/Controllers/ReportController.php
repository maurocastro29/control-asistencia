<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Services\AttendanceService;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\WeeklyAttendanceService;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportController extends Controller
{

    public function __construct(
        private readonly AttendanceService $attendanceService,
        private readonly WeeklyAttendanceService $weeklyAttendanceService
    ) {
        $this->middleware('permission:reports.view');
    }

    /**
     * Reporte de asistencia.
     */
    public function attendance(Request $request): View
    {
        $filters = [
            'date_from' => $request->input(
                'date_from',
                now()->startOfMonth()->format('Y-m-d')
            ),
            'date_to' => $request->input(
                'date_to',
                now()->format('Y-m-d')
            ),
            'employee_id' => $request->input('employee_id'),
            'department_id' => $request->input('department_id'),
            'position_id' => $request->input('position_id'),
        ];

        $dateFrom = Carbon::parse($filters['date_from']);
        $dateTo = Carbon::parse($filters['date_to']);

        /*
        |--------------------------------------------------------------------------
        | Obtener empleados
        |--------------------------------------------------------------------------
        */

        $employees = Employee::query()
            ->with([
                'department',
                'position',
                'workSchedule.days.weekDay',
                'attendanceRecords',
            ])
            ->where('is_active', true)

            ->when(
                $filters['employee_id'],
                fn ($query, $employeeId) =>
                    $query->where('id', $employeeId)
            )

            ->when(
                $filters['department_id'],
                fn ($query, $departmentId) =>
                    $query->where('department_id', $departmentId)
            )

            ->when(
                $filters['position_id'],
                fn ($query, $positionId) =>
                    $query->where('position_id', $positionId)
            )

            ->orderBy('first_name')
            ->orderBy('first_last_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Construir jornadas
        |--------------------------------------------------------------------------
        */

        $reportRecords = collect();

        foreach ($employees as $employee) {

            $records = $this->attendanceService->buildReportRecords(
                $employee,
                $dateFrom,
                $dateTo
            );

            foreach ($records as $record) {
                $reportRecords->push($record);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Resumen
        |--------------------------------------------------------------------------
        */

        $totalWorkedMinutes = $reportRecords->sum(
            'worked_minutes'
        );

        $totalOrdinaryMinutes = $reportRecords->sum(
            'ordinary_minutes'
        );

        $totalOvertimeMinutes = $reportRecords->sum(
            'overtime_minutes'
        );

        /*
        |--------------------------------------------------------------------------
        | Horas adicionales
        |--------------------------------------------------------------------------
        */

        $totalHedMinutes = $reportRecords->sum('hed_minutes');
        $totalHenMinutes = $reportRecords->sum('hen_minutes');
        $totalHefdMinutes = $reportRecords->sum('hefd_minutes');
        $totalHefnMinutes = $reportRecords->sum('hefn_minutes');

        $totalRnMinutes = $reportRecords->sum('rn_minutes');
        $totalRnfMinutes = $reportRecords->sum('rnf_minutes');

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
        | Paginar resultados
        |--------------------------------------------------------------------------
        */

        $page = $request->integer('page', 1);
        $perPage = 15;

        $attendanceRecords = new \Illuminate\Pagination\LengthAwarePaginator(
            $reportRecords->forPage($page, $perPage)->values(),
            $reportRecords->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Opciones de filtros
        |--------------------------------------------------------------------------
        */

        $departments = Department::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $positions = Position::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'reports.attendance',
            compact(
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
                'overtimeTimeFormatted',
                'totalHedMinutes',
                'totalHenMinutes',
                'totalHefdMinutes',
                'totalHefnMinutes',
                'totalRnMinutes',
                'totalRnfMinutes',
            )
        );
    }

    /**
     * Exporta el reporte de asistencia a Excel.
     */
    public function exportAttendance(Request $request)
    {
        $filters = [
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'employee_id' => $request->input('employee_id'),
            'department_id' => $request->input('department_id'),
            'position_id' => $request->input('position_id'),
        ];

        $fileName = 'reporte-asistencia-' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(
            new AttendanceExport(
                $filters,
                $this->attendanceService
            ),
            $fileName
        );
    }

}