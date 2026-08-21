<x-app-layout>
    <x-layout.page-header title="Cargos" subtitle="Administración de los cargos de la compañía">
        <x-slot:actions>
            @can('positions.create')
                <x-button.primary :href="route('positions.create')">
                    Nuevo Cargo
                </x-button.primary>
            @endcan
        </x-slot:actions>
    </x-layout.page-header>
    <x-layout.card>
        <x-table.table>
            <x-table.head>
                <x-table.row>
                    <x-table.th>Nombre</x-table.th>
                    <x-table.th>Descripción</x-table.th>
                    <x-table.th class="text-center"> Estado</x-table.th>
                    <x-table.th class="text-center w-40"> Acciones</x-table.th>
                </x-table.row>
            </x-table.head>
            <x-table.body>
                @forelse($positions as $position)
                    <x-table.row>
                        <x-table.td>
                            {{ $position->name }}
                        </x-table.td>
                        <x-table.td>
                            {{ $position->description }}
                        </x-table.td>
                        <x-table.td class="text-center">
                            <x-table.badge :active="$position->is_active" />
                        </x-table.td>
                        <x-table.td>
                            <x-table.actions>
                                @can('positions.edit')
                                    <x-button.secondary :href="route('positions.edit', $position)">
                                        Editar
                                    </x-button.secondary>
                                @endcan
                            </x-table.actions>
                        </x-table.td>
                    </x-table.row>
                @empty
                    <x-table.empty :colspan="4" message="No existen posiciones registradas." />
                @endforelse
            </x-table.body>
        </x-table.table>
        <div class="mt-6">
            {{ $positions->links() }}
        </div>
    </x-layout.card>
</x-app-layout>
