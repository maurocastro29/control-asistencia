@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <x-form.select name="employee_id" label="Empleado" :options="$employees" optionValue="id" optionLabel="full_name"
        :selected="old('employee_id', $attendanceRecord->employee_id ?? '')" required />
    <x-form.input type="date" name="work_date" label="Fecha" :value="old(
        'work_date',
        isset($attendanceRecord) ? $attendanceRecord->work_date->format('Y-m-d') : now()->format('Y-m-d'),
    )" required />
    <x-form.input type="time" name="entry_time" label="Hora de entrada" :value="old('entry_time', $attendanceRecord->entry_time?->format('H:i') ?? '')" required />
    <x-form.input type="time" name="exit_time" label="Hora de salida" :value="old('exit_time', $attendanceRecord->exit_time?->format('H:i') ?? '')" required />
    <x-form.input type="number" name="lunch_time" label="Tiempo de almuerzo (Horas)" step="0.5" min="0"
        :value="old('lunch_time', isset($attendanceRecord) ? $attendanceRecord->lunch_time / 60 : 1)" required />
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
