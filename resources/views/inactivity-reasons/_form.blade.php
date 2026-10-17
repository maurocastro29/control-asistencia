@csrf
<x-form.input type="text" name="name" label="Nombre del motivo" :value="old('name', $inactivityReason->name ?? '')"
    placeholder="Ej. Vacaciones" required />
<div class="md:col-span-2">
    <label for="description" class="mb-1 block text-sm font-medium text-slate-700">Descripción</label>
    <textarea id="description" name="description" rows="4" maxlength="1000"
        class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $inactivityReason->description ?? '') }}</textarea>
    @error('description')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
<div class="md:col-span-2">
    <label class="inline-flex cursor-pointer items-center gap-3">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $inactivityReason->is_active ?? true))
            class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
        <span class="text-sm font-medium text-slate-700">Motivo activo</span>
    </label>
</div>