<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:users.view')
            ->only(['index', 'show']);

        $this->middleware('permission:users.create')
            ->only(['create', 'store']);

        $this->middleware('permission:users.edit')
            ->only(['edit', 'update']);

        $this->middleware('permission:users.delete')
            ->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $users = User::orderBy('first_name')
            ->orderBy('first_last_name')
            ->paginate(10);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $roles = Role::orderBy('name')->get();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create($request->validated());
        $user->assignRole($request->role);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View
    {
        $user->load('positions');
        $roles = Role::orderBy('name')->get();

        return view('users.show', compact('user', 'roles'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        $roles = Role::orderBy('name')->get();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (blank($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);
        $user->syncRoles($request->role);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->update([
            'is_active' => false,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario desactivado correctamente.');
    }
}