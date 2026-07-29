@props([
    'href' => null,
])

@if ($href)
    <a href="{{ $href }}"
        {{ $attributes->merge([
            'class' => 'inline-flex items-center justify-center rounded-md p-2 hover:bg-slate-100',
        ]) }}>

        {{ $slot }}

    </a>
@else
    <button
        {{ $attributes->merge([
            'class' => 'inline-flex items-center justify-center rounded-md p-2 hover:bg-slate-100',
        ]) }}>

        {{ $slot }}

    </button>
@endif
