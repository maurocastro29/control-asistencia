<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-800">
            Registro de Asistencia
        </h2>
    </x-slot>

    {{-- Mensajes --}}
    @if (session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- ==========================
        BUSCAR EMPLEADO
    =========================== --}}
    <x-card>

        <form action="{{ route('attendance.search') }}" method="POST">

            @csrf

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">

                <div class="md:col-span-4">

                    <x-form.input name="search" label="Buscar empleado" placeholder="Documento, nombre o apellido..."
                        :value="old('search')" required />

                </div>

                <div>

                    <x-button.primary type="submit" class="w-full">

                        Buscar

                    </x-button.primary>

                </div>

            </div>

        </form>

    </x-card>

    {{-- ==========================
        RESULTADOS
    =========================== --}}
    @if (isset($employees) && $employees->count())

        <div class="mt-6">

            <x-card>

                <h3 class="text-lg font-semibold mb-4">
                    Resultados de la búsqueda
                </h3>

                <x-table>

                    <x-slot name="head">

                        <tr>
                            <th>Documento</th>
                            <th>Empleado</th>
                            <th>Departamento</th>
                            <th>Cargo</th>
                            <th class="text-center">Acción</th>
                        </tr>

                    </x-slot>

                    <x-slot name="body">

                        @foreach ($employees as $employee)
                            <tr>

                                <td>
                                    {{ $employee->document_number }}
                                </td>

                                <td>
                                    {{ $employee->full_name }}
                                </td>

                                <td>
                                    {{ $employee->department->name }}
                                </td>

                                <td>
                                    {{ $employee->position->name }}
                                </td>

                                <td class="text-center">

                                    <x-button.primary href="{{ route('attendance.select', $employee) }}">

                                        Seleccionar

                                    </x-button.primary>

                                </td>

                            </tr>
                        @endforeach

                    </x-slot>

                </x-table>

            </x-card>

        </div>

    @endif

    {{-- ==========================
        FORMULARIO DE REGISTRO
    =========================== --}}
    @if (isset($selectedEmployee) && $selectedEmployee)
        <div class="mt-6">

            <x-card>

                <h3 class="text-lg font-semibold mb-6">
                    Registrar Jornada
                </h3>

                <form action="{{ route('attendance.store') }}" method="POST">

                    @csrf

                    <input type="hidden" name="employee_id" value="{{ $selectedEmployee->id }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <x-form.input label="Empleado" :value="$selectedEmployee->full_name" disabled />

                        <x-form.input label="Documento" :value="$selectedEmployee->document_number" disabled />

                        <x-form.input label="Departamento" :value="$selectedEmployee->department->name" disabled />

                        <x-form.input label="Cargo" :value="$selectedEmployee->position->name" disabled />

                        <x-form.input type="date" name="work_date" label="Fecha" :value="old('work_date', now()->format('Y-m-d'))" required />

                        <x-form.input type="time" name="entry_time" label="Hora de entrada" :value="old('entry_time')"
                            required />

                        <x-form.input type="time" name="exit_time" label="Hora de salida" :value="old('exit_time')"
                            required />

                        <x-form.input type="number" name="lunch_time" label="Tiempo de almuerzo (Horas)" step="0.5"
                            min="0" :value="old('lunch_time', 1)" required />

                        <div class="md:col-span-2">

                            <x-form.textarea name="observations" label="Observaciones">

                                {{ old('observations') }}

                            </x-form.textarea>

                        </div>

                    </div>

                    <div class="mt-6 flex justify-end gap-3">

                        <x-button.secondary href="{{ route('attendance.register') }}">

                            Cancelar

                        </x-button.secondary>

                        <x-button.primary type="submit">

                            Registrar Jornada

                        </x-button.primary>

                    </div>

                </form>

            </x-card>

        </div>
    @endif

</x-app-layout>
