@props(['name', 'label', 'value' => ''])

<div>

    <label class="block mb-2 text-sm font-medium">

        {{ $label }}

    </label>

    <textarea name="{{ $name }}" rows="4"
        {{ $attributes->merge([
            'class' => 'w-full rounded-lg border-slate-300',
        ]) }}>{{ old($name, $value) }}</textarea>

    @error($name)
        <p class="mt-1 text-sm text-red-600">

            {{ $message }}

        </p>
    @enderror

</div>
