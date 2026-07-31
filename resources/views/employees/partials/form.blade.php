<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <x-form.select name="document_type_id" label="Tipo de documento" required>

        <option value="">
            Seleccione...
        </option>

        @foreach ($documentTypes as $documentType)
            <option value="{{ $documentType->id }}" @selected(old('document_type_id', $employee->document_type_id ?? '') == $documentType->id)>

                {{ $documentType->name }}

            </option>
        @endforeach

    </x-form.select>

    <x-form.input name="document_number" label="Número de documento" :value="old('document_number', $employee->document_number ?? '')" required />

    <x-form.input name="first_name" label="Primer nombre" :value="old('first_name', $employee->first_name ?? '')" required />

    <x-form.input name="middle_name" label="Segundo nombre" :value="old('middle_name', $employee->middle_name ?? '')" />

    <x-form.input name="first_last_name" label="Primer apellido" :value="old('first_last_name', $employee->first_last_name ?? '')" required />

    <x-form.input name="second_last_name" label="Segundo apellido" :value="old('second_last_name', $employee->second_last_name ?? '')" />

    <x-form.select name="department_id" label="Departamento" required>

        <option value="">
            Seleccione...
        </option>

        @foreach ($departments as $department)
            <option value="{{ $department->id }}" @selected(old('department_id', $employee->department_id ?? '') == $department->id)>

                {{ $department->name }}

            </option>
        @endforeach

    </x-form.select>

    <x-form.select name="position_id" label="Cargo" required>

        <option value="">
            Seleccione...
        </option>

        @foreach ($positions as $position)
            <option value="{{ $position->id }}" @selected(old('position_id', $employee->position_id ?? '') == $position->id)>

                {{ $position->name }}

            </option>
        @endforeach

    </x-form.select>

    <x-form.select name="work_schedule_id" label="Horario laboral">

        <option value="">
            Sin horario asignado
        </option>

        @foreach ($workSchedules as $schedule)
            <option value="{{ $schedule->id }}" @selected(old('work_schedule_id', $employee->work_schedule_id ?? '') == $schedule->id)>

                {{ $schedule->name }}

            </option>
        @endforeach

    </x-form.select>

    <x-form.select name="is_active" label="Estado" required>

        <option value="1" @selected(old('is_active', $employee->is_active ?? true))>

            Activo

        </option>

        <option value="0" @selected(old('is_active', $employee->is_active ?? true) == false)>

            Inactivo

        </option>

    </x-form.select>

</div>

<div class="flex justify-end gap-3 mt-8">

    <x-button.secondary :href="route('employees.index')">

        Cancelar

    </x-button.secondary>

    <x-button.primary>

        {{ isset($employee) ? 'Actualizar' : 'Guardar' }}

    </x-button.primary>

</div>
