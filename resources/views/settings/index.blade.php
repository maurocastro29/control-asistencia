<x-app-layout>
    <x-layout.page-header title="Configuración de acceso"
        subtitle="Administra roles, permisos y autorizaciones del sistema" />
    @if (session('success'))
        <x-alert.success>{{ session('success') }}</x-alert.success>
    @endif
    @if (session('error'))
        <x-alert.error>{{ session('error') }}</x-alert.error>
    @endif
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <x-layout.card>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">Roles del sistema</h3>
                    <p class="mt-1 text-sm text-slate-500">Activa o desactiva perfiles y administra sus autorizaciones.
                    </p>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-4 text-xs font-medium text-slate-600">
                    {{ $roles->count() }} roles
                </span>
            </div>
            <div class="mt-6 space-y-4">
                @forelse ($roles as $role)
                    <div class="rounded-lg border border-slate-200 p-4 mt-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <div class="flex items-center gap-3">
                                    <h4 class="font-semibold text-slate-800">{{ $role->name }}</h4>
                                    <x-table.badge :active="$role->is_active" />
                                </div>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $role->users_count }} usuario(s) ·
                                    {{ $role->permissions->where('is_active', true)->count() }} permiso(s) activo(s)
                                </p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @can('settings.view')
                                    <x-button.secondary :href="route('settings.roles.edit', $role)">Permisos</x-button.secondary>
                                @endcan
                                @can('settings.edit')
                                    <form method="POST" action="{{ route('settings.roles.status', $role) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="inline-flex items-center rounded-lg border px-3 py-2 text-sm font-medium transition {{ $role->is_active ? 'border-amber-300 text-amber-700 hover:bg-amber-50' : 'border-green-300 text-green-700 hover:bg-green-50' }}">
                                            {{ $role->is_active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                        @if ($role->permissions->isNotEmpty())
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($role->permissions->take(8) as $permission)
                                    <span
                                        class="rounded bg-slate-100 px-2 py-1 text-xs text-slate-600 {{ !$permission->is_active ? 'line-through opacity-60' : '' }}">{{ $permission->display_name }}</span>
                                @endforeach
                                @if ($role->permissions->count() > 8)
                                    <span
                                        class="px-2 py-1 text-xs text-slate-400">+{{ $role->permissions->count() - 8 }}
                                        más</span>
                                @endif
                            </div>
                        @else
                            <p class="mt-3 text-xs text-slate-400">Este rol no tiene permisos asignados.</p>
                        @endif
                    </div>
                @empty
                    <p
                        class="rounded-lg border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-slate-500">
                        No existen roles registrados.</p>
                @endforelse
            </div>
        </x-layout.card>
        <x-layout.card>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">Permisos disponibles</h3>
                    <p class="mt-1 text-sm text-slate-500">Los permisos inactivos no autorizan ningún módulo.</p>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-4 text-xs font-medium text-slate-600">
                    {{ $permissions->flatten()->count() }} permisos
                </span>
            </div>

            <div class="mt-6 space-y-5">
                @forelse ($permissions as $module => $modulePermissions)
                    <section>
                        <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {{ str_replace('-', ' ', $module) }}</h4>
                        <div class="divide-y divide-slate-100 rounded-lg border border-slate-200">
                            @foreach ($modulePermissions as $permission)
                                <div class="flex items-center justify-between gap-3 px-3 py-2.5">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-medium text-slate-700">
                                            {{ $permission->display_name }}</p>
                                        <p class="truncate text-xs text-slate-400">{{ $permission->name }}</p>
                                        <p
                                            class="text-xs {{ $permission->is_active ? 'text-green-600' : 'text-slate-400' }}">
                                            {{ $permission->is_active ? 'Activo' : 'Inactivo' }}</p>
                                    </div>
                                    @can('settings.edit')
                                        <form method="POST"
                                            action="{{ route('settings.permissions.status', $permission) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="whitespace-nowrap text-xs font-medium {{ $permission->is_active ? 'text-amber-700 hover:text-amber-900' : 'text-green-700 hover:text-green-900' }}">
                                                {{ $permission->is_active ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            @endforeach
                        </div>
                    </section>
                @empty
                    <p
                        class="rounded-lg border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-slate-500">
                        No existen permisos registrados.</p>
                @endforelse
            </div>
        </x-layout.card>
    </div>
</x-app-layout>
