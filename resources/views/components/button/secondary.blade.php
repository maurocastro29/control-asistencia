@props([
    'href' => null,
])

@if ($href)
    <a href="{{ $href }}"
        {{ $attributes->merge([
            'class' =>
                'inline-flex items-center justify-center px-4 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-100 transition',
        ]) }}>

        {{ $slot }}

    </a>
@else
    <button
        {{ $attributes->merge([
            'class' =>
                'inline-flex items-center justify-center px-4 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-100 transition',
        ]) }}>

        {{ $slot }}

    </button>
@endif
