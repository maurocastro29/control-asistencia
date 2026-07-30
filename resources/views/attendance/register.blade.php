<x-app-layout>

    <x-slot name="header">
        {{ __('Registro de Asistencia') }}
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">

        @if (session('success'))
            <div class="rounded-lg border border-green-300 bg-green-100 px-4 py-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg border border-red-300 bg-red-100 px-4 py-3 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        {{-- Buscar empleado --}}
        <x-card>

            <form method="POST" action="{{ route('attendance.search') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

                    <div class="md:col-span-3">

                        <x-form.input name="document_number" label="Número de documento" :value="old('document_number')" required
                            autofocus />

                    </div>

                    <div>

                        <x-button.primary type="submit" class="w-full">

                            Buscar

                        </x-button.primary>

                    </div>

                </div>

            </form>

        </x-card>

        @if ($employee)
            {{-- Información del empleado --}}
            <x-card>

                <h2 class="text-lg font-semibold mb-4">
                    Información del empleado
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>
                        <strong>Documento</strong><br>
                        {{ $employee->document_number }}
                    </div>

                    <div>
                        <strong>Nombre</strong><br>
                        {{ $employee->full_name }}
                    </div>

                    <div>
                        <strong>Departamento</strong><br>
                        {{ $employee->department->name }}
                    </div>

                    <div>
                        <strong>Cargo</strong><br>
                        {{ $employee->position->name }}
                    </div>

                </div>

            </x-card>

            {{-- Registrar marcación --}}
            <x-card>

                <form method="POST" action="{{ route('attendance.store') }}">

                    @csrf

                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">

                    <div class="space-y-6">

                        <x-form.select name="attendance_type_id" label="Tipo de marcación" :options="$attendanceTypes->pluck('name', 'id')" required />

                        <x-form.textarea name="observations" label="Observaciones" :value="old('observations')" />

                        <div class="flex justify-end">

                            <x-button.primary type="submit">

                                Registrar Marcación

                            </x-button.primary>

                        </div>

                    </div>

                </form>

            </x-card>
        @endif

    </div>

</x-app-layout>
