<x-app-layout>

    <x-layout.page-header title="Detalle del Usuario" subtitle="Información del usuario" />

    <x-layout.card>

        <div class="grid grid-cols-2 gap-6">

            <div>
                <strong>Usuario</strong>
                <p>{{ $user->username }}</p>
            </div>

            <div>
                <strong>Rol</strong>
                <p>{{ $user->role->name }}</p>
            </div>

            <div>
                <strong>Nombre completo</strong>
                <p>{{ $user->full_name }}</p>
            </div>

            <div>
                <strong>Rol</strong>
                <p>{{ $user->getRoleNames()->first() }}</p>
            </div>

            <div>
                <strong>Estado</strong>

                <div class="mt-2">
                    <x-table.badge :active="$user->is_active" />
                </div>

            </div>

            <div>
                <strong>Último acceso</strong>

                <p>

                    {{ $user->last_login_at?->format('d/m/Y H:i') ?? 'Nunca ha iniciado sesión' }}

                </p>

            </div>

            <div>
                <strong>Fecha de creación</strong>

                <p>

                    {{ $user->created_at->format('d/m/Y H:i') }}

                </p>

            </div>

        </div>

        <div class="mt-8">

            <x-button.secondary :href="route('users.index')">

                Volver

            </x-button.secondary>

        </div>

    </x-layout.card>

</x-app-layout>
