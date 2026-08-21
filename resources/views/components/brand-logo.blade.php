@props([
    'light' => false,
    'compact' => false,
])

<div {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
    <span
        class="flex {{ $compact ? 'h-9 w-9 rounded-xl' : 'h-11 w-11 rounded-2xl' }} shrink-0 items-center justify-center bg-cyan-400 shadow-sm shadow-cyan-900/20"
        aria-hidden="true">
        <svg class="{{ $compact ? 'h-6 w-6' : 'h-7 w-7' }} text-slate-950" viewBox="0 0 32 32" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path
                d="M8 25V7h8.4c5.1 0 8.6 2.7 8.6 7.1 0 4.5-3.5 7.1-8.6 7.1H12V25H8Zm4-7.4h4.1c3.1 0 4.8-1.1 4.8-3.5s-1.7-3.5-4.8-3.5H12v7Z"
                fill="currentColor" />
            <path d="M20.8 6.3c2.7.4 4.7 1.5 6.1 3.3-1.4 1-2.6 1.9-3.7 2.7-.5-2.1-1.3-4.1-2.4-6Z" fill="#0E7490" />
        </svg>
    </span>
    <span class="min-w-0">
        <span
            class="block text-lg font-bold leading-none tracking-tight {{ $light ? 'text-white' : 'text-slate-900' }}">Pangea</span>
        @unless ($compact)
            <span
                class="mt-1 block text-[10px] font-semibold uppercase tracking-[0.16em] {{ $light ? 'text-cyan-200' : 'text-slate-500' }}">Control
                de asistencia</span>
        @endunless
    </span>
</div>
