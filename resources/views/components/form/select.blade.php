@props([
    'name',
    'label',
    'options' => [],
    'optionValue' => 'id',
    'optionLabel' => 'name',
    'selected' => null,
    'required' => false,
])

<div>

    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">

        {{ $label }}

    </label>

    <select id="{{ $name }}" name="{{ $name }}" @required($required)
        {{ $attributes->merge([
            'class' => 'w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
        ]) }}>

        <option value="">
            Seleccione...
        </option>

        @foreach ($options as $option)
            <option value="{{ $option->{$optionValue} }}" @selected(old($name, $selected) == $option->{$optionValue})>

                {{ $option->{$optionLabel} }}

            </option>
        @endforeach

    </select>

    @error($name)
        <p class="mt-1 text-sm text-red-600">

            {{ $message }}

        </p>
    @enderror

</div>
