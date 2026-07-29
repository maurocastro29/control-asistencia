<x-app-layout>

    <x-layout.page-header title="Nuevo Tipo de Documento" subtitle="Registrar un nuevo tipo de documento" />

    <x-layout.card>

        <form action="{{ route('document-types.store') }}" method="POST">

            @csrf

            @include('document-types._form')

        </form>

    </x-layout.card>

</x-app-layout>
