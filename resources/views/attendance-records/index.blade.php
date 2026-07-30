<x-app-layout> <x-slot name="header"> Jornadas Registradas </x-slot>
    <div class="mb-4 flex justify-end"> <x-button.primary href="{{ route('attendance-records.create') }}"> Nueva Jornada
        </x-button.primary> </div> <x-table> <x-slot name="head">
            <th>Empleado</th>
            <th>Fecha</th>
            <th>Entrada</th>
            <th>Salida</th>
            <th>Almuerzo</th>
            <th class="text-center">Acciones</th>
        </x-slot> <x-slot name="body">
            @foreach ($attendanceRecords as $attendanceRecord)
                <tr>
                    <td>{{ $attendanceRecord->employee->full_name }}</td>
                    <td>{{ $attendanceRecord->work_date->format('d/m/Y') }}</td>
                    <td>{{ $attendanceRecord->entry_time }}</td>
                    <td>{{ $attendanceRecord->exit_time }}</td>
                    <td>{{ $attendanceRecord->lunch_time / 60 }} h</td>
                    <td class="text-center"> <x-button.show :href="route('attendance-records.show', $attendanceRecord)" /> <x-button.edit :href="route('attendance-records.edit', $attendanceRecord)" />
                    </td>
                </tr>
            @endforeach
        </x-slot> </x-table>
    <div class="mt-4"> {{ $attendanceRecords->links() }} </div>
</x-app-layout>
