<div class="space-y-6">

    <x-form.input name="name" label="Nombre" :value="$documentType->name ?? ''" required />

    <x-form.input name="abbreviation" label="Descripción" :value="$documentType->description ?? ''" required />

    <x-form.textarea name="description" label="Descripción" :value="$documentType->description ?? ''" />

    <x-form.switch name="is_active" label="Activo" :checked="$documentType->is_active ?? true" />

    <x-form.actions :cancelRoute="route('document-types.index')" />

</div>
