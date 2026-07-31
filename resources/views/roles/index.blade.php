<x-app-layout>
    <x-layout.page-header title="Empleados" subtitle="Administración de los empleados">

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
                        descripcion
                    </x-table.th>

                    <x-table.th class="text-center">
                        Estado
                    </x-table.th>

                    <x-table.th class="text-center w-48">
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
                                <a href="{{ route('roles.edit', $role) }}" class="text-blue-500 hover:text-blue-700">
                                    Editar
                                </a>
                            </x-table.actions>

                        </x-table.td>

                    </x-table.row>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            No hay usuarios registrados.
                        </td>
                    </tr>
                @endforelse

            </x-table.body>

        </x-table.table>
    </x-layout.card>

</x-app-layout>
