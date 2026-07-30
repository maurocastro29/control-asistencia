@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <x-form.select name="employee_id" label="Empleado" :options="$employees->pluck('full_name', 'id')" :selected="old('employee_id', $attendanceRecord->employee_id ?? '')" required />

    <x-form.select name="attendance_type_id" label="Tipo de marcación" :options="$attendanceTypes->pluck('name', 'id')" :selected="old('attendance_type_id', $attendanceRecord->attendance_type_id ?? '')" required />

    <x-form.input type="datetime-local" name="attendance_datetime" label="Fecha y hora" :value="old(
        'attendance_datetime',
        isset($attendanceRecord)
            ? $attendanceRecord->attendance_datetime->format('Y-m-d\TH:i')
            : now()->format('Y-m-d\TH:i'),
    )" required />

    <div class="md:col-span-2">
        <x-form.textarea name="observations" label="Observaciones" :value="old('observations', $attendanceRecord->observations ?? '')" />
    </div>

</div>

<div class="mt-6 flex justify-end gap-2">

    <x-button.secondary href="{{ route('attendance-records.index') }}">
        Cancelar
    </x-button.secondary>

    <x-button.primary type="submit">
        Guardar
    </x-button.primary>

</div>
