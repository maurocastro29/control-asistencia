<x-app-layout>
    <x-layout.page-header title="Editar rol" subtitle="Actualizar la información del perfil" />

    <x-layout.card>
        <form method="POST" action="{{ route('roles.update', $role) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Nombre del rol</label>
                <input id="name" name="name" type="text" value="{{ old('name', $role->name) }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                    placeholder="Ej. Administrador" required>
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <input id="is_active" name="is_active" type="checkbox" value="1"
                    {{ old('is_active', $role->is_active) ? 'checked' : '' }}
                    class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <label for="is_active" class="text-sm font-medium text-slate-700">Rol activo</label>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <x-button.primary type="submit">Actualizar rol</x-button.primary>
                <x-button.secondary :href="route('settings.roles.edit', $role)">Gestionar permisos</x-button.secondary>
                <x-button.secondary :href="route('roles.index')">Volver</x-button.secondary>
            </div>
        </form>
    </x-layout.card>
</x-app-layout>
