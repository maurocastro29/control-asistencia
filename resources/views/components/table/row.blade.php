<tr {{ $attributes->merge([
    'class' => 'hover:bg-slate-50 transition-colors',
]) }}>
    {{ $slot }}
</tr>
