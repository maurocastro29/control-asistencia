@props([
    'href' => null,
])

@if ($href)
    <a href="{{ $href }}"
        {{ $attributes->merge([
            'class' =>
                'inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition',
        ]) }}>
        {{ $slot }}
    </a>
@else
    <button
        {{ $attributes->merge([
            'class' =>
                'inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition',
        ]) }}>
        {{ $slot }}
    </button>
@endif
