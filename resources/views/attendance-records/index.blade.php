<x-app-layout>
    <x-layout.page-header title="Jornadas" subtitle="Jornadas Registradas">
        <x-slot:actions>
            @can('attendance-records.create')
                <x-button.primary :href="route('attendance.register')">
                    Registrar nueva asistencia
                </x-button.primary>
            @endcan
        </x-slot:actions>
    </x-layout.page-header>
    <x-layout.card>
        <form action="{{ route('attendance-records.index') }}" method="GET" class="mb-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">
                <x-form.input type="date" name="date_from" label="Fecha desde" :value="$filters['date_from'] ?? ''" />
                <x-form.input type="date" name="date_to" label="Fecha hasta" :value="$filters['date_to'] ?? ''" />
                <x-form.select name="employee_id" label="Empleado" :options="$employees" optionLabel="full_name"
                    optionValue="id" :selected="$filters['employee_id'] ?? ''" />
                <x-form.select name="department_id" label="Departamento" :options="$departments" optionLabel="name"
                    optionValue="id" :selected="$filters['department_id'] ?? ''" />
                <x-form.select name="position_id" label="Cargo" :options="$positions" optionLabel="name" optionValue="id"
                    :selected="$filters['position_id'] ?? ''" />
            </div>
            <div class="mt-4 flex justify-end gap-3">
                <x-button.secondary :href="route('attendance-records.index')">
                    Limpiar
                </x-button.secondary>
                <x-button.primary type="submit">
                    Filtrar
                </x-button.primary>
            </div>
        </form>
        @if (!$hasFilters && $lastWorkDate)
            <div class="py-2">
                <p class="text-sm text-slate-700 mt-1">
                    Mostrando jornadas del {{ \Carbon\Carbon::parse($lastWorkDate)->format('d/m/Y') }} <span
                        class="text-slate-500"> (Última fecha que
                        registra asistencia)</span>
                </p>
            </div>
        @elseif ($hasFilters)
            <div class="py-2">
                <p class="text-sm text-slate-700 mt-1">
                    Resultados según los filtros seleccionados.
                </p>
            </div>
        @endif
        <x-table.table>
            <x-table.head>
                <x-table.row>
                    <x-table.th>Empleado</x-table.th>
                    <x-table.th>Fecha</x-table.th>
                    <x-table.th>Entrada</x-table.th>
                    <x-table.th>Salida</x-table.th>
                    <x-table.th>Almuerzo</x-table.th>
                    <x-table.th class="w-40">Acciones</x-table.th>
                </x-table.row>
            </x-table.head>
            <x-table.body>
                @foreach ($attendanceRecords as $attendanceRecord)
                    <x-table.row>
                        <x-table.td>{{ $attendanceRecord->employee->full_name }}</x-table.td>
                        <x-table.td>{{ $attendanceRecord->work_date->format('d/m/Y') }}</x-table.td>
                        <x-table.td>{{ $attendanceRecord->entry_time?->format('H:i') }}</x-table.td>
                        <x-table.td>{{ $attendanceRecord->exit_time?->format('H:i') }}</x-table.td>
                        <x-table.td>{{ $attendanceRecord->lunch_time / 60 }} h</x-table.td>
                        <x-table.td class="flex gap-2 justify-start">
                            <x-button.secondary class="bg-indigo-700 hover:bg-indigo-600" :href="route('attendance-records.show', $attendanceRecord)">
                                Ver
                            </x-button.secondary>
                            @can('attendance-records.edit')
                                <x-button.secondary class="bg-green-700 hover:bg-green-600" :href="route('attendance-records.edit', $attendanceRecord)">
                                    Editar
                                </x-button.secondary>
                            @endcan
                        </x-table.td>
                    </x-table.row>
                @endforeach
            </x-table.body>
        </x-table.table>
        <div class="mt-4"> {{ $attendanceRecords->links() }} </div>
    </x-layout.card>
</x-app-layout>
