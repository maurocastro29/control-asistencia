<x-app-layout>

    <div class="m-6 p-6 bg-white rounded-lg shadow">
        <x-layout.page-header class="m-6" title="Tipos de Documento"
            subtitle="Administración de los tipos de documento">
            <x-slot:actions>
                <x-button.primary :href="route('document-types.create')">
                    Nuevo Tipo de Documento
                </x-button.primary>
            </x-slot:actions>
        </x-layout.page-header>
    </div>

    <x-layout.card class="m-6">

        <x-table.table>

            <x-table.head>

                <tr>

                    <x-table.th>
                        Nombre
                    </x-table.th>

                    <x-table.th>
                        Abreviatura
                    </x-table.th>

                    <x-table.th class="text-center">
                        Estado
                    </x-table.th>

                    <x-table.th class="text-center w-40">
                        Acciones
                    </x-table.th>

                </tr>

            </x-table.head>

            <x-table.body>

                @forelse($documentTypes as $documentType)
                    <x-table.row>

                        <x-table.td>
                            {{ $documentType->name }}
                        </x-table.td>

                        <x-table.td>
                            {{ $documentType->abbreviation }}
                        </x-table.td>

                        <x-table.td class="text-center">

                            <x-table.badge :active="$documentType->is_active" />

                        </x-table.td>

                        <x-table.td>

                            <x-table.actions>

                                <x-button.secondary :href="route('document-types.edit', $documentType)">

                                    Editar

                                </x-button.secondary>

                            </x-table.actions>

                        </x-table.td>

                    </x-table.row>

                @empty

                    <x-table.empty :colspan="4" message="No existen tipos de documento registrados." />
                @endforelse

            </x-table.body>

        </x-table.table>

        <div class="mt-6">

            {{ $documentTypes->links() }}

        </div>

    </x-layout.card>

</x-app-layout>
