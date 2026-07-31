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

                    <h3 class="text-xl font-bold">

                        {{ $workSchedule->name }}

                    </h3>

                    <p class="text-gray-600 mt-2">

                        {{ $workSchedule->description }}

                    </p>

                </div>

                <x-table>

                    <x-slot name="head">

                        <tr>

                            <x-table.th>Día</x-table.th>

                            <x-table.th>Entrada</x-table.th>

                            <x-table.th>Salida</x-table.th>

                            <x-table.th>Almuerzo</x-table.th>

                            <x-table.th>Ordinarias</x-table.th>

                        </tr>

                    </x-slot>

                    <x-slot name="body">

                        @foreach ($workSchedule->days as $day)
                            <tr>

                                <x-table.td>

                                    {{ $day->weekDay->name }}

                                </x-table.td>

                                <x-table.td>

                                    {{ $day->entry_time }}

                                </x-table.td>

                                <x-table.td>

                                    {{ $day->exit_time }}

                                </x-table.td>

                                <x-table.td>

                                    {{ $day->lunch_minutes }} min

                                </x-table.td>

                                <x-table.td>

                                    {{ $day->ordinary_minutes }} min

                                </x-table.td>

                            </tr>
                        @endforeach

                    </x-slot>

                </x-table>

            </div>

        </div>

    </div>

</x-app-layout>
