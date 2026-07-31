<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Horarios Laborales') }}
        </h2>
    </x-slot>

    <div class="py-6">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-lg">

                <div class="p-6 border-b flex items-center justify-between">

                    <h3 class="text-lg font-semibold">
                        Horarios registrados
                    </h3>

                    <a href="{{ route('work-schedules.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">

                        Nuevo Horario

                    </a>

                </div>

                <x-table>

                    <x-slot name="head">

                        <tr>

                            <x-table.th>Nombre</x-table.th>

                            <x-table.th>Descripción</x-table.th>

                            <x-table.th>Estado</x-table.th>

                            <x-table.th>Empleados</x-table.th>

                            <x-table.th class="text-right">
                                Acciones
                            </x-table.th>

                        </tr>

                    </x-slot>

                    <x-slot name="body">

                        @forelse($workSchedules as $schedule)
                            <tr>

                                <x-table.td>

                                    {{ $schedule->name }}

                                </x-table.td>

                                <x-table.td>

                                    {{ $schedule->description }}

                                </x-table.td>

                                <x-table.td>

                                    @if ($schedule->is_active)
                                        <span class="text-green-600">
                                            Activo
                                        </span>
                                    @else
                                        <span class="text-red-600">
                                            Inactivo
                                        </span>
                                    @endif

                                </x-table.td>

                                <x-table.td>

                                    {{ $schedule->employees_count }}

                                </x-table.td>

                                <x-table.td class="text-right space-x-2">

                                    <a href="{{ route('work-schedules.show', $schedule) }}" class="text-blue-600">

                                        Ver

                                    </a>

                                    <a href="{{ route('work-schedules.edit', $schedule) }}" class="text-indigo-600">

                                        Editar

                                    </a>

                                </x-table.td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5" class="text-center py-8">

                                    No existen horarios registrados.

                                </td>

                            </tr>
                        @endforelse

                    </x-slot>

                </x-table>

                <div class="p-6">

                    {{ $workSchedules->links() }}

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
