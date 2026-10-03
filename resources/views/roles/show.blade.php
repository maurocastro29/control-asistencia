<x-app-layout>
    <x-layout.page-header title="Detalle del rol" subtitle="Información general y permisos asignados" />

    <div class="grid gap-6 xl:grid-cols-2">
        <x-layout.card>
            <div class="space-y-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Nombre</p>
                    <h3 class="mt-2 text-2xl font-bold text-slate-800">{{ $role->name }}</h3>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</p>
                    <div class="mt-2">
                        <x-table.badge :active="$role->is_active" />
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Usuarios asignados</p>
                    <p class="mt-2 text-lg text-slate-700">{{ $role->users()->count() }}</p>
                </div>
            </div>
        </x-layout.card>

        <x-layout.card>
            <div class="flex items-center justify-between gap-4">
                <h3 class="text-lg font-semibold text-slate-800">Permisos asignados</h3>
                @can('settings.edit')
                    <x-button.secondary :href="route('settings.roles.edit', $role)">Editar permisos</x-button.secondary>
                @endcan
            </div>

            <div class="mt-4 space-y-2">
                @forelse($role->permissions as $permission)
                    <span
                        class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-sm text-slate-700">{{ $permission->display_name }}</span>
                @empty
                    <p class="text-sm text-slate-500">Este rol no tiene permisos asignados.</p>
                @endforelse
            </div>
        </x-layout.card>
    </div>

    <div class="mt-6 flex gap-3">
        @can('roles.edit')
            <x-button.primary :href="route('roles.edit', $role)">Editar rol</x-button.primary>
        @endcan
        <x-button.secondary :href="route('roles.index')">Volver a roles</x-button.secondary>
    </div>
</x-app-layout>
