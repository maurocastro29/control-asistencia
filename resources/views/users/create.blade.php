<x-app-layout>

    <x-layout.page-header title="Nuevo Usuario" subtitle="Registrar un nuevo usuario del sistema" />

    <x-layout.card>

        <form method="POST" action="{{ route('users.store') }}">

            @include('users._form')

        </form>

    </x-layout.card>

</x-app-layout>
