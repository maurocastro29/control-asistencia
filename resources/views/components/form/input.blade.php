@props(['name', 'label', 'type' => 'text', 'value' => '', 'required' => false])

<div>

    <label for="{{ $name }}" class="block mb-2 text-sm font-medium text-slate-700">

        {{ $label }}

        @if ($required)
            <span class="text-red-500">*</span>
        @endif

    </label>

    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}"
        @required($required)
        {{ $attributes->merge([
            'class' => 'w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500',
        ]) }}>

    @error($name)
        <p class="mt-1 text-sm text-red-600">

            {{ $message }}

        </p>
    @enderror

</div>
