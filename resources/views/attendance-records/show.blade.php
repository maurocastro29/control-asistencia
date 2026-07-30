<x-app-layout>

    <x-slot name="header">
        Detalle de Marcación
    </x-slot>

    <x-card>

        <dl class="grid grid-cols-2 gap-6">

            <div>
                <dt class="font-semibold">Empleado</dt>
                <dd>{{ $attendanceRecord->employee->full_name }}</dd>
            </div>

            <div>
                <dt class="font-semibold">Tipo</dt>
                <dd>{{ $attendanceRecord->attendanceType->name }}</dd>
            </div>

            <div>
                <dt class="font-semibold">Fecha y Hora</dt>
                <dd>{{ $attendanceRecord->attendance_datetime->format('d/m/Y H:i') }}</dd>
            </div>

            <div>
                <dt class="font-semibold">Registrado por</dt>
                <dd>{{ $attendanceRecord->createdBy->full_name }}</dd>
            </div>

            <div class="col-span-2">
                <dt class="font-semibold">Observaciones</dt>
                <dd>{{ $attendanceRecord->observations ?: 'Sin observaciones' }}</dd>
            </div>

        </dl>

        <div class="mt-6">

            <x-button.secondary href="{{ route('attendance-records.index') }}">

                Volver

            </x-button.secondary>

        </div>

    </x-card>

</x-app-layout>
