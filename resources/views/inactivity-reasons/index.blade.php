<x-app-layout>
    <x-layout.page-header class="m-6" title="Motivos de inactividad" subtitle="Administra los motivos disponibles para registrar días de inactividad.">
        <x-slot:actions>
            @can('inactivity-reasons.create')
                <x-button.primary :href="route('inactivity-reasons.create')">
                    Nuevo motivo
                </x-button.primary>
            @endcan
        </x-slot:actions>
    </x-layout.page-header>
    <x-layout.card>
        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif
        <x-table.table class="min-w-full divide-y divide-slate-200">
            <x-table.head class="bg-slate-50">
                <x-table.row>
                    <x-table.th class="px-6 py-3 text-left text-xs font-medium uppercase text-slate-500">Motivo</x-table.th>
                    <x-table.th class="px-6 py-3 text-left text-xs font-medium uppercase text-slate-500">Descripción</x-table.th>
                    <x-table.th class="px-6 py-3 text-left text-xs font-medium uppercase text-slate-500">Estado</x-table.th>
                    <x-table.th class="px-6 py-3 text-right text-xs font-medium uppercase text-slate-500">Acciones</x-table.th>
                </x-table.row>
            </x-table.head>
            <tbody class="divide-y divide-slate-200 bg-white">
                @forelse ($inactivityReasons as $inactivityReason)
                    <x-table.row class="hover:bg-slate-50">
                        <x-table.td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-900">
                            {{ $inactivityReason->name }}
                        </x-table.td>
                        <x-table.td class="px-6 py-4 text-sm text-slate-700">
                            {{ $inactivityReason->description ?: 'Sin descripción' }}
                        </x-table.td>
                        <x-table.td class="whitespace-nowrap px-6 py-4">
                            @if ($inactivityReason->is_active)
                                <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Activo</span>
                            @else
                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">Inactivo</span>
                            @endif
                        </x-table.td>
                        <x-table.td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                            <div class="flex justify-end gap-2">
                                <x-button.secondary href="{{ route('inactivity-reasons.show', $inactivityReason) }}">
                                    Ver
                                </x-button.secondary>
                                @can('inactivity-reasons.edit')
                                    <x-button.secondary href="{{ route('inactivity-reasons.edit', $inactivityReason) }}">
                                        Editar
                                    </x-button.secondary>
                                @endcan
                                @can('inactivity-reasons.delete')
                                    @if ($inactivityReason->is_active)
                                        <form action="{{ route('inactivity-reasons.destroy', $inactivityReason) }}" method="POST"
                                            data-confirm="¿Deseas desactivar este motivo?"
                                            data-confirm-title="Desactivar motivo"
                                            data-confirm-button="Sí, desactivar">
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
                            No hay motivos de inactividad registrados.
                        </x-table.td>
                    </tr>
                @endforelse
            </tbody>
        </x-table.table>
        <div class="mt-4">
            {{ $inactivityReasons->links() }}
        </div>
    </x-layout.card>
</x-app-layout>