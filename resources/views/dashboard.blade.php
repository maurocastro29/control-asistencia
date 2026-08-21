<x-app-layout>
    <x-layout.page-header title="Dashboard" subtitle="Resumen operativo de asistencia y jornadas trabajadas">
        <x-slot:actions>
            @canany(['attendance.view', 'attendance.create'])
                <x-button.primary :href="route('attendance.register')">
                    Registrar asistencia
                </x-button.primary>
            @endcanany
            @can('reports.view')
                <x-button.secondary :href="route('reports.attendance')">
                    Ver reporte
                </x-button.secondary>
            @endcan
        </x-slot:actions>
    </x-layout.page-header>

    <div class="mb-6 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500">Estado de hoy</p>
            <p class="text-2xl font-bold text-slate-800">{{ $today->translatedFormat('l, d \d\e F') }}</p>
        </div>
        <p class="text-sm text-slate-500">Acumulado desde {{ $monthStart->translatedFormat('d \d\e F') }}</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-layout.card>
            <p class="text-sm font-medium text-slate-500">Empleados activos</p>
            <p class="mt-2 text-3xl font-bold text-slate-800">{{ $activeEmployees }}</p>
            <p class="mt-1 text-xs text-slate-500">Personal habilitado en el sistema</p>
        </x-layout.card>
        <x-layout.card>
            <p class="text-sm font-medium text-slate-500">Asistieron hoy</p>
            <p class="mt-2 text-3xl font-bold text-green-600">{{ $attendedToday }} <span
                    class="text-lg font-medium text-slate-400">/ {{ $expectedToday }}</span></p>
            <p class="mt-1 text-xs text-slate-500">Personas con registro de entrada</p>
        </x-layout.card>
        <x-layout.card>
            <p class="text-sm font-medium text-slate-500">Pendientes hoy</p>
            <p class="mt-2 text-3xl font-bold {{ $missingToday ? 'text-amber-600' : 'text-green-600' }}">
                {{ $missingToday }}</p>
            <p class="mt-1 text-xs text-slate-500">Según el horario asignado</p>
        </x-layout.card>
        <x-layout.card>
            <p class="text-sm font-medium text-slate-500">Horas extras del mes</p>
            <p class="mt-2 text-3xl font-bold text-orange-600">{{ intdiv($monthlyOvertimeMinutes, 60) }}h
                {{ $monthlyOvertimeMinutes % 60 }}m</p>
            <p class="mt-1 text-xs text-slate-500">Tiempo adicional calculado</p>
        </x-layout.card>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
        <x-layout.card class="xl:col-span-2">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">Últimas jornadas</h3>
                    <p class="mt-1 text-sm text-slate-500">Registros realizados durante el mes actual</p>
                </div>
                @can('attendance-records.view')
                    <a href="{{ route('attendance-records.index') }}"
                        class="text-sm font-medium text-blue-600 hover:text-blue-800">Ver historial</a>
                @endcan
            </div>

            @if ($recentRecords->isNotEmpty())
                <div class="mt-5 overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                                <th class="px-3 py-3 font-medium">Empleado</th>
                                <th class="px-3 py-3 font-medium">Fecha</th>
                                <th class="px-3 py-3 font-medium">Horario</th>
                                <th class="px-3 py-3 text-right font-medium">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($recentRecords as $record)
                                <tr>
                                    <td class="whitespace-nowrap px-3 py-3 font-medium text-slate-700">
                                        {{ $record->employee->full_name }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-slate-500">
                                        {{ $record->work_date->format('d/m/Y') }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-slate-500">
                                        {{ $record->entry_time->format('H:i') }} -
                                        {{ $record->exit_time->format('H:i') }}</td>
                                    <td class="px-3 py-3 text-right">
                                        <span
                                            class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">Registrada</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="mt-5 rounded-md border border-dashed border-slate-300 px-4 py-8 text-center">
                    <p class="font-medium text-slate-700">Aún no hay jornadas registradas</p>
                    <p class="mt-1 text-sm text-slate-500">Los registros del mes aparecerán aquí.</p>
                </div>
            @endif
        </x-layout.card>

        <x-layout.card>
            <h3 class="text-lg font-semibold text-slate-800">Resumen del mes</h3>
            <p class="mt-1 text-sm text-slate-500">Tiempo procesado hasta hoy</p>
            <dl class="mt-6 space-y-5">
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-sm text-slate-500">Jornadas registradas</dt>
                    <dd class="font-semibold text-slate-800">{{ $monthlyRecords->count() }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-sm text-slate-500">Tiempo trabajado</dt>
                    <dd class="font-semibold text-slate-800">{{ intdiv($monthlyWorkedMinutes, 60) }}h
                        {{ $monthlyWorkedMinutes % 60 }}m</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-sm text-slate-500">Tiempo ordinario</dt>
                    <dd class="font-semibold text-slate-800">
                        {{ intdiv($monthlyRecords->sum('ordinary_minutes'), 60) }}h
                        {{ $monthlyRecords->sum('ordinary_minutes') % 60 }}m</dd>
                </div>
                <div class="border-t border-slate-200 pt-5">
                    <dt class="text-sm text-slate-500">Cobertura de hoy</dt>
                    <dd class="mt-2 text-2xl font-bold text-blue-600">
                        {{ $expectedToday ? min(100, round(($attendedToday / $expectedToday) * 100)) : 0 }}%</dd>
                </div>
            </dl>
        </x-layout.card>
    </div>
</x-app-layout>
