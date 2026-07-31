@props(['name', 'label', 'value' => null, 'required' => false])

<div>

    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">

        {{ $label }}

        @if ($required)
            <span class="text-red-500">*</span>
        @endif

    </label>

    <input type="date" id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $value) }}"
        @required($required)
        {{ $attributes->merge([
            'class' => 'w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
        ]) }}>

    @error($name)
        <p class="mt-1 text-sm text-red-600">

            {{ $message }}

        </p>
    @enderror

</div>
