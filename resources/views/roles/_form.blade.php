@csrf

<x-form.input name="name" label="Nombre" :value="old('name', $role->name ?? '')" required />

<x-form.textarea name="description" label="Descripción" :value="old('description', $role->description ?? '')" />

<x-form.switch name="is_active" label="Rol activo" :checked="old('is_active', $role->is_active ?? true)" />

<div class="flex justify-end gap-3 mt-6">

    <x-button.secondary :href="route('roles.index')">
        Cancelar
    </x-button.secondary>

    <x-button.primary>
        Guardar
    </x-button.primary>

</div>
