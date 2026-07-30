<x-app-layout>

    <x-slot name="header">
        Registrar Marcación
    </x-slot>

    <x-card>

        <form action="{{ route('attendance-records.store') }}" method="POST">

            @include('attendance-records._form')

        </form>

    </x-card>

</x-app-layout>
