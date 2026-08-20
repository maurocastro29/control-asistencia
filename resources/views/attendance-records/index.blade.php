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
        @if ($lastWorkDate)
            <div class="py-2">
                <p class="text-sm text-slate-700 mt-1">
                    Mostrando jornadas del {{ \Carbon\Carbon::parse($lastWorkDate)->format('d/m/Y') }} <span
                        class="text-slate-500"> (Última fecha que
                        registra asistencia)</span>
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
