<x-app-layout>
    <x-layout.page-header title="Roles del sistema" subtitle="Administración de perfiles y accesos del sistema">
        @can('roles.create')
            <x-slot:actions>
                <x-button.primary :href="route('roles.create')">
                    Nuevo rol
                </x-button.primary>
            </x-slot:actions>
        @endcan
    </x-layout.page-header>

    @if (session('success'))
        <x-alert.success>{{ session('success') }}</x-alert.success>
    @endif

    <x-layout.card>
        <x-table.table>
            <x-table.head>
                <x-table.row>
                    <x-table.th>Rol</x-table.th>
                    <x-table.th>Permisos</x-table.th>
                    <x-table.th>Usuarios</x-table.th>
                    <x-table.th class="text-center">Estado</x-table.th>
                    <x-table.th class="text-center w-60">Acciones</x-table.th>
                </x-table.row>
            </x-table.head>
            <x-table.body>
                @forelse($roles as $role)
                    <x-table.row>
                        <x-table.td>
                            <div class="font-semibold text-slate-800">{{ $role->name }}</div>
                        </x-table.td>
                        <x-table.td>
                            {{ $role->permissions->count() }}
                        </x-table.td>
                        <x-table.td>
                            {{ $role->users_count }}
                        </x-table.td>
                        <x-table.td class="text-center">
                            <x-table.badge :active="$role->is_active" />
                        </x-table.td>
                        <x-table.td>
                            <x-table.actions>
                                <x-button.secondary style="background-color:gray; color:white;" :href="route('roles.show', $role)">
                                    Ver
                                </x-button.secondary>
                                @can('roles.edit')
                                    <x-button.secondary style="background-color:blue; color:white;" :href="route('roles.edit', $role)">
                                        Editar
                                    </x-button.secondary>
                                @endcan
                                @can('settings.edit')
                                    <x-button.secondary style="background-color:green; color:white;" :href="route('settings.roles.edit', $role)">
                                        Permisos
                                    </x-button.secondary>
                                @endcan
                            </x-table.actions>
                        </x-table.td>
                    </x-table.row>
                @empty
                    <x-table.empty :colspan="5" message="No existen roles registrados." />
                @endforelse
            </x-table.body>
        </x-table.table>
    </x-layout.card>
</x-app-layout>
