<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\Holiday;
use App\Services\AttendanceService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService
    ) {
    }

    public function index(): View
    {
        $today = now()->startOfDay();
        $monthStart = $today->copy()->startOfMonth();

        $employees = Employee::query()
            ->where('is_active', true)
            ->with(['workSchedule.days.weekDay', 'department'])
            ->orderBy('first_name')
            ->orderBy('first_last_name')
            ->get();

        $todayIsHoliday = Holiday::query()
            ->where('is_active', true)
            ->whereDate('date', $today)
            ->exists();

        $expectedToday = $employees->filter(function (Employee $employee) use ($today, $todayIsHoliday) {
            return !$todayIsHoliday
                && $this->attendanceService->getEmployeeScheduleForDate($employee, $today)?->is_working_day;
        });

        $todayRecords = AttendanceRecord::query()
            ->whereDate('work_date', $today)
            ->whereIn('employee_id', $employees->modelKeys())
            ->with('employee.department')
            ->orderByDesc('entry_time')
            ->get();

        $attendedEmployeeIds = $todayRecords->pluck('employee_id')->unique();
        $monthlyRecords = collect();

        foreach ($employees as $employee) {
            $monthlyRecords = $monthlyRecords->merge($this->attendanceService->buildReportRecords(
                $employee,
                $monthStart,
                $today
            ));
        }

        $recentRecords = AttendanceRecord::query()
            ->whereBetween('work_date', [$monthStart, $today])
            ->whereIn('employee_id', $employees->modelKeys())
            ->with('employee.department')
            ->latest('work_date')
            ->latest('entry_time')
            ->limit(8)
            ->get();

        return view('dashboard', [
            'today' => $today,
            'monthStart' => $monthStart,
            'activeEmployees' => $employees->count(),
            'expectedToday' => $expectedToday->count(),
            'attendedToday' => $attendedEmployeeIds->count(),
            'missingToday' => max(0, $expectedToday->count() - $attendedEmployeeIds->intersect($expectedToday->modelKeys())->count()),
            'todayRecords' => $todayRecords,
            'monthlyRecords' => $monthlyRecords,
            'monthlyWorkedMinutes' => $monthlyRecords->sum('worked_minutes'),
            'monthlyOvertimeMinutes' => $monthlyRecords->sum('overtime_minutes'),
            'recentRecords' => $recentRecords,
        ]);
    }
}