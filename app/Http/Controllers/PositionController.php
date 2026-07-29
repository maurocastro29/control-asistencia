<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateDocumentTypeRequest;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documentTypes = Position::orderBy('name')->paginate(10);
        return view('positions.index', compact('documentTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('positions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Position::create($request->validated());
        return redirect()
            ->route('positions.index')
            ->with('success', 'Cargo creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Position $position)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Position $position)
    {
        return view('positions.edit', compact('position'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentTypeRequest $request, Position $position)
    {
        $position->update($request->validated());

        return redirect()
            ->route('positions.index')
            ->with('success', 'Cargo actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position)
    {
        $position->delete();
        return redirect()
            ->route('positions.index')
            ->with('success', 'Cargo eliminado correctamente.');
    }
}