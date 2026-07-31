<x-layout.page-header title="Detalle del Empleado" subtitle="Información del empleado">

    <x-slot:actions>

        <x-button.secondary :href="route('employees.edit', $employee)">

            Editar

        </x-button.secondary>

        <x-button.secondary :href="route('employees.index')">

            Volver

        </x-button.secondary>

    </x-slot:actions>

</x-layout.page-header>

<x-layout.card>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <div>

            <label class="block text-sm font-medium text-gray-500">
                Tipo de documento
            </label>

            <p class="mt-1 text-gray-900">
                {{ $employee->documentType->name }}
            </p>

        </div>

        <div>

            <label class="block text-sm font-medium text-gray-500">
                Número de documento
            </label>

            <p class="mt-1 text-gray-900">
                {{ $employee->document_number }}
            </p>

        </div>

        <div>

            <label class="block text-sm font-medium text-gray-500">
                Primer nombre
            </label>

            <p class="mt-1 text-gray-900">
                {{ $employee->first_name }}
            </p>

        </div>

        <div>

            <label class="block text-sm font-medium text-gray-500">
                Segundo nombre
            </label>

            <p class="mt-1 text-gray-900">
                {{ $employee->middle_name ?: '-' }}
            </p>

        </div>

        <div>

            <label class="block text-sm font-medium text-gray-500">
                Primer apellido
            </label>

            <p class="mt-1 text-gray-900">
                {{ $employee->first_last_name }}
            </p>

        </div>

        <div>

            <label class="block text-sm font-medium text-gray-500">
                Segundo apellido
            </label>

            <p class="mt-1 text-gray-900">
                {{ $employee->second_last_name ?: '-' }}
            </p>

        </div>

        <div>

            <label class="block text-sm font-medium text-gray-500">
                Departamento
            </label>

            <p class="mt-1 text-gray-900">
                {{ $employee->department?->name }}
            </p>

        </div>

        <div>

            <label class="block text-sm font-medium text-gray-500">
                Cargo
            </label>

            <p class="mt-1 text-gray-900">
                {{ $employee->position?->name }}
            </p>

        </div>

        <div>

            <label class="block text-sm font-medium text-gray-500">
                Horario laboral
            </label>

            <p class="mt-1 text-gray-900">
                {{ $employee->workSchedule?->name ?? 'Sin asignar' }}
            </p>

        </div>

        <div>

            <label class="block text-sm font-medium text-gray-500">
                Estado
            </label>

            <div class="mt-1">

                <x-table.badge :active="$employee->is_active" />

            </div>

        </div>

    </div>

</x-layout.card>
