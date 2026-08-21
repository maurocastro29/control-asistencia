<x-app-layout>
    <x-layout.page-header title="Editar Empleado" subtitle="Actualizar la información del empleado">
    </x-layout.page-header>
    <x-layout.card>
        <form action="{{ route('employees.update', $employee) }}" method="POST">
            @csrf
            @method('PUT')
            @include('employees.partials.form')
        </form>
    </x-layout.card>
</x-app-layout>
