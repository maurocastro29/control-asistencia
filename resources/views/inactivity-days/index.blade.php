<x-app-layout>
    <x-layout.page-header class="m-6" title="Inactividad"
        subtitle="Consulta y administra los registros de inactividad por trabajador.">
        <x-slot:actions>
            @can('inactivity-days.create')
                <x-button.primary :href="route('inactivity-records.create')">Nuevo registro</x-button.primary>
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
                    <x-table.th>Trabajador</x-table.th>
                    <x-table.th>Motivo</x-table.th>
                    <x-table.th>Estado</x-table.th>
                    <x-table.th class="text-right">Acciones</x-table.th>
                </x-table.row>
            </x-table.head>
            <x-table.body>
                @forelse ($inactivityRecords as $inactivityRecord)
                    <x-table.row class="hover:bg-slate-50">
                        <x-table.td>{{ $inactivityRecord->employee->full_name }}</x-table.td>
                        <x-table.td>{{ $inactivityRecord->reason->name }}</x-table.td>
                        <x-table.td>
                            <x-table.badge :active="$inactivityRecord->is_active" />
                        </x-table.td>
                        <x-table.td class="text-right">
                            <x-table.actions>
                                <x-button.secondary href="{{ route('inactivity-days.show', $inactivityRecord) }}">Ver detalle</x-button.secondary>
                                @can('inactivity-days.edit')
                                    <x-button.secondary
                                        href="{{ route('inactivity-records.edit', $inactivityRecord) }}">Editar</x-button.secondary>
                                @endcan
                                @can('inactivity-days.delete')
                                    @if ($inactivityRecord->is_active)
                                        <form action="{{ route('inactivity-records.destroy', $inactivityRecord) }}"
                                            method="POST" data-confirm="¿Deseas desactivar esta asignación?"
                                            data-confirm-title="Desactivar asignación" data-confirm-button="Sí, desactivar">
                                            @csrf
                                            @method('DELETE')
                                            <x-button.primary class="bg-red-500 hover:bg-red-600"
                                                type="submit">Desactivar</x-button.primary>
                                        </form>
                                    @endif
                                @endcan
                            </x-table.actions>
                        </x-table.td>
                    </x-table.row>
                @empty
                    <x-table.row>
                        <x-table.td colspan="4" class="py-8 text-center text-sm text-slate-500">
                            No hay registros de inactividad.
                        </x-table.td>
                    </x-table.row>
                @endforelse
            </x-table.body>
        </x-table.table>
        @if ($inactivityRecords->hasPages())
            <div class="mt-6">{{ $inactivityRecords->links() }}</div>
        @endif
    </x-layout.card>
</x-app-layout>
