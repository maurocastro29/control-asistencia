<x-layout.page-header title="Nuevo Empleado" subtitle="Registrar un nuevo empleado">

</x-layout.page-header>

<x-layout.card>

    <form action="{{ route('employees.store') }}" method="POST">

        @csrf

        @include('employees.partials.form')

    </form>

</x-layout.card>
