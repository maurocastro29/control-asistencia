<x-app-layout>

    <x-slot name="header">
        Marcaciones
    </x-slot>

    <div class="mb-4 flex justify-end">

        <x-button.primary href="{{ route('attendance-records.create') }}">

            Nueva Marcación

        </x-button.primary>

    </div>

    <x-table>

        <x-slot name="head">

            <th>Empleado</th>
            <th>Tipo</th>
            <th>Fecha y Hora</th>
            <th>Registrado por</th>
            <th class="text-center">Acciones</th>

        </x-slot>

        <x-slot name="body">

            @foreach ($attendanceRecords as $attendanceRecord)
                <tr>

                    <td>{{ $attendanceRecord->employee->full_name }}</td>

                    <td>{{ $attendanceRecord->attendanceType->name }}</td>

                    <td>{{ $attendanceRecord->attendance_datetime->format('d/m/Y H:i') }}</td>

                    <td>{{ $attendanceRecord->createdBy->full_name }}</td>

                    <td class="text-center">

                        <x-button.edit :href="route('attendance-records.edit', $attendanceRecord)" />

                        <x-button.show :href="route('attendance-records.show', $attendanceRecord)" />

                    </td>

                </tr>
            @endforeach

        </x-slot>

    </x-table>

    <div class="mt-4">

        {{ $attendanceRecords->links() }}

    </div>

</x-app-layout>
