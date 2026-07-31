<div class="bg-white shadow rounded-lg">

    <div class="p-6 space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <x-form.input name="name" label="Nombre" :value="old('name', $workSchedule->name ?? '')" required />

            <x-form.select name="is_active" label="Estado">

                <option value="1" @selected(old('is_active', $workSchedule->is_active ?? true))>

                    Activo

                </option>

                <option value="0" @selected(old('is_active', $workSchedule->is_active ?? false) == false)>

                    Inactivo

                </option>

            </x-form.select>

        </div>

        <div>

            <x-form.textarea name="description" label="Descripción"
                rows="3">{{ old('description', $workSchedule->description ?? '') }}</x-form.textarea>

        </div>

    </div>

</div>

<div class="bg-white shadow rounded-lg mt-6">

    <div class="p-6">

        <h3 class="text-lg font-semibold mb-4">

            Configuración semanal

        </h3>

        <x-table>

            <x-slot name="head">

                <tr>

                    <x-table.th>Día</x-table.th>

                    <x-table.th>¿Labora?</x-table.th>

                    <x-table.th>Entrada</x-table.th>

                    <x-table.th>Salida</x-table.th>

                    <x-table.th>Almuerzo (min)</x-table.th>

                    <x-table.th>Minutos ordinarios</x-table.th>

                </tr>

            </x-slot>

            <x-slot name="body">

                @foreach ($weekDays as $index => $weekDay)
                    @php

                        $scheduleDay = isset($workSchedule)
                            ? optional($workSchedule->days->firstWhere('week_day_id', $weekDay->id))
                            : null;

                    @endphp

                    <tr>

                        <x-table.td>

                            {{ $weekDay->name }}

                            <input type="hidden" name="days[{{ $index }}][week_day_id]"
                                value="{{ $weekDay->id }}">

                        </x-table.td>

                        <x-table.td>

                            <input type="hidden" name="days[{{ $index }}][is_working_day]" value="0">

                            <input type="checkbox" name="days[{{ $index }}][is_working_day]" value="1"
                                @checked(old("days.$index.is_working_day", $scheduleDay?->is_working_day ?? $weekDay->is_working_day_default))>

                        </x-table.td>

                        <x-table.td>

                            <input type="time" class="w-full rounded-md border-gray-300"
                                name="days[{{ $index }}][entry_time]"
                                value="{{ old("days.$index.entry_time", $scheduleDay?->entry_time) }}">

                        </x-table.td>

                        <x-table.td>

                            <input type="time" class="w-full rounded-md border-gray-300"
                                name="days[{{ $index }}][exit_time]"
                                value="{{ old("days.$index.exit_time", $scheduleDay?->exit_time) }}">

                        </x-table.td>

                        <x-table.td>

                            <input type="number" min="0" class="w-full rounded-md border-gray-300"
                                name="days[{{ $index }}][lunch_minutes]"
                                value="{{ old("days.$index.lunch_minutes", $scheduleDay?->lunch_minutes ?? 60) }}">

                        </x-table.td>

                        <x-table.td>

                            <input type="number" min="0" class="w-full rounded-md border-gray-300"
                                name="days[{{ $index }}][ordinary_minutes]"
                                value="{{ old("days.$index.ordinary_minutes", $scheduleDay?->ordinary_minutes ?? 0) }}">

                        </x-table.td>

                    </tr>
                @endforeach

            </x-slot>

        </x-table>

    </div>

</div>

<div class="flex justify-end gap-3 mt-6">

    <a href="{{ route('work-schedules.index') }}"
        class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">

        Cancelar

    </a>

    <button type="submit"
        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">

        Guardar

    </button>

</div>
