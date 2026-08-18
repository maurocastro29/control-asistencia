<x-app-layout>

    <x-layout.page-header title="Departamentos" subtitle="Administración de los departamentos">

        <x-slot:actions>

            <x-button.primary :href="route('departments.create')">
                Nuevo Departamento
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

                @forelse($departments as $department)
                    <x-table.row>

                        <x-table.td>
                            {{ $department->name }}
                        </x-table.td>

                        <x-table.td>
                            {{ $department->description }}
                        </x-table.td>

                        <x-table.td class="text-center">

                            <x-table.badge :active="$department->is_active" />

                        </x-table.td>

                        <x-table.td>

                            <x-table.actions>

                                <x-button.secondary :href="route('departments.edit', $department)">

                                    Editar

                                </x-button.secondary>

                            </x-table.actions>

                        </x-table.td>

                    </x-table.row>

                @empty

                    <x-table.empty :colspan="4" message="No existen departamentos registrados." />
                @endforelse

            </x-table.body>

        </x-table.table>

        <div class="mt-6">

            {{ $departments->links() }}

        </div>

    </x-layout.card>

</x-app-layout>
