@props(['name', 'label', 'checked' => true])

<input type="hidden" name="{{ $name }}" value="0">

<label class="inline-flex items-center gap-3">

    <input type="checkbox" name="{{ $name }}" value="1" @checked(old($name, $checked))>

    <span>
        {{ $label }}
    </span>

</label>

@error($name)
    <p class="mt-1 text-sm text-red-600">
        {{ $message }}
    </p>
@enderror
