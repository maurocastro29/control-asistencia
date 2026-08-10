<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Horario Laboral
        </h2>
    </x-slot>

    <div class="py-6">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white shadow rounded-lg">

            <form method="POST" action="{{ route('work-schedules.store') }}">

                @csrf

                @include('work-schedules.partials.form')

            </form>

        </div>

    </div>

</x-app-layout>
