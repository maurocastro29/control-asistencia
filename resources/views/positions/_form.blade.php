<div class="space-y-6">
    <x-form.input name="name" label="Nombre" :value="$position->name ?? ''" required />
    <x-form.textarea name="description" label="Descripción" :value="$position->description ?? ''" />
    <x-form.switch name="is_active" label="Activo" :checked="$position->is_active ?? true" />
    <x-form.actions :cancelRoute="route('positions.index')" />
</div>
