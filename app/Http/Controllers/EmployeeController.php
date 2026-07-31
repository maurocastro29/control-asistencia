<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\DocumentType;
use App\Models\WorkSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $employees = Employee::with([
                'documentType',
                'department',
                'position',
                'workSchedule',
            ])
            ->latest()
            ->paginate(15);

        return view('employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $documentTypes = DocumentType::where('is_active', true)
            ->orderBy('name')
            ->get();

        $departments = Department::where('is_active', true)
            ->orderBy('name')
            ->get();

        $positions = Position::where('is_active', true)
            ->orderBy('name')
            ->get();

        $workSchedules = WorkSchedule::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('employees.create', compact(
            'documentTypes',
            'departments',
            'positions',
            'workSchedules'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        Employee::create(
            $request->validated()
        );

        return redirect()
            ->route('employees.index')
            ->with('success', 'Empleado creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee): View
    {
        $employee->load([
            'documentType',
            'department',
            'position',
            'workSchedule',
        ]);

        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee): View
    {
        $documentTypes = DocumentType::where('is_active', true)
            ->orderBy('name')
            ->get();

        $departments = Department::where('is_active', true)
            ->orderBy('name')
            ->get();

        $positions = Position::where('is_active', true)
            ->orderBy('name')
            ->get();

        $workSchedules = WorkSchedule::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('employees.edit', compact(
            'employee',
            'documentTypes',
            'departments',
            'positions',
            'workSchedules'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateEmployeeRequest $request,
        Employee $employee
    ): RedirectResponse {

        $employee->update(
            $request->validated()
        );

        return redirect()
            ->route('employees.index')
            ->with('success', 'Empleado actualizado correctamente.');
    }

    /**
     * Desactiva el empleado.
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        if (!$employee->is_active) {

            return redirect()
                ->route('employees.index')
                ->with('warning', 'El empleado ya se encuentra inactivo.');

        }

        $employee->update([
            'is_active' => false,
        ]);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Empleado desactivado correctamente.');
    }
}