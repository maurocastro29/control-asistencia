<x-app-layout>

    <x-layout.page-header title="Usuarios" subtitle="Administración de los usuarios del sistema">

        <x-slot:actions>

            <x-button.primary :href="route('users.create')">
                Nuevo Usuario
            </x-button.primary>

        </x-slot:actions>

    </x-layout.page-header>

    <x-layout.card>

        <x-table.table>

            <x-table.head>

                <x-table.row>

                    <x-table.th>
                        Usuario
                    </x-table.th>

                    <x-table.th>
                        Nombre Completo
                    </x-table.th>

                    <x-table.th>
                        Rol
                    </x-table.th>

                    <x-table.th>
                        Último acceso
                    </x-table.th>

                    <x-table.th class="text-center">
                        Estado
                    </x-table.th>

                    <x-table.th class="text-center w-52">
                        Acciones
                    </x-table.th>

                </x-table.row>

            </x-table.head>

            <x-table.body>

                @forelse($users as $user)
                    <x-table.row>

                        <x-table.td>
                            {{ $user->username }}
                        </x-table.td>

                        <x-table.td>
                            {{ $user->full_name }}
                        </x-table.td>

                        <x-table.td>
                            {{ $user->getRoleNames()->first() ?? 'Sin rol' }}
                        </x-table.td>

                        <x-table.td>
                            {{ $user->last_login_at?->format('d/m/Y H:i') ?? 'Nunca' }}
                        </x-table.td>

                        <x-table.td class="text-center">

                            <x-table.badge :active="$user->is_active" />

                        </x-table.td>

                        <x-table.td>

                            <x-table.actions>

                                <x-button.secondary :href="route('users.show', $user)">

                                    Ver

                                </x-button.secondary>

                                <x-button.secondary :href="route('users.edit', $user)">

                                    Editar

                                </x-button.secondary>

                            </x-table.actions>

                        </x-table.td>

                    </x-table.row>

                @empty

                    <x-table.empty :colspan="6" message="No existen usuarios registrados." />
                @endforelse

            </x-table.body>

        </x-table.table>

        <div class="mt-6">

            {{ $users->links() }}

        </div>

    </x-layout.card>

</x-app-layout>
