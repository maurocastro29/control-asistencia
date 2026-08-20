<x-app-layout>
    <x-layout.page-header title="Editar Departamento" subtitle="Actualizar la información" />
    <x-layout.card>
        <form action="{{ route('departments.update', $department) }}" method="POST">
            @csrf
            @method('PUT')
            @include('departments._form')
        </form>
    </x-layout.card>
</x-app-layout>
