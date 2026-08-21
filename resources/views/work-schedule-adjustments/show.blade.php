<x-app-layout>

    <x-layout.page-header title="Detalle del ajuste" subtitle="Consulta la información del ajuste de jornada.">
        @can('work-schedules-adjustments.edit')
            <x-slot:actions>
                <x-button.primary :href="route('work-schedule-adjustments.edit', $adjustment)">
                    Nuevo Ajuste
                </x-button.primary>
            </x-slot:actions>
        @endcan
    </x-layout.page-header>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">

                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Empleado --}}
                        <div>
                            <dt class="text-sm font-medium text-slate-500">
                                Empleado
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-slate-900">
                                {{ $adjustment->employee->full_name }}
                            </dd>
                        </div>

                        {{-- Fecha del ajuste --}}
                        <div>
                            <dt class="text-sm font-medium text-slate-500">
                                Fecha del ajuste
                            </dt>

                            <dd class="mt-1 text-sm text-slate-900">
                                {{ $adjustment->adjustment_date->format('d/m/Y') }}
                            </dd>
                        </div>

                        {{-- Tiempo reducido --}}
                        <div>
                            <dt class="text-sm font-medium text-slate-500">
                                Tiempo reducido
                            </dt>

                            <dd class="mt-1 text-sm text-slate-900">

                                {{ intdiv($adjustment->reduced_minutes, 60) }} h

                                @if ($adjustment->reduced_minutes % 60 > 0)
                                    {{ $adjustment->reduced_minutes % 60 }} min
                                @endif

                            </dd>
                        </div>

                        {{-- Fecha compensación --}}
                        <div>
                            <dt class="text-sm font-medium text-slate-500">
                                Fecha de compensación
                            </dt>

                            <dd class="mt-1 text-sm text-slate-900">

                                @if ($adjustment->compensation_date)
                                    {{ $adjustment->compensation_date->format('d/m/Y') }}
                                @else
                                    <span class="text-slate-400">
                                        Sin fecha de compensación
                                    </span>
                                @endif

                            </dd>
                        </div>

                        {{-- Estado --}}
                        <div>
                            <dt class="text-sm font-medium text-slate-500">
                                Estado
                            </dt>

                            <dd class="mt-1">

                                @switch($adjustment->status)
                                    @case('pending')
                                        <span
                                            class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-700">
                                            Pendiente
                                        </span>
                                    @break

                                    @case('completed')
                                        <span
                                            class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">
                                            Cumplido
                                        </span>
                                    @break

                                    @case('cancelled')
                                        <span
                                            class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-600">
                                            Cancelado
                                        </span>
                                    @break
                                @endswitch

                            </dd>
                        </div>

                        {{-- Fecha creación --}}
                        <div>
                            <dt class="text-sm font-medium text-slate-500">
                                Registrado
                            </dt>

                            <dd class="mt-1 text-sm text-slate-900">
                                {{ $adjustment->created_at?->format('d/m/Y H:i') }}
                            </dd>
                        </div>

                    </div>

                    {{-- Motivo --}}
                    <div class="mt-8 border-t border-slate-200 pt-6">

                        <dt class="text-sm font-medium text-slate-500">
                            Motivo
                        </dt>

                        <dd class="mt-2 text-sm text-slate-700 whitespace-pre-line">
                            {{ $adjustment->reason ?: 'No se especificó un motivo.' }}
                        </dd>

                    </div>
                    {{-- Acciones --}}
                    <div class="mt-8 flex justify-end gap-3 border-t border-slate-200 pt-6">

                        <x-button.secondary :href="route('work-schedule-adjustments.index')">
                            Volver
                        </x-button.secondary>

                        @if ($adjustment->is_active && $adjustment->status === 'pending')
                            <x-button.secondary :href="route('work-schedule-adjustments.edit', $adjustment)">
                                Editar
                            </x-button.secondary>

                            <form action="{{ route('work-schedule-adjustments.complete', $adjustment) }}" method="POST"
                                onsubmit="return confirm('¿Deseas aprobar este ajuste de jornada?')">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-medium text-white hover:bg-green-500 focus:outline-none">
                                    Completar
                                </button>
                            </form>
                            <form action="{{ route('work-schedule-adjustments.canceled', $adjustment) }}" method="POST"
                                onsubmit="return confirm('¿Deseas cancelar este ajuste de jornada?')">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-500 focus:outline-none">
                                    Cancelar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
