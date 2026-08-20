<x-app-layout>
    <x-layout.page-header title="Crear horario" subtitle="Crear nuevo Horario laboral">
        <x-slot:actions>
            <x-button.primary :href="route('work-schedules.index')">
                Volver
            </x-button.primary>
        </x-slot:actions>
    </x-layout.page-header>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white shadow rounded-lg">
            <form method="POST" action="{{ route('work-schedules.store') }}">
                @csrf
                @include('work-schedules.partials.form')
            </form>
        </div>
    </div>
</x-app-layout>
