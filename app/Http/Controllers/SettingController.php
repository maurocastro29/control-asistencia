<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:settings.view')->only(['index', 'editRole']);
        $this->middleware('permission:settings.edit')->only([
            'updateRole',
            'toggleRole',
            'togglePermission',
        ]);
    }

    public function index(): View
    {
        $roles = Role::query()
            ->with('permissions')
            ->withCount('users')
            ->orderBy('name')
            ->get();

        $permissions = Permission::query()
            ->orderBy('display_name')
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Permission $permission) => explode('.', $permission->name)[0]);

        return view('settings.index', compact('roles', 'permissions'));
    }

    public function editRole(Role $role): View
    {
        $role->load('permissions');

        $permissions = Permission::query()
            ->orderBy('display_name')
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Permission $permission) => explode('.', $permission->name)[0]);

        return view('settings.roles.edit', compact('role', 'permissions'));
    }

    public function updateRole(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ]);

        $permissionIds = collect($validated['permission_ids'] ?? []);
        $activePermissionIds = Permission::query()
            ->whereIn('id', $permissionIds)
            ->where('is_active', true)
            ->pluck('id');

        $role->syncPermissions($activePermissionIds->all());

        return redirect()
            ->route('settings.index')
            ->with('success', "Permisos del rol {$role->name} actualizados correctamente.");
    }

    public function toggleRole(Role $role): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = request()->user();
        $currentUserHasOnlyRole = $currentUser->roles()
            ->where('is_active', true)
            ->count() <= 1;

        if ($role->is_active && $currentUserHasOnlyRole && $currentUser->hasRole($role)) {
            return back()->with('error', 'No puedes desactivar el único rol activo de tu usuario.');
        }

        $role->update(['is_active' => !$role->is_active]);

        return back()->with(
            'success',
            "Rol {$role->name} " . ($role->is_active ? 'activado' : 'desactivado') . ' correctamente.'
        );
    }

    public function togglePermission(Permission $permission): RedirectResponse
    {
        if ($permission->is_active && $permission->name === 'settings.view') {
            return back()->with('error', 'El permiso de consulta de configuración no puede desactivarse desde este módulo.');
        }

        $permission->update(['is_active' => !$permission->is_active]);

        return back()->with(
            'success',
            "Permiso {$permission->name} " . ($permission->is_active ? 'activado' : 'desactivado') . ' correctamente.'
        );
    }
}