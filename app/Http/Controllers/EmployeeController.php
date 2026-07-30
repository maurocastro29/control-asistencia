<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\DocumentType;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::with([
                'documentType',
                'department',
                'position'
            ])
            ->orderBy('first_last_name')
            ->orderBy('first_name')
            ->paginate(10);

        return view('employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employees.create', [
            'documentTypes' => DocumentType::where('is_active', true)
                ->orderBy('name')
                ->get(),

            'departments' => Department::where('is_active', true)
                ->orderBy('name')
                ->get(),

            'positions' => Position::where('is_active', true)
                ->orderBy('name')
                ->get(), ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Employee::create($request->validated());

        return redirect()
            ->route('employees.index')
            ->with('success', 'Empleado registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $employee->load([
            'documentType',
            'department',
            'position'
        ]);

        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        return view('employees.edit', [
            'employee' => $employee,

            'documentTypes' => DocumentType::where('is_active', true)
            ->orderBy('name')
            ->get(),

            'departments' => Department::where('is_active', true)
            ->orderBy('name')
            ->get(),
            'positions' => Position::where('is_active', true)
            ->orderBy('name')
            ->get(), ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $employee->update($request->validated());

        return redirect()
        ->route('employees.index')
        ->with('success', 'Empleado actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->update([
            'is_active' => false,
            'termination_date' => now()->toDateString(), ]);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Empleado dado de baja correctamente.');
    }
}