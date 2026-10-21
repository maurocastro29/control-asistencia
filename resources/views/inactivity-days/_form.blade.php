<div>
    <label for="employee_id" class="block text-sm font-medium text-slate-700">Trabajador</label>
    <select id="employee_id" name="employee_id" required
        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <option value="">Selecciona un trabajador</option>
        @foreach ($employees as $employee)
            <option value="{{ $employee->id }}" @selected(old('employee_id', $inactivityRecord->employee_id ?? '') == $employee->id)>
                {{ $employee->full_name }}
            </option>
        @endforeach
    </select>
    @error('employee_id')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
<div>
    <label for="inactivity_reason_id" class="block text-sm font-medium text-slate-700">Motivo</label>
    <select id="inactivity_reason_id" name="inactivity_reason_id" required
        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <option value="">Selecciona un motivo</option>
        @foreach ($reasons as $reason)
            <option value="{{ $reason->id }}" @selected(old('inactivity_reason_id', $inactivityRecord->inactivity_reason_id ?? '') == $reason->id)>
                {{ $reason->name }}
            </option>
        @endforeach
    </select>
    @error('inactivity_reason_id')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
<div>
    <label for="date_from" class="block text-sm font-medium text-slate-700">Fecha inicial</label>
    <input type="date" id="date_from" name="date_from" value="{{ old('date_from', $dateFrom) }}" required
        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    @error('date_from')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
<div>
    <label for="date_to" class="block text-sm font-medium text-slate-700">Fecha final</label>
    <input type="date" id="date_to" name="date_to" value="{{ old('date_to', $dateTo) }}" required
        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    @error('date_to')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
<fieldset class="md:col-span-2">
    <legend class="block text-sm font-medium text-slate-700">Tipo de día</legend>
    <div class="mt-2 flex flex-wrap gap-6">
        <label class="inline-flex items-center gap-2">
            <input type="radio" name="is_paid" value="1" required @checked((string) old('is_paid', isset($inactivityRecord) ? (int) $inactivityRecord->is_paid : '') === '1')
                class="border-slate-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm text-slate-700">Remunerado</span>
        </label>
        <label class="inline-flex items-center gap-2">
            <input type="radio" name="is_paid" value="0" required @checked((string) old('is_paid', isset($inactivityRecord) ? (int) $inactivityRecord->is_paid : '') === '0')
                class="border-slate-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm text-slate-700">No remunerado</span>
        </label>
    </div>
    @error('is_paid')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</fieldset>
@if ($isEditing)
    <div class="md:col-span-2">
        <label class="inline-flex cursor-pointer items-center gap-3">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $inactivityRecord->is_active))
                class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500">
            <span class="text-sm font-medium text-slate-700">Asignación activa</span>
        </label>
    </div>
@endif
