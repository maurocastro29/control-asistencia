<x-app-layout>
    <x-layout.page-header class="m-6" title="Nuevo motivo de inactividad" subtitle="Registra un motivo reutilizable para las asignaciones.">
        <x-slot:actions>
            <x-button.primary :href="route('inactivity-reasons.index')">Volver</x-button.primary>
        </x-slot:actions>
    </x-layout.page-header>
    <div class="py-6">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3">
                    <ul class="list-inside list-disc text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="rounded-lg bg-white shadow-sm">
                <form action="{{ route('inactivity-reasons.store') }}" method="POST" class="p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        @include('inactivity-reasons._form')
                    </div>
                    <div class="mt-8 flex justify-end gap-3">
                        <x-button.secondary href="{{ route('inactivity-reasons.index') }}">Cancelar</x-button.secondary>
                        <x-button.primary type="submit">Guardar</x-button.primary>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>