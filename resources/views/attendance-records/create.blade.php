<x-app-layout>
    <x-layout.page-header title="Nueva Jornada" subtitle="Registrar una nueva jornada" />
    <x-layout.card>
        <form action="{{ route('attendance-records.store') }}" method="POST">
            @csrf
            @include('attendance-records._form')
        </form>
    </x-layout.card>
</x-app-layout>
