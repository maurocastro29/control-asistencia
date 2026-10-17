<?php

namespace App\Http\Controllers;

use App\Models\InactivityReason;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class InactivityReasonController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:inactivity-reasons.view')
            ->only(['index', 'show']);

        $this->middleware('permission:inactivity-reasons.create')
            ->only(['create', 'store']);

        $this->middleware('permission:inactivity-reasons.edit')
            ->only(['edit', 'update']);

        $this->middleware('permission:inactivity-reasons.delete')
            ->only('destroy');
    }

    public function index()
    {
        $inactivityReasons = InactivityReason::query()
            ->orderBy('name')
            ->paginate(10);

        return view('inactivity-reasons.index', compact('inactivityReasons'));
    }

    public function create()
    {
        return view('inactivity-reasons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:inactivity_reasons,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        InactivityReason::create($validated);

        return redirect()
            ->route('inactivity-reasons.index')
            ->with('success', 'Motivo de inactividad creado correctamente.');
    }

    public function show(InactivityReason $inactivityReason)
    {
        return view('inactivity-reasons.show', compact('inactivityReason'));
    }

    public function edit(InactivityReason $inactivityReason)
    {
        return view('inactivity-reasons.edit', compact('inactivityReason'));
    }

    public function update(Request $request, InactivityReason $inactivityReason)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('inactivity_reasons', 'name')->ignore($inactivityReason),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        $inactivityReason->update($validated);

        return redirect()
            ->route('inactivity-reasons.index')
            ->with('success', 'Motivo de inactividad actualizado correctamente.');
    }

    public function destroy(InactivityReason $inactivityReason)
    {
        $inactivityReason->update(['is_active' => false]);

        return redirect()
            ->route('inactivity-reasons.index')
            ->with('success', 'Motivo de inactividad desactivado correctamente.');
    }
}