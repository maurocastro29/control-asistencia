<div class="space-y-8">
    {{-- Información general --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-form.input name="name" label="Nombre" :value="old('name', $workSchedule->name ?? '')" required />
        <x-form.input name="weekly_minutes" label="Horas semanales (minutos)" type="number" min="1" max="10080"
            :value="old('weekly_minutes', $workSchedule->weekly_minutes ?? 2520)" required />
        <x-form.switch name="is_active" label="Horario activo" :checked="old('is_active', $workSchedule->is_active ?? true)" />
    </div>
    {{-- Descripción --}}
    <div>
        <x-form.textarea name="description" label="Descripción" :value="old('description', $workSchedule->description ?? '')" rows="3" />
    </div>
    {{-- Configuración semanal --}}
    <div>
        <h3 class="text-lg font-semibold text-slate-800">
            Configuración semanal
        </h3>
        <p class="text-sm text-slate-500 mt-1 mb-6">
            Configure los días laborables, horarios de entrada y salida,
            tiempo de almuerzo y las horas ordinarias correspondientes.
        </p>
        <x-table.table>
            <x-table.head>
                <x-table.row>
                    <x-table.th>Día</x-table.th>
                    <x-table.th class="text-center">¿Labora?</x-table.th>
                    <x-table.th>Entrada</x-table.th>
                    <x-table.th>Salida</x-table.th>
                    <x-table.th>Almuerzo (min)</x-table.th>
                    <x-table.th> Horas ordinarias</x-table.th>
                </x-table.row>
            </x-table.head>
            <x-table.body>
                @foreach ($weekDays as $index => $weekDay)
                    @php
                        $scheduleDay = isset($workSchedule)
                            ? optional($workSchedule->days->firstWhere('week_day_id', $weekDay->id))
                            : null;
                        $ordinaryMinutes = old("days.$index.ordinary_minutes", $scheduleDay?->ordinary_minutes ?? 0);
                    @endphp
                    <x-table.row>
                        {{-- Día --}}
                        <x-table.td>
                            {{ $weekDay->name }}
                            <input type="hidden" name="days[{{ $index }}][week_day_id]"
                                value="{{ $weekDay->id }}">
                        </x-table.td>
                        {{-- ¿Labora? --}}
                        <x-table.td class="text-center">
                            <input type="hidden" name="days[{{ $index }}][is_working_day]" value="0">
                            <input type="checkbox" name="days[{{ $index }}][is_working_day]" value="1"
                                @checked(old("days.$index.is_working_day", $scheduleDay?->is_working_day ?? $weekDay->is_working_day_default))>
                        </x-table.td>
                        {{-- Entrada --}}
                        <x-table.td>
                            <input type="time" id="entry_time_{{ $index }}"
                                class="w-full rounded-md border-gray-300 shadow-sm"
                                name="days[{{ $index }}][entry_time]"
                                value="{{ old("days.$index.entry_time", $scheduleDay?->entry_time?->format('H:i')) }}">
                        </x-table.td>
                        {{-- Salida --}}
                        <x-table.td>
                            <input type="time" id="exit_time_{{ $index }}"
                                class="w-full rounded-md border-gray-300 shadow-sm"
                                name="days[{{ $index }}][exit_time]"
                                value="{{ old("days.$index.exit_time", $scheduleDay?->exit_time?->format('H:i')) }}">
                        </x-table.td>
                        {{-- Almuerzo --}}
                        <x-table.td>
                            <input type="number" min="0" id="lunch_minutes_{{ $index }}"
                                class="w-full rounded-md border-gray-300 shadow-sm"
                                name="days[{{ $index }}][lunch_minutes]"
                                value="{{ old("days.$index.lunch_minutes", $scheduleDay?->lunch_minutes ?? 60) }}">
                        </x-table.td>
                        {{-- Horas ordinarias --}}
                        <x-table.td>
                            {{-- Valor visible para el usuario --}}
                            <input type="text" readonly id="ordinary_hours_{{ $index }}"
                                class="w-full rounded-md border-gray-300 bg-slate-100 text-slate-600 shadow-sm"
                                value="{{ number_format($ordinaryMinutes / 60, 1) }}">
                            {{-- Valor real enviado a Laravel: minutos --}}
                            <input type="hidden" id="ordinary_minutes_{{ $index }}"
                                name="days[{{ $index }}][ordinary_minutes]" value="{{ $ordinaryMinutes }}">
                        </x-table.td>
                    </x-table.row>
                @endforeach
            </x-table.body>
        </x-table.table>
    </div>
    {{-- Acciones --}}
    <x-form.actions :cancelRoute="route('work-schedules.index')" />
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function calculateOrdinaryMinutes(index) {
            const entry = document.getElementById(`entry_time_${index}`);
            const exit = document.getElementById(`exit_time_${index}`);
            const lunch = document.getElementById(`lunch_minutes_${index}`);
            const ordinaryMinutes = document.getElementById(`ordinary_minutes_${index}`);
            const ordinaryHours = document.getElementById(`ordinary_hours_${index}`);
            if (!entry || !exit || !ordinaryMinutes || !ordinaryHours) {
                return;
            }
            if (!entry.value || !exit.value) {
                ordinaryMinutes.value = 0;
                ordinaryHours.value = '0.0';
                return;
            }
            const [entryHour, entryMinute] = entry.value.split(':').map(Number);
            const [exitHour, exitMinute] = exit.value.split(':').map(Number);
            let entryTotalMinutes = (entryHour * 60) + entryMinute;
            let exitTotalMinutes = (exitHour * 60) + exitMinute;
            // Si la salida es menor que la entrada,
            // asumimos que termina al día siguiente.
            if (exitTotalMinutes < entryTotalMinutes) {
                exitTotalMinutes += 24 * 60;
            }
            const lunchMinutes = parseInt(lunch.value) || 0;
            let workedMinutes = exitTotalMinutes - entryTotalMinutes;
            // Restamos el tiempo de almuerzo.
            workedMinutes -= lunchMinutes;
            // Nunca permitimos minutos negativos.
            workedMinutes = Math.max(workedMinutes, 0);
            // Guardamos minutos en el campo hidden.
            ordinaryMinutes.value = workedMinutes;
            // Mostramos horas solamente.
            const hours = workedMinutes / 60;
            ordinaryHours.value = hours.toFixed(1);
        }
        @foreach ($weekDays as $index => $weekDay)
            const entry{{ $index }} = document.getElementById('entry_time_{{ $index }}');
            const exit{{ $index }} = document.getElementById('exit_time_{{ $index }}');
            const lunch{{ $index }} = document.getElementById('lunch_minutes_{{ $index }}');
            entry{{ $index }}?.addEventListener('change', function() {
                calculateOrdinaryMinutes({{ $index }});
            });
            exit{{ $index }}?.addEventListener('change', function() {
                calculateOrdinaryMinutes({{ $index }});
            });
            lunch{{ $index }}?.addEventListener('input', function() {
                calculateOrdinaryMinutes({{ $index }});
            });
            // Calculamos también al cargar la página.
            calculateOrdinaryMinutes({{ $index }});
        @endforeach
    });
</script>
