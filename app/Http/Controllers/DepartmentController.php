<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateDocumentTypeRequest;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documentTypes = Department::orderBy('name')->paginate(10);
        return view('departments.index', compact('documentTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('departments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Department::create($request->validated());
        return redirect()
            ->route('departments.index')
            ->with('success', 'Tipo de documento creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentTypeRequest $request, Department $department)
    {
        $department->update($request->validated());

        return redirect()
            ->route('departments.index')
            ->with('success', 'Tipo de documento actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        //
    }
}