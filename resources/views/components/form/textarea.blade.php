@props(['name', 'label', 'value' => '', 'required' => false])

<div>

    <label for="{{ $name }}" class="block mb-2 text-sm font-medium text-gray-700">

        {{ $label }}

        @if ($required)
            <span class="text-red-500">*</span>
        @endif

    </label>

    <textarea id="{{ $name }}" name="{{ $name }}" rows="4" @required($required)
        {{ $attributes->merge([
            'class' => 'w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
        ]) }}>{{ old($name, $value) }}</textarea>

    @error($name)
        <p class="mt-1 text-sm text-red-600">

            {{ $message }}

        </p>
    @enderror

</div>
