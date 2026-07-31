<x-app-layout>

    <x-layout.page-header title="Editar Usuario" subtitle="Actualizar la información del usuario" />

    <x-layout.card>

        <form method="POST" action="{{ route('users.update', $user) }}">

            @csrf
            @method('PUT')

            @include('users._form')

        </form>

    </x-layout.card>

</x-app-layout>
