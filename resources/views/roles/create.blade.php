<x-app-layout>
    <x-layout.page-header title="Nuevo rol" subtitle="Crear un perfil de acceso para el sistema" />

    <x-layout.card>
        <form method="POST" action="{{ route('roles.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Nombre del rol</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                    placeholder="Ej. Administrador" required>
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <input id="is_active" name="is_active" type="checkbox" value="1"
                    {{ old('is_active', true) ? 'checked' : '' }}
                    class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <label for="is_active" class="text-sm font-medium text-slate-700">Rol activo</label>
            </div>

            <div class="flex items-center gap-3">
                <x-button.primary type="submit">Guardar rol</x-button.primary>
                <x-button.secondary :href="route('roles.index')">Cancelar</x-button.secondary>
            </div>
        </form>
    </x-layout.card>
</x-app-layout>
