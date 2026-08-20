<x-app-layout>
    <x-layout.page-header class="m-6" title="Detalle del festivo" subtitle="Consulta la información del día festivo.">
        <x-slot:actions>
            @can('holidays.edit')
                <x-button.primary :href="route('holidays.edit')">
                    Editar
                </x-button.primary>
            @endcan
        </x-slot:actions>
    </x-layout.page-header>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-slate-500">
                                Fecha
                            </dt>
                            <dd class="mt-1 text-sm text-slate-900">
                                {{ $holiday->date->format('d/m/Y') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">
                                Nombre
                            </dt>
                            <dd class="mt-1 text-sm text-slate-900">
                                {{ $holiday->name }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">
                                Estado
                            </dt>
                            <dd class="mt-1">
                                @if ($holiday->is_active)
                                    <span
                                        class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">
                                        Activo
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">
                                        Inactivo
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">
                                Registrado
                            </dt>
                            <dd class="mt-1 text-sm text-slate-900">
                                {{ $holiday->created_at->format('d/m/Y H:i') }}
                            </dd>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end gap-3">
                        <x-button.secondary href="{{ route('holidays.index') }}">
                            Volver
                        </x-button.secondary>
                        <x-button.primary href="{{ route('holidays.edit', $holiday) }}">
                            Editar
                        </x-button.primary>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
