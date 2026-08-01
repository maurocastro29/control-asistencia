<x-app-layout>

    <x-layout.page-header title="Nuevo Rol" subtitle="Registrar un nuevo rol">

    </x-layout.page-header>

    <x-layout.card>

        <form method="POST" action="{{ route('roles.store') }}">

            @include('roles._form')

        </form>

    </x-layout.card>

</x-app-layout>
