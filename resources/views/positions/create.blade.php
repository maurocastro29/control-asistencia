<x-app-layout>

    <x-layout.page-header title="Nueva Posición" subtitle="Registrar una nueva posición" />

    <x-layout.card>

        <form action="{{ route('positions.store') }}" method="POST">

            @csrf

            @include('positions._form')

        </form>

    </x-layout.card>

</x-app-layout>
