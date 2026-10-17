<x-app-layout>
    <x-layout.page-header class="m-6" title="Detalle del motivo" subtitle="Información del motivo de inactividad.">
        <x-slot:actions>
            <x-button.secondary href="{{ route('inactivity-reasons.index') }}">Volver</x-button.secondary>
            @can('inactivity-reasons.edit')
                <x-button.primary href="{{ route('inactivity-reasons.edit', $inactivityReason) }}">Editar</x-button.primary>
            @endcan
        </x-slot:actions>
    </x-layout.page-header>
    <x-layout.card>
        <dl class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-slate-500">Nombre</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ $inactivityReason->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-slate-500">Estado</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ $inactivityReason->is_active ? 'Activo' : 'Inactivo' }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-sm font-medium text-slate-500">Descripción</dt>
                <dd class="mt-1 whitespace-pre-line text-sm text-slate-900">
                    {{ $inactivityReason->description ?: 'Sin descripción' }}</dd>
            </div>
        </dl>
    </x-layout.card>
</x-app-layout>
