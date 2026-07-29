@props(['name', 'label', 'checked' => true])

<div>

    <label class="inline-flex items-center gap-3">

        <input type="checkbox" name="{{ $name }}" value="1" {{ old($name, $checked) ? 'checked' : '' }}>

        <span>

            {{ $label }}

        </span>

    </label>

</div>
