<x-app-layout>
    <x-layout.page-header title="Editar Posición" subtitle="Actualizar la información" />
    <x-layout.card>
        <form action="{{ route('positions.update', $position) }}" method="POST">
            @csrf
            @method('PUT')
            @include('positions._form')
        </form>
    </x-layout.card>
</x-app-layout>
