<x-app-layout>
    <x-layout.page-header title="Editar Tipo de Documento" subtitle="Actualizar la información" />
    <x-layout.card>
        <form action="{{ route('document-types.update', $documentType) }}" method="POST">
            @csrf
            @method('PUT')
            @include('document-types._form')
        </form>
    </x-layout.card>
</x-app-layout>
