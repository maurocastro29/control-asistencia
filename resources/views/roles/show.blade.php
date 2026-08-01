<x-app-layout>

    <x-layout.page-header title="Detalle del Rol" subtitle="Información del rol">

    </x-layout.page-header>

    <x-layout.card>

        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>

                <dt class="font-semibold text-gray-600">

                    Nombre

                </dt>

                <dd class="mt-1">

                    {{ $role->name }}

                </dd>

            </div>

            <div>

                <dt class="font-semibold text-gray-600">

                    Estado

                </dt>

                <dd class="mt-1">

                    <x-table.badge :active="$role->is_active" />

                </dd>

            </div>

            <div class="md:col-span-2">

                <dt class="font-semibold text-gray-600">

                    Descripción

                </dt>

                <dd class="mt-1">

                    {{ $role->description ?: 'Sin descripción.' }}

                </dd>

            </div>

        </dl>

        <div class="flex justify-end mt-8">

            <x-button.secondary :href="route('roles.index')">

                Volver

            </x-button.secondary>

        </div>

    </x-layout.card>

</x-app-layout>
