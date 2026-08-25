<x-app-layout>
    <x-layout.page-header title="Editar ajuste de jornada"
        subtitle="Modifica la información del ajuste de jornada seleccionado.">
    </x-layout.page-header>
    <div class="py-6">
        <div class="w-full mx-auto">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('work-schedule-adjustments.update', $adjustment) }}"
                        class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        @csrf
                        @method('PUT')
                        {{-- Empleado --}}
                        <div>
                            <label for="employee_id" class="block text-sm font-medium text-slate-700">
                                Empleado
                            </label>
                            <select id="employee_id" name="employee_id" required
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">
                                    Selecciona un empleado
                                </option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" @selected(old('employee_id', $adjustment->employee_id) == $employee->id)>
                                        {{ $employee->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        {{-- Minutos reducidos --}}
                        <div>
                            <label for="reduced_minutes" class="block text-sm font-medium text-slate-700">
                                Tiempo reducido
                            </label>
                            <div class="mt-1 flex items-center gap-3">
                                <input type="number" id="reduced_minutes" name="reduced_minutes"
                                    value="{{ old('reduced_minutes', $adjustment->reduced_minutes) }}" min="1"
                                    required
                                    class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <span class="text-sm text-slate-500 whitespace-nowrap">
                                    minutos
                                </span>
                            </div>
                            <p class="mt-1 text-xs text-slate-500">
                                Ejemplo: 60 minutos = 1 hora.
                            </p>
                            @error('reduced_minutes')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        {{-- Fecha del ajuste --}}
                        <div>
                            <label for="adjustment_date" class="block text-sm font-medium text-slate-700">
                                Fecha del ajuste
                            </label>
                            <input type="date" id="adjustment_date" name="adjustment_date"
                                value="{{ old('adjustment_date', $adjustment->adjustment_date->format('Y-m-d')) }}"
                                required
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('adjustment_date')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        {{-- Fecha de compensación --}}
                        <div>
                            <label for="compensation_date" class="block text-sm font-medium text-slate-700">
                                Fecha de compensación
                            </label>
                            <input type="date" id="compensation_date" name="compensation_date"
                                value="{{ old('compensation_date', $adjustment->compensation_date?->format('Y-m-d')) }}"
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('compensation_date')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        {{-- Motivo --}}
                        <div class="md:col-span-2">
                            <label for="reason" class="block text-sm font-medium text-slate-700">
                                Motivo
                            </label>
                            <textarea id="reason" name="reason" rows="4" maxlength="1000"
                                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Describe el motivo del ajuste...">{{ old('reason', $adjustment->reason) }}</textarea>
                            @error('reason')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        {{-- Botones --}}
                        <div class="md:col-span-2 flex justify-end gap-3 border-t border-slate-200 pt-4">
                            <x-button.secondary :href="route('work-schedule-adjustments.index')">
                                Cancelar
                            </x-button.secondary>
                            <x-button.primary type="submit">
                                Guardar cambios
                            </x-button.primary>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
