<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle del Horario
        </h2>
    </x-slot>

    <div class="py-6">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white rounded-lg shadow">

                <div class="p-6">
                    <div class="flex justify-between gap-3">
                        <div>
                            <h3 class="text-xl font-bold">

                                {{ $workSchedule->name }}

                            </h3>

                            <p class="text-gray-600 mt-2">

                                {{ $workSchedule->description }}

                            </p>
                        </div>
                        <div>
                            <button class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                                onclick="window.history.back()">
                                Volver
                            </button>
                        </div>
                    </div>




                </div>

                <x-table.table>

                    <x-table.head>

                        <x-table.row>

                            <x-table.th>
                                Día
                            </x-table.th>

                            <x-table.th>
                                Entrada
                            </x-table.th>

                            <x-table.th>
                                Salida
                            </x-table.th>

                            <x-table.th>
                                Almuerzo
                            </x-table.th>

                            <x-table.th>
                                Ordinaria
                            </x-table.th>

                        </x-table.row>

                    </x-table.head>

                    <x-table.body>

                        @foreach ($workSchedule->days as $day)
                            <x-table.row>

                                <x-table.td>

                                    {{ $day->weekDay->name }}

                                </x-table.td>

                                <x-table.td>

                                    {{ $day->entry_time?->format('H:i') ?? '-' }}

                                </x-table.td>

                                <x-table.td>

                                    {{ $day->exit_time?->format('H:i') ?? '-' }}

                                </x-table.td>

                                <x-table.td>

                                    {{ number_format($day->lunch_minutes / 60, 1) }} h

                                </x-table.td>

                                <x-table.td>

                                    {{ number_format($day->ordinary_minutes / 60, 1) }} h

                                </x-table.td>

                            </x-table.row>
                        @endforeach

                    </x-table.body>

                </x-table.table>

            </div>

        </div>

    </div>

</x-app-layout>
