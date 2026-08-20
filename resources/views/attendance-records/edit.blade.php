<x-app-layout>
    <x-layout.page-header title="Editar Jornada" subtitle="Actualizar la información de la jornada del empleado">
    </x-layout.page-header>
    <x-layout.card>
        <form action="{{ route('attendance-records.update', $attendanceRecord) }}" method="POST">
            @csrf
            @method('PUT')
            @include('attendance-records._form')
        </form>
    </x-layout.card>
</x-app-layout>
