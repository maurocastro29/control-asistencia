<x-app-layout>
    <x-layout.page-header title="Nuevo Departamento" subtitle="Registrar un nuevo departamento" />
    <x-layout.card>
        <form action="{{ route('departments.store') }}" method="POST">
            @csrf
            @include('departments._form')
        </form>
    </x-layout.card>
</x-app-layout>
