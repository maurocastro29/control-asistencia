<x-app-layout>

    <x-layout.page-header title="Roles" subtitle="Administración de los roles del sistema">

        <x-slot:actions>

            <x-button.primary :href="route('roles.create')">
                Nuevo Rol
            </x-button.primary>

        </x-slot:actions>

    </x-layout.page-header>

    <x-layout.card>

        <x-table.table>

            <x-table.head>

                <tr>

                    <x-table.th>
                        Nombre
                    </x-table.th>

                    <x-table.th>
                        Descripción
                    </x-table.th>

                    <x-table.th class="text-center">
                        Estado
                    </x-table.th>

                    <x-table.th class="text-center w-40">
                        Acciones
                    </x-table.th>

                </tr>

            </x-table.head>

            <x-table.body>

                @forelse($roles as $role)
                    <x-table.row>

                        <x-table.td>

                            {{ $role->name }}

                        </x-table.td>

                        <x-table.td>

                            {{ $role->description }}

                        </x-table.td>

                        <x-table.td class="text-center">

                            <x-table.badge :active="$role->is_active" />

                        </x-table.td>

                        <x-table.td>

                            <x-table.actions>

                                <x-button.secondary :href="route('roles.show', $role)">

                                    Ver

                                </x-button.secondary>

                                <x-button.secondary :href="route('roles.edit', $role)">

                                    Editar

                                </x-button.secondary>

                            </x-table.actions>

                        </x-table.td>

                    </x-table.row>

                @empty

                    <x-table.empty :colspan="4" message="No existen roles registrados." />
                @endforelse

            </x-table.body>

        </x-table.table>

        <div class="mt-6">

            {{ $roles->links() }}

        </div>

    </x-layout.card>

</x-app-layout>
