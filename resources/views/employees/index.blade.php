<x-app-layout>
    <x-layout.page-header title="Empleados" subtitle="Administración de los empleados">
        <x-slot:actions>
            @can('employees.view')
                <x-button.primary :href="route('employees.create')">
                    Nuevo Empleado
                </x-button.primary>
            @endcan
        </x-slot:actions>
    </x-layout.page-header>
    <x-layout.card>
        <x-table.table>
            <x-table.head>
                <x-table.row>
                    <x-table.th>Documento</x-table.th>
                    <x-table.th>Nombre Completo</x-table.th>
                    <x-table.th>Departamento</x-table.th>
                    <x-table.th>Cargo</x-table.th>
                    <x-table.th>Horario</x-table.th>
                    <x-table.th class="text-center">Estado</x-table.th>
                    <x-table.th class="text-center w-48">Acciones</x-table.th>
                </x-table.row>
            </x-table.head>
            <x-table.body>
                @forelse($employees as $employee)
                    <x-table.row>
                        <x-table.td>
                            {{ $employee->documentType->abbreviation }}
                            {{ $employee->document_number }}
                        </x-table.td>
                        <x-table.td>
                            {{ $employee->full_name }}
                        </x-table.td>
                        <x-table.td>
                            {{ $employee->department?->name }}
                        </x-table.td>
                        <x-table.td>
                            {{ $employee->position?->name }}
                        </x-table.td>
                        <x-table.td>
                            {{ $employee->workSchedule?->name ?? 'Sin asignar' }}
                        </x-table.td>
                        <x-table.td class="text-center">
                            <x-table.badge :active="$employee->is_active" />
                        </x-table.td>
                        <x-table.td>
                            <x-table.actions>
                                <x-button.secondary :href="route('employees.show', $employee)">
                                    Ver
                                </x-button.secondary>
                                @can('employees.edit')
                                    <x-button.secondary :href="route('employees.edit', $employee)">
                                        Editar
                                    </x-button.secondary>
                                @endcan
                            </x-table.actions>
                        </x-table.td>
                    </x-table.row>
                @empty
                    <x-table.empty :colspan="7" message="No existen empleados registrados." />
                @endforelse
            </x-table.body>
        </x-table.table>
        <div class="mt-6">
            {{ $employees->links() }}
        </div>
    </x-layout.card>
</x-app-layout>
