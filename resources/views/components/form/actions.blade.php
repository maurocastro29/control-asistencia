@props(['cancelRoute'])

<div class="flex justify-end gap-3 pt-4">

    <x-button.secondary :href="$cancelRoute">

        Cancelar

    </x-button.secondary>

    <x-button.primary type="submit">

        Guardar

    </x-button.primary>

</div>
