<x-app-layout>

    <x-slot name="header">
        Detalle de la Jornada
    </x-slot>

    <x-card>

        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <dt class="font-semibold">Empleado</dt>
                <dd>{{ $attendanceRecord->employee->full_name }}</dd>
            </div>

            <div>
                <dt class="font-semibold">Fecha</dt>
                <dd>{{ $attendanceRecord->work_date->format('d/m/Y') }}</dd>
            </div>

            <div>
                <dt class="font-semibold">Hora de entrada</dt>
                <dd>{{ $attendanceRecord->entry_time }}</dd>
            </div>

            <div>
                <dt class="font-semibold">Hora de salida</dt>
                <dd>{{ $attendanceRecord->exit_time }}</dd>
            </div>

            <div>
                <dt class="font-semibold">Tiempo de almuerzo</dt>
                <dd>{{ $attendanceRecord->lunch_time / 60 }} horas</dd>
            </div>

            <div>
                <dt class="font-semibold">Registrado por</dt>
                <dd>{{ $attendanceRecord->createdBy->full_name }}</dd>
            </div>

            <div class="md:col-span-2">
                <dt class="font-semibold">Observaciones</dt>
                <dd>{{ $attendanceRecord->observations ?: 'Sin observaciones' }}</dd>
            </div>

        </dl>

        <div class="mt-6 flex justify-end">

            <x-button.secondary href="{{ route('attendance-records.index') }}">
                Volver
            </x-button.secondary>

        </div>

    </x-card>

</x-app-layout>
