<x-app-layout>
    <x-layout.page-header class="m-6" title="Detalle del registro de inactividad"
        subtitle="Consulta los días incluidos en este registro.">
        <x-slot:actions>
            <x-button.secondary href="{{ route('inactivity-records.index') }}">Volver</x-button.secondary>
            @can('inactivity-days.edit')
                <x-button.primary href="{{ route('inactivity-records.edit', $inactivityRecord) }}">Editar</x-button.primary>
            @endcan
        </x-slot:actions>
    </x-layout.page-header>
    <x-layout.card>
        <dl class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-slate-500">ID del registro</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ $inactivityRecord->id }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-slate-500">Trabajador</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ $inactivityRecord->employee->full_name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-slate-500">Cantidad de días</dt>
                <dd class="mt-1 text-sm text-slate-900">
                    {{ $inactivityRecord->days->count() }}
                    {{ $inactivityRecord->days->count() === 1 ? 'día' : 'días' }}
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-slate-500">Motivo</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ $inactivityRecord->reason->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-slate-500">Tipo</dt>
                <dd class="mt-1 text-sm text-slate-900">
                    {{ $inactivityRecord->is_paid ? 'Remunerado' : 'No remunerado' }}
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-slate-500">Estado</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ $inactivityRecord->is_active ? 'Activo' : 'Inactivo' }}</dd>
            </div>
        </dl>
        @php
            $calendarMonths = $inactivityRecord->days->groupBy(fn($day) => $day->date->format('Y-m'))->map(
                fn($days, $monthKey) => [
                    'month' => \Carbon\CarbonImmutable::parse($monthKey . '-01'),
                    'assignedDates' => $days->mapWithKeys(fn($day) => [$day->date->toDateString() => true]),
                ],
            );
            $weekdays = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        @endphp
        <section class="mt-8" aria-labelledby="inactivity-calendar-title">
            <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 id="inactivity-calendar-title" class="text-sm font-semibold text-slate-900">
                        Calendario de días asignados
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">Las fechas resaltadas pertenecen a este registro.</p>
                </div>
                <span class="inline-flex items-center gap-2 text-xs font-medium text-slate-600">
                    <span class="h-2.5 w-2.5 rounded-full bg-blue-600" aria-hidden="true"></span>
                    Día asignado
                </span>
            </div>

            @if ($calendarMonths->isEmpty())
                <p class="py-8 text-center text-sm text-slate-500">Este registro no tiene días asignados.</p>
            @else
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                    @foreach ($calendarMonths as $monthData)
                        <section class="min-w-0"
                            aria-label="Calendario de {{ $monthData['month']->translatedFormat('F Y') }}">
                            <h3 class="mb-3 text-center text-sm font-semibold capitalize text-slate-800">
                                {{ $monthData['month']->translatedFormat('F Y') }}
                            </h3>
                            <div role="grid" aria-label="{{ $monthData['month']->translatedFormat('F Y') }}">
                                <div class="mb-2 grid grid-cols-7 text-center text-xs font-medium text-slate-500">
                                    @foreach ($weekdays as $weekday)
                                        <span>{{ $weekday }}</span>
                                    @endforeach
                                </div>
                                <div class="grid grid-cols-7 gap-1">
                                    @for ($blankCell = 1; $blankCell < $monthData['month']->dayOfWeekIso; $blankCell++)
                                        <span class="aspect-square" aria-hidden="true"></span>
                                    @endfor
                                    @for ($dayNumber = 1; $dayNumber <= $monthData['month']->daysInMonth; $dayNumber++)
                                        @php
                                            $calendarDate = $monthData['month']->day($dayNumber);
                                            $isAssigned = $monthData['assignedDates']->has(
                                                $calendarDate->toDateString(),
                                            );
                                        @endphp
                                        <div role="gridcell"
                                            aria-label="{{ $calendarDate->translatedFormat('l d \\d\\e F Y') }}{{ $isAssigned ? ', día asignado' : '' }}"
                                            @class([
                                                'relative flex aspect-square items-center justify-center rounded-md text-sm',
                                                'bg-blue-600 font-semibold text-white shadow-sm' => $isAssigned,
                                                'text-slate-400' => !$isAssigned,
                                            ])>
                                            <time
                                                datetime="{{ $calendarDate->toDateString() }}">{{ $dayNumber }}</time>
                                            @if ($isAssigned)
                                                <span class="sr-only">{{ $calendarDate->format('d/m/Y') }}</span>
                                                <span class="absolute bottom-1 h-1 w-1 rounded-full bg-white"
                                                    aria-hidden="true"></span>
                                            @endif
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </section>
                    @endforeach
                </div>
            @endif
        </section>
    </x-layout.card>
</x-app-layout>
