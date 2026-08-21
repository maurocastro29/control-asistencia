<x-app-layout>
    <x-layout.page-header class="m-6" title="Tipos de Documento" subtitle="Administración de los tipos de documento">
        <x-slot:actions>
            @can('document-types.create')
                <x-button.primary :href="route('document-types.create')">
                    Nuevo Tipo de Documento
                </x-button.primary>
            @endcan
        </x-slot:actions>
    </x-layout.page-header>
    <x-layout.card>
        <x-table.table>
            <x-table.head>
                <x-table.row>
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
                </x-table.row>
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
                            @can('document-types.edit')
                                <x-table.actions>
                                    <x-button.secondary :href="route('document-types.edit', $documentType)">
                                        Editar
                                    </x-button.secondary>
                                </x-table.actions>
                            @endcan
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
