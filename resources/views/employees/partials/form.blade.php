<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @if ($errors->any())
        <div class="bg-red-100 border border-red-300 rounded p-4 mb-4">
            <ul>
                @foreach ($errors->messages() as $field => $messages)
                    <li>
                        <strong>{{ $field }}</strong>:
                        {{ implode(', ', $messages) }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
    <x-form.select name="document_type_id" label="Tipo de documento" :options="$documentTypes" :selected="old('document_type_id', $employee->document_type_id ?? null)" required />
    <x-form.input name="document_number" label="Número de documento" :value="old('document_number', $employee->document_number ?? '')" required />
    <x-form.input name="first_name" label="Primer nombre" :value="old('first_name', $employee->first_name ?? '')" required />
    <x-form.input name="middle_name" label="Segundo nombre" :value="old('middle_name', $employee->middle_name ?? '')" />
    <x-form.input name="first_last_name" label="Primer apellido" :value="old('first_last_name', $employee->first_last_name ?? '')" required />
    <x-form.input name="second_last_name" label="Segundo apellido" :value="old('second_last_name', $employee->second_last_name ?? '')" />
    <x-form.input type="date" name="hire_date" label="Fecha de ingreso" :value="old('hire_date', isset($employee) ? optional($employee->hire_date)->format('Y-m-d') : '')" required />
    <x-form.select name="department_id" label="Departamento" :options="$departments" :selected="old('department_id', $employee->department_id ?? null)" required />
    <x-form.select name="position_id" label="Cargo" :options="$positions" :selected="old('position_id', $employee->position_id ?? null)" required />
    <x-form.select name="work_schedule_id" label="Horario laboral" :options="$workSchedules" :selected="old('work_schedule_id', $employee->work_schedule_id ?? null)"
        placeholder="Sin horario asignado" />
    <x-form.switch name="is_active" label="Activo" :checked="old('is_active', $employee->is_active ?? true)" />
</div>
<div class="flex justify-end gap-3 mt-8">
    <x-button.secondary :href="route('employees.index')">
        Cancelar
    </x-button.secondary>
    <x-button.primary>
        {{ isset($employee) ? 'Actualizar' : 'Guardar' }}
    </x-button.primary>
</div>
