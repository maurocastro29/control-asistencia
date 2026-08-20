<x-app-layout>
    <x-layout.page-header title="Editar horario" subtitle="Editar detalle del Horario laboral">
        <x-slot:actions>
            <x-button.primary :href="route('work-schedules.index')">
                Volver
            </x-button.primary>
        </x-slot:actions>
    </x-layout.page-header>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('work-schedules.update', $workSchedule) }}">
                @csrf
                @method('PUT')
                @include('work-schedules.partials.form')
            </form>
        </div>
    </div>
</x-app-layout>
