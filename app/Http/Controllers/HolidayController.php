<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    /**
     * Display a listing of the holidays.
     */
    public function index()
    {
        $holidays = Holiday::query()
            ->orderBy('date')
            ->get();

        return view('holidays.index', compact('holidays'));
    }

    /**
     * Show the form for creating a new holiday.
     */
    public function create()
    {
        return view('holidays.create');
    }

    /**
     * Store a newly created holiday.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => [
                'required',
                'date',
                'unique:holidays,date',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['is_active'] =
            $request->boolean('is_active');

        Holiday::create($validated);

        return redirect()
            ->route('holidays.index')
            ->with('success', 'Festivo creado correctamente.');
    }

    /**
     * Display the specified holiday.
     */
    public function show(Holiday $holiday)
    {
        return view('holidays.show', compact('holiday'));
    }

    /**
     * Show the form for editing the specified holiday.
     */
    public function edit(Holiday $holiday)
    {
        return view('holidays.edit', compact('holiday'));
    }

    /**
     * Update the specified holiday.
     */
    public function update(
        Request $request,
        Holiday $holiday
    ) {
        $validated = $request->validate([
            'date' => [
                'required',
                'date',
                'unique:holidays,date,' . $holiday->id,
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['is_active'] =
            $request->boolean('is_active');

        $holiday->update($validated);

        return redirect()
            ->route('holidays.index')
            ->with('success', 'Festivo actualizado correctamente.');
    }

    /**
     * Remove the specified holiday.
     */
    public function destroy(Holiday $holiday)
    {
        $holiday->update([
            'is_active' => false,
        ]);

        return redirect()
            ->route('holidays.index')
            ->with('success', 'Festivo desactivado correctamente.');
    }
}