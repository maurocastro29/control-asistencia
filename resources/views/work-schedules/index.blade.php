<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Horarios Laborales') }}
        </h2>
    </x-slot>

    <div class="py-6">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-lg">

                <div class="p-6 flex items-center justify-between">

                    <h3 class="text-lg font-semibold">
                        Horarios registrados
                    </h3>

                    <a href="{{ route('work-schedules.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">

                        Nuevo Horario

                    </a>

                </div>
                <div class="rounded-xl mx-6">
                    <x-table.table>

                        <x-table.head>

                            <tr>

                                <x-table.th>
                                    Nombre
                                </x-table.th>

                                <x-table.th>
                                    Descripción
                                </x-table.th>

                                <x-table.th>
                                    Estado
                                </x-table.th>

                                <x-table.th>
                                    Empleados
                                </x-table.th>

                                <x-table.th class="text-right">
                                    Acciones
                                </x-table.th>

                            </tr>

                        </x-table.head>

                        <x-table.body>

                            @forelse($workSchedules as $schedule)
                                <x-table.row>

                                    <x-table.td>
                                        {{ $schedule->name }}
                                    </x-table.td>

                                    <x-table.td>
                                        {{ $schedule->description ?? 'Sin descripción' }}
                                    </x-table.td>

                                    <x-table.td>

                                        <x-table.badge :active="$schedule->is_active" />

                                    </x-table.td>

                                    <x-table.td>
                                        {{ $schedule->employees_count }}
                                    </x-table.td>

                                    <x-table.td class="text-right">

                                        <x-table.actions>

                                            <x-button.secondary :href="route('work-schedules.show', $schedule)">
                                                Ver
                                            </x-button.secondary>

                                            <x-button.secondary :href="route('work-schedules.edit', $schedule)">
                                                Editar
                                            </x-button.secondary>

                                        </x-table.actions>

                                    </x-table.td>

                                </x-table.row>

                            @empty

                                <x-table.empty :colspan="5" message="No existen horarios registrados." />
                            @endforelse

                        </x-table.body>

                    </x-table.table>
                </div>


                <div class="p-6">

                    {{ $workSchedules->links() }}

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
