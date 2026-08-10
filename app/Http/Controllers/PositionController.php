<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Routing\Controller;
use App\Http\Requests\StorePositionRequest;
use App\Http\Requests\UpdatePositionRequest;

class PositionController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:positions.view')
            ->only(['index', 'show']);

        $this->middleware('permission:positions.create')
            ->only(['create', 'store']);

        $this->middleware('permission:positions.edit')
            ->only(['edit', 'update']);

        $this->middleware('permission:positions.delete')
            ->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $positions = Position::orderBy('name')->paginate(10);
        return view('positions.index', compact('positions'));
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
    public function store(StorePositionRequest $request)
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
    public function update(UpdatePositionRequest $request, Position $position)
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