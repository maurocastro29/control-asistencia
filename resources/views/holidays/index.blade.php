<x-app-layout>
    <x-layout.page-header class="m-6" title="Festivos" subtitle="Gestiona los días festivos registrados en el sistema.">
        <x-slot:actions>
            @can('holidays.create')
                <x-button.primary :href="route('holidays.create')">
                    Nuevo festivo
                </x-button.primary>
            @endcan
        </x-slot:actions>
    </x-layout.page-header>
    <x-layout.card>
        @if (session('success'))
            <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif
        <x-table.table class="min-w-full divide-y divide-slate-200">
            <x-table.head class="bg-slate-50">
                <x-table.row>
                    <x-table.th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                        Fecha
                    </x-table.th>
                    <x-table.th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                        Festivo
                    </x-table.th>
                    <x-table.th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                        Estado
                    </x-table.th>
                    <x-table.th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">
                        Acciones
                    </x-table.th>
                </x-table.row>
            </x-table.head>
            <tbody class="bg-white divide-y divide-slate-200">
                @forelse ($holidays as $holiday)
                    <x-table.row class="hover:bg-slate-50">
                        <x-table.td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                            {{ $holiday->date->format('d/m/Y') }}
                        </x-table.td>
                        <x-table.td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                            {{ $holiday->name }}
                        </x-table.td>
                        <x-table.td class="px-6 py-4 whitespace-nowrap">
                            @if ($holiday->is_active)
                                <span
                                    class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">
                                    Activo
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">
                                    Inactivo
                                </span>
                            @endif
                        </x-table.td>
                        <x-table.td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <div class="flex justify-end gap-2">
                                <x-button.secondary href="{{ route('holidays.show', $holiday) }}">
                                    Ver
                                </x-button.secondary>
                                @can('holidays.edit')
                                    <x-button.secondary href="{{ route('holidays.edit', $holiday) }}">
                                        Editar
                                    </x-button.secondary>
                                @endcan
                                @can('holidays.delete')
                                    @if ($holiday->is_active)
                                        <form action="{{ route('holidays.destroy', $holiday) }}" method="POST"
                                            onsubmit="return confirm('¿Deseas desactivar este festivo?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-button.primary class="bg-red-500 hover:bg-red-600" type="submit">
                                                Desactivar
                                            </x-button.primary>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </x-table.td>
                    </x-table.row>
                @empty
                    <tr>
                        <x-table.td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500">
                            No hay festivos registrados.
                        </x-table.td>
                    </tr>
                @endforelse
            </tbody>
        </x-table.table>
    </x-layout.card>
</x-app-layout>
