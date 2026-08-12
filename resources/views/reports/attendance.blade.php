<x-app-layout>
    <x-layout.page-header title="Reporte de Asistencia" subtitle="Consulta y analiza las jornadas registradas" />

    {{-- ==========================================
    FILTROS
=========================================== --}}

    <x-layout.card>

        <form action="{{ route('reports.attendance') }}" method="GET">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">

                {{-- Fecha inicial --}}
                <x-form.input type="date" name="date_from" label="Fecha desde" :value="$filters['date_from'] ?? ''" />

                {{-- Fecha final --}}
                <x-form.input type="date" name="date_to" label="Fecha hasta" :value="$filters['date_to'] ?? ''" />

                {{-- Empleado --}}
                <x-form.select name="employee_id" label="Empleado" :options="$employees" optionLabel="full_name"
                    optionValue="id" :selected="$filters['employee_id'] ?? ''" />

                {{-- Departamento --}}
                <x-form.select name="department_id" label="Departamento" :options="$departments" optionLabel="name"
                    optionValue="id" :selected="$filters['department_id'] ?? ''" />

                {{-- Cargo --}}
                <x-form.select name="position_id" label="Cargo" :options="$positions" optionLabel="name" optionValue="id"
                    :selected="$filters['position_id'] ?? ''" />
            </div>

            <div class="mt-6 flex justify-end gap-3">

                <x-button.secondary href="{{ route('reports.attendance') }}">
                    Limpiar
                </x-button.secondary>

                <x-button.primary type="submit">
                    Filtrar
                </x-button.primary>

                <a href="{{ route('reports.attendance.export', request()->query()) }}"
                    class="inline-flex items-center justify-center rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-green-700">
                    Exportar Excel
                </a>

            </div>

        </form>

        {{-- ==========================
    RESUMEN
=========================== --}}

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">

            {{-- Jornadas registradas --}}
            <x-layout.card>

                <p class="text-sm font-medium text-slate-500">
                    Jornadas registradas
                </p>

                <p class="mt-2 text-2xl font-bold text-slate-800">
                    {{ $attendanceRecords->total() }}
                </p>

            </x-layout.card>


            {{-- Tiempo trabajado --}}
            <x-layout.card>

                <p class="text-sm font-medium text-slate-500">
                    Tiempo trabajado
                </p>

                <p class="mt-2 text-2xl font-bold text-slate-800">
                    {{ $workedTimeFormatted }}
                </p>

                <p class="mt-1 text-xs text-slate-500">
                    Horas : minutos
                </p>

            </x-layout.card>


            {{-- Tiempo ordinario --}}
            <x-layout.card>

                <p class="text-sm font-medium text-slate-500">
                    Tiempo ordinario
                </p>

                <p class="mt-2 text-2xl font-bold text-slate-800">
                    {{ $ordinaryTimeFormatted }}
                </p>

                <p class="mt-1 text-xs text-slate-500">
                    Horas : minutos
                </p>

            </x-layout.card>


            {{-- Horas extras --}}
            <x-layout.card>

                <p class="text-sm font-medium text-slate-500">
                    Horas extras
                </p>

                <p class="mt-2 text-2xl font-bold text-orange-600">
                    {{ $overtimeTimeFormatted }}
                </p>

                <p class="mt-1 text-xs text-slate-500">
                    Horas : minutos
                </p>

            </x-layout.card>

        </div>

    </x-layout.card>


    {{-- ==========================================
    RESULTADOS
=========================================== --}}

    <div class="mt-6">

        <x-layout.card>

            <div class="flex items-center justify-between mb-6">

                <div>

                    <h3 class="text-lg font-semibold text-slate-800">
                        Jornadas registradas
                    </h3>

                    <p class="text-sm text-slate-500 mt-1">
                        {{ $attendanceRecords->total() }}
                        registro(s) encontrado(s)
                    </p>

                </div>

            </div>


            @if ($attendanceRecords->count())

                <x-table.table>

                    <x-table.head>

                        <x-table.row>

                            <x-table.th>
                                Fecha
                            </x-table.th>

                            <x-table.th>
                                Empleado
                            </x-table.th>

                            <x-table.th>
                                Departamento
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
                                Trabajado
                            </x-table.th>

                            <x-table.th>
                                Ordinario
                            </x-table.th>

                            <x-table.th>
                                Extra
                            </x-table.th>

                        </x-table.row>

                    </x-table.head>


                    <x-table.body>

                        @foreach ($attendanceRecords as $attendance)
                            <x-table.row>

                                <x-table.td>
                                    {{ $attendance->work_date->format('d/m/Y') }}
                                </x-table.td>

                                <x-table.td>
                                    {{ $attendance->employee->full_name }}
                                </x-table.td>

                                <x-table.td>
                                    {{ $attendance->employee->department->name }}
                                </x-table.td>

                                <x-table.td>
                                    {{ $attendance->entry_time?->format('H:i') }}
                                </x-table.td>

                                <x-table.td>
                                    {{ $attendance->exit_time?->format('H:i') }}
                                </x-table.td>

                                <x-table.td>
                                    {{ $attendance->lunch_time }} min
                                </x-table.td>

                                <x-table.td>
                                    {{ intdiv($attendance->worked_minutes, 60) }}h
                                    {{ $attendance->worked_minutes % 60 }}m
                                </x-table.td>

                                <x-table.td>
                                    {{ intdiv($attendance->ordinary_minutes, 60) }}h
                                    {{ $attendance->ordinary_minutes % 60 }}m
                                </x-table.td>

                                <x-table.td>

                                    @if ($attendance->overtime_minutes > 0)
                                        <span class="font-semibold text-red-600">

                                            {{ intdiv($attendance->overtime_minutes, 60) }}h
                                            {{ $attendance->overtime_minutes % 60 }}m

                                        </span>
                                    @else
                                        <span class="text-slate-400">
                                            0h 0m
                                        </span>
                                    @endif

                                </x-table.td>

                            </x-table.row>
                        @endforeach

                    </x-table.body>

                </x-table.table>

                <div class="mt-6">
                    {{ $attendanceRecords->links() }}
                </div>
            @else
                <div class="py-10 text-center">

                    <p class="text-slate-500">
                        No se encontraron jornadas con los filtros seleccionados.
                    </p>

                </div>

            @endif

        </x-layout.card>

    </div>
</x-app-layout>
