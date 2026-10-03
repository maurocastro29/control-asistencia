<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:settings.view')->only(['index', 'show']);
        $this->middleware('permission:settings.edit')->only(['create', 'store', 'edit', 'update']);
    }

    public function index(): View
    {
        $roles = Role::query()
            ->withCount('users')
            ->with('permissions')
            ->orderBy('name')
            ->get();

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('roles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Role::query()->create([
            'name' => trim($validated['name']),
            'guard_name' => 'web',
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rol creado correctamente.');
    }

    public function show(Role $role): View
    {
        $role->load('permissions');

        return view('roles.show', compact('role'));
    }

    public function edit(Role $role): View
    {
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $role->update([
            'name' => trim($validated['name']),
            'is_active' => $validated['is_active'] ?? $role->is_active,
        ]);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rol actualizado correctamente.');
    }
}
