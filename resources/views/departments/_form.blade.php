<div class="space-y-6">

    <x-form.input name="name" label="Nombre" :value="$department->name ?? ''" required />

    <x-form.input name="abbreviation" label="Descripción" :value="$department->description ?? ''" required />

    <x-form.textarea name="description" label="Descripción" :value="$department->description ?? ''" />

    <x-form.switch name="is_active" label="Activo" :checked="$department->is_active ?? true" />

    <x-form.actions :cancelRoute="route('departments.index')" />

</div>
