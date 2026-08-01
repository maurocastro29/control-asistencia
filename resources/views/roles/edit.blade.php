<x-app-layout>

    <x-layout.page-header title="Editar Rol" subtitle="Actualizar información del rol">

    </x-layout.page-header>

    <x-layout.card>

        <form method="POST" action="{{ route('roles.update', $role) }}">

            @csrf
            @method('PUT')

            @include('roles._form')

        </form>

    </x-layout.card>

</x-app-layout>
