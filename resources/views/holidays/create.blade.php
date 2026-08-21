<x-app-layout>
    <x-layout.page-header class="m-6" title="Nuevo festivo" subtitle="Registra un nuevo día festivo.">
        <x-slot:actions>
            <x-button.primary :href="route('holidays.index')">
                Volver
            </x-button.primary>
        </x-slot:actions>
    </x-layout.page-header>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3">
                    <ul class="list-disc list-inside text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="bg-white shadow-sm sm:rounded-lg">
                <form action="{{ route('holidays.store') }}" method="POST" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @include('holidays._form')
                    </div>
                    <div class="mt-8 flex justify-end gap-3">
                        <x-button.secondary href="{{ route('holidays.index') }}">
                            Cancelar
                        </x-button.secondary>
                        <x-button.primary type="submit">
                            Guardar
                        </x-button.primary>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
