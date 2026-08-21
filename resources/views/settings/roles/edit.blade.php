<x-app-layout>
    <x-layout.page-header title="Permisos del rol" subtitle="{{ $role->name }}">
        <x-slot:actions>
            <x-button.secondary :href="route('settings.index')">Volver a configuración</x-button.secondary>
        </x-slot:actions>
    </x-layout.page-header>

    @if ($errors->any())
        <x-alert.error>
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert.error>
    @endif

    <x-layout.card>
        <div
            class="mb-6 flex flex-col gap-2 border-b border-slate-200 pb-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-slate-800">Autorizaciones por módulo</h3>
                <p class="mt-1 text-sm text-slate-500">Solo los permisos activos pueden asignarse y surtir efecto.</p>
            </div>
            <x-table.badge :active="$role->is_active" />
        </div>

        <form method="POST" action="{{ route('settings.roles.update', $role) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($permissions as $module => $modulePermissions)
                    <section class="rounded-lg border border-slate-200 p-4">
                        <h4 class="mb-3 text-sm font-semibold capitalize text-slate-700">
                            {{ str_replace('-', ' ', $module) }}</h4>
                        <div class="space-y-3">
                            @foreach ($modulePermissions as $permission)
                                @php
                                    $selectedPermissions = old(
                                        'permission_ids',
                                        $role->permissions->pluck('id')->all(),
                                    );
                                @endphp
                                <label
                                    class="flex items-start gap-3 {{ !$permission->is_active ? 'cursor-not-allowed opacity-50' : 'cursor-pointer' }}">
                                    <input type="checkbox" name="permission_ids[]" value="{{ $permission->id }}"
                                        @checked(in_array($permission->id, $selectedPermissions)) @disabled(!$permission->is_active)
                                        class="mt-0.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span>
                                        <span
                                            class="block text-sm font-medium text-slate-700">{{ $permission->display_name }}</span>
                                        <span class="block text-xs text-slate-400">{{ $permission->name }}</span>
                                        <span
                                            class="text-xs {{ $permission->is_active ? 'text-green-600' : 'text-slate-400' }}">{{ $permission->is_active ? 'Activo' : 'Inactivo' }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>

            @can('settings.edit')
                <div class="mt-6 flex justify-end border-t border-slate-200 pt-5">
                    <x-button.primary type="submit">Guardar permisos</x-button.primary>
                </div>
            @endcan
        </form>
    </x-layout.card>
</x-app-layout>
