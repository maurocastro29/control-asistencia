<x-app-layout>
    <x-layout.page-header title="Ajustes de jornada"
        subtitle="Gestiona las reducciones y compensaciones de jornada laboral.">
        <x-slot:actions>
            <x-button.primary :href="route('work-schedule-adjustments.create')">
                Nuevo Ajuste
            </x-button.primary>
        </x-slot:actions>
    </x-layout.page-header>
    <x-layout.card>
        @if (session('success'))
            <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif
        <x-table.table>
            <x-table.head>
                <x-table.row>
                    <x-table.th>
                        Empleado
                    </x-table.th>
                    <x-table.th>
                        Fecha ajuste
                    </x-table.th>
                    <x-table.th>
                        Reducción
                    </x-table.th>
                    <x-table.th>
                        Fecha compensación
                    </x-table.th>
                    <x-table.th>
                        Estado
                    </x-table.th>
                    <x-table.th class="w-48">
                        Acciones
                    </x-table.th>
                </x-table.row>
            </x-table.head>
            <x-table.body>
                @forelse ($adjustments as $adjustment)
                    <x-table.row>
                        <x-table.td>
                            {{ $adjustment->employee->full_name }}
                        </x-table.td>
                        <x-table.td>
                            {{ $adjustment->adjustment_date->format('d/m/Y') }}
                        </x-table.td>
                        <x-table.td>
                            {{ intdiv($adjustment->reduced_minutes, 60) }}
                            h
                            {{ $adjustment->reduced_minutes % 60 }} min
                        </x-table.td>
                        <x-table.td>
                            @if ($adjustment->compensation_date)
                                {{ $adjustment->compensation_date->format('d/m/Y') }}
                            @else
                                <span class="text-slate-400">
                                    Sin compensación
                                </span>
                            @endif
                        </x-table.td>
                        <x-table.td>
                            @if ($adjustment->status === 'pending')
                                <span
                                    class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-700">
                                    {{ $adjustment->status === 'pending' ? 'Pendiente' : 'Desconocido' }}
                                </span>
                            @elseif ($adjustment->status === 'completed')
                                <span
                                    class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-600">
                                    {{ $adjustment->status === 'completed' ? 'Completado' : 'Desconocido' }}
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700">
                                    {{ $adjustment->status === 'cancelled' ? 'Cancelado' : 'Desconocido' }}
                                </span>
                            @endif
                        </x-table.td>
                        <x-table.td class="flex gap-2">
                            <a class="text-gray-700 hover:text-gray-600 hover:underline mt-2"
                                href="{{ route('work-schedule-adjustments.show', $adjustment) }}">Ver</a>
                            @if ($adjustment->is_active && $adjustment->status === 'pending')
                                <a class="text-blue-700 hover:text-indigo-600 hover:underline mt-2"
                                    href="{{ route('work-schedule-adjustments.edit', $adjustment) }}">Editar</a>
                            @endif
                            @if ($adjustment->is_active && $adjustment->status === 'pending' && $adjustment->status !== 'cancelled')
                                <form action="{{ route('work-schedule-adjustments.complete', $adjustment) }}"
                                    method="POST" onsubmit="return confirm('¿Deseas aprobar este ajuste de jornada?')">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-medium text-white hover:bg-green-500 focus:outline-none">
                                        Completar
                                    </button>
                                </form>
                            @endif
                            @if ($adjustment->is_active && $adjustment->status === 'pending' && $adjustment->status !== 'completed')
                                <form action="{{ route('work-schedule-adjustments.canceled', $adjustment) }}"
                                    method="POST"
                                    onsubmit="return confirm('¿Deseas cancelar este ajuste de jornada?')">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-500 focus:outline-none">
                                        Cancelar
                                    </button>
                                </form>
                            @endif
                        </x-table.td>
                    </x-table.row>
                @empty
                    <x-table.row>
                        <x-table.td colspan="6" class="py-8 text-center text-slate-500">
                            No hay ajustes de jornada registrados.
                        </x-table.td>
                    </x-table.row>
                @endforelse
            </x-table.body>
        </x-table.table>
        @if ($adjustments->hasPages())
            <div class="mt-6">
                {{ $adjustments->links() }}
            </div>
        @endif
    </x-layout.card>
</x-app-layout>
