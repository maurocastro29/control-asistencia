<x-app-layout>
    <x-layout.page-header title="Mi perfil" subtitle="Consulta y actualiza la información de tu cuenta" />

    @if (session('status') === 'profile-updated')
        <x-alert.success>La información del perfil se actualizó correctamente.</x-alert.success>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <x-layout.card>
            <div class="flex items-center gap-4">
                <div
                    class="flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 text-2xl font-bold text-indigo-700">
                    {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->first_last_name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <h2 class="truncate text-lg font-semibold text-slate-800">{{ $user->full_name }}</h2>
                    <p class="truncate text-sm text-slate-500">{{ '@' . $user->username }}</p>
                </div>
            </div>

            <dl class="mt-6 divide-y divide-slate-200 border-y border-slate-200">
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-sm text-slate-500">Rol</dt>
                    <dd class="text-right text-sm font-medium text-slate-700">
                        {{ $user->getRoleNames()->join(', ') ?: 'Sin rol' }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-sm text-slate-500">Estado</dt>
                    <dd>
                        <x-table.badge :active="$user->is_active" />
                    </dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-sm text-slate-500">Último acceso</dt>
                    <dd class="text-right text-sm font-medium text-slate-700">
                        {{ $user->last_login_at?->format('d/m/Y H:i') ?? 'Aún no registrado' }}</dd>
                </div>
            </dl>

            <p class="mt-5 text-xs leading-5 text-slate-500">El rol, el estado y el último acceso son administrados
                desde el módulo de usuarios y configuración.</p>
        </x-layout.card>

        <div class="space-y-6 xl:col-span-2">
            <x-layout.card>
                @include('profile.partials.update-profile-information-form')
            </x-layout.card>

            <x-layout.card>
                @include('profile.partials.update-password-form')
            </x-layout.card>
        </div>
    </div>
</x-app-layout>
