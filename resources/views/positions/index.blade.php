<x-app-layout>

    <x-layout.page-header title="Posiciones" subtitle="Administración de las posiciones">

        <x-slot:actions>

            <x-button.primary :href="route('positions.create')">
                Nueva Posición
            </x-button.primary>

        </x-slot:actions>

    </x-layout.page-header>

    <x-layout.card>

        <x-table.table>

            <x-table.head>

                <tr>

                    <x-table.th>
                        Nombre
                    </x-table.th>

                    <x-table.th>
                        Abreviatura
                    </x-table.th>

                    <x-table.th class="text-center">
                        Estado
                    </x-table.th>

                    <x-table.th class="text-center w-40">
                        Acciones
                    </x-table.th>

                </tr>

            </x-table.head>

            <x-table.body>

                @forelse($positions as $position)
                    <x-table.row>

                        <x-table.td>
                            {{ $position->name }}
                        </x-table.td>

                        <x-table.td>
                            {{ $position->abbreviation }}
                        </x-table.td>

                        <x-table.td class="text-center">

                            <x-table.badge :active="$position->is_active" />

                        </x-table.td>

                        <x-table.td>

                            <x-table.actions>

                                <x-button.secondary :href="route('positions.edit', $position)">

                                    Editar

                                </x-button.secondary>

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
