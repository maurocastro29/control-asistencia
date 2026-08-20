<x-app-layout>
    <x-layout.page-header title="Reporte de Asistencia" subtitle="Consulta y analiza las jornadas registradas">
        <x-slot:actions>
            @can('attendance.register')
                <x-button.primary :href="route('attendance.register')">
                    Nueva asistencia
                </x-button.primary>
            @endcan
        </x-slot:actions>
    </x-layout.page-header>
    {{-- ==========================================
    FILTROS
    ========================================== --}}
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
                <div class="w-full overflow-x-auto">
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
                                    Estado
                                </x-table.th>
                                <x-table.th>
                                    Extra
                                </x-table.th>
                            </x-table.row>
                        </x-table.head>
                        <x-table.body>
                            @foreach ($attendanceRecords as $record)
                                <x-table.row>
                                    {{-- Fecha --}}
                                    <x-table.td>
                                        {{ $record['work_date']->format('d/m/Y') }}
                                    </x-table.td>
                                    {{-- Empleado --}}
                                    <x-table.td>
                                        {{ $record['employee']->full_name }}
                                    </x-table.td>
                                    {{-- Departamento --}}
                                    <x-table.td>
                                        {{ $record['employee']->department->name }}
                                    </x-table.td>
                                    {{-- Estado --}}
                                    <x-table.td>
                                        @if ($record['status'] === 'registered')
                                            <span
                                                class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">
                                                Registrada
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700">
                                                Sin registrar
                                            </span>
                                        @endif
                                    </x-table.td>
                                    {{-- Entrada --}}
                                    <x-table.td>
                                        {{ $record['attendance']?->entry_time?->format('H:i') ?? '—' }}
                                    </x-table.td>
                                    {{-- Salida --}}
                                    <x-table.td>
                                        {{ $record['attendance']?->exit_time?->format('H:i') ?? '—' }}
                                    </x-table.td>
                                    {{-- Almuerzo --}}
                                    <x-table.td>
                                        {{ $record['attendance']?->lunch_time ?? 0 }} min
                                    </x-table.td>
                                    {{-- Trabajado --}}
                                    <x-table.td>
                                        {{ intdiv($record['worked_minutes'], 60) }}h
                                        {{ $record['worked_minutes'] % 60 }}m
                                    </x-table.td>
                                    {{-- Ordinario --}}
                                    <x-table.td>
                                        {{ intdiv($record['ordinary_minutes'], 60) }}h
                                        {{ $record['ordinary_minutes'] % 60 }}m
                                    </x-table.td>
                                    {{-- Extra --}}
                                    <x-table.td>
                                        @if ($record['overtime_minutes'] > 0)
                                            <span class="font-semibold text-red-600">
                                                {{ intdiv($record['overtime_minutes'], 60) }}h
                                                {{ $record['overtime_minutes'] % 60 }}m
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
                </div>
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
