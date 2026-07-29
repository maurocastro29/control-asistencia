@props([
    'active' => true,
])

<span @class([
    'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium',
    'bg-green-100 text-green-800' => $active,
    'bg-red-100 text-red-800' => !$active,
])>

    {{ $active ? 'Activo' : 'Inactivo' }}

</span>
