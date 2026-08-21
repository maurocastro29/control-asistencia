<x-app-layout>
    <x-layout.page-header title="Horarios Laborales" subtitle="Administración de los horarios laborales">
        <x-slot:actions>
            @can('work-schedules.create')
                <x-button.primary :href="route('work-schedules.create')">
                    Registrar nuevo horario
                </x-button.primary>
            @endcan
        </x-slot:actions>
    </x-layout.page-header>
    <x-layout.card>
        <x-table.table>
            <x-table.head>
                <x-table.row>
                    <x-table.th>Nombre</x-table.th>
                    <x-table.th>Descripción</x-table.th>
                    <x-table.th>Estado</x-table.th>
                    <x-table.th>Empleados</x-table.th>
                    <x-table.th class="text-right">Acciones</x-table.th>
                </x-table.row>
            </x-table.head>
            <x-table.body>
                @forelse($workSchedules as $schedule)
                    <x-table.row>
                        <x-table.td>
                            {{ $schedule->name }}
                        </x-table.td>
                        <x-table.td>
                            {{ $schedule->description ?? 'Sin descripción' }}
                        </x-table.td>
                        <x-table.td>
                            <x-table.badge :active="$schedule->is_active" />
                        </x-table.td>
                        <x-table.td>
                            {{ $schedule->employees_count }}
                        </x-table.td>
                        <x-table.td class="text-right">
                            <x-table.actions>
                                <x-button.secondary :href="route('work-schedules.show', $schedule)">
                                    Ver
                                </x-button.secondary>
                                @can('work-schedules.edit')
                                    <x-button.secondary :href="route('work-schedules.edit', $schedule)">
                                        Editar
                                    </x-button.secondary>
                                @endcan
                            </x-table.actions>
                        </x-table.td>
                    </x-table.row>
                @empty
                    <x-table.empty :colspan="5" message="No existen horarios registrados." />
                @endforelse
            </x-table.body>
        </x-table.table>
        </div>
        <div class="p-6">
            {{ $workSchedules->links() }}
        </div>
    </x-layout.card>
</x-app-layout>
