@csrf

<x-form.input type="date" name="date" label="Fecha" :value="old('date', isset($holiday) ? $holiday->date->format('Y-m-d') : now()->format('Y-m-d'))" required />

<x-form.input type="text" name="name" label="Nombre del festivo" :value="old('name', $holiday->name ?? '')"
    placeholder="Ej. Día de la Independencia" required />

<div class="md:col-span-2">
    <label class="inline-flex items-center gap-3 cursor-pointer">

        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $holiday->is_active ?? true))
            class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500">

        <span class="text-sm font-medium text-slate-700">
            Festivo activo
        </span>

    </label>
</div>
