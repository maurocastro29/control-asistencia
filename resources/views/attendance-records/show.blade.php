<x-app-layout>
    <x-layout.page-header title="Detalle de la Jornada" subtitle="Información de la jornada">
        <x-slot:actions>
            @can('attendance-records.edit')
                <x-button.secondary :href="route('attendance-records.edit', $attendanceRecord)">
                    Editar
                </x-button.secondary>
            @endcan
            <x-button.secondary :href="route('attendance-records.index')">
                Volver
            </x-button.secondary>
        </x-slot:actions>
    </x-layout.page-header>
    <x-layout.card>
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
                <dd>{{ $attendanceRecord->entry_time?->format('H:i') }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Hora de salida</dt>
                <dd>{{ $attendanceRecord->exit_time?->format('H:i') }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Tiempo de almuerzo</dt>
                <dd>{{ $attendanceRecord->lunch_time / 60 }} horas</dd>
            </div>
            <div>
                <dt class="font-semibold">Registrado por</dt>
                <dd>{{ $attendanceRecord->createdBy->full_name }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Fecha de registro</dt>
                <dd>{{ $attendanceRecord->created_at->format('d/m/Y H:i') }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Última actualización</dt>
                <dd>{{ $attendanceRecord->updated_at->format('d/m/Y H:i') }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Observaciones</dt>
                <dd>{{ $attendanceRecord->observations ?: 'Sin observaciones' }}</dd>
            </div>
        </dl>
    </x-layout.card>
</x-app-layout>
