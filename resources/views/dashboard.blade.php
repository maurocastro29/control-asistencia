<x-app-layout>

    <div class="flex min-h-screen bg-gray-100">

        <!-- Sidebar -->
        <aside class="w-64 bg-slate-800 text-white">

            <div class="p-5 border-b border-slate-700">
                <h2 class="text-xl font-bold">
                    Control Asistencia
                </h2>
            </div>

            <nav class="mt-4">

                <a href="{{ route('dashboard') }}" class="block px-5 py-3 hover:bg-slate-700">
                    Dashboard
                </a>

                <div class="mt-4 px-5 text-xs uppercase text-slate-400">
                    Administración
                </div>

                <a href="{{ route('users.index') }}" class="block px-5 py-2 hover:bg-slate-700">
                    Usuarios
                </a>

                <a href="{{ route('roles.index') }}" class="block px-5 py-2 hover:bg-slate-700">
                    Roles
                </a>

                <div class="mt-4 px-5 text-xs uppercase text-slate-400">
                    Organización
                </div>

                <a href="{{ route('departments.index') }}" class="block px-5 py-2 hover:bg-slate-700">
                    Departamentos
                </a>

                <a href="#" class="block px-5 py-2 hover:bg-slate-700">
                    Cargos
                </a>

                <a href="#" class="block px-5 py-2 hover:bg-slate-700">
                    Empleados
                </a>

                <div class="mt-4 px-5 text-xs uppercase text-slate-400">
                    Asistencia
                </div>

                <a href="#" class="block px-5 py-2 hover:bg-slate-700">
                    Registrar asistencia
                </a>

                <a href="#" class="block px-5 py-2 hover:bg-slate-700">
                    Historial
                </a>

                <div class="mt-4 px-5 text-xs uppercase text-slate-400">
                    Reportes
                </div>

                <a href="#" class="block px-5 py-2 hover:bg-slate-700">
                    Reportes
                </a>

                <div class="mt-4 px-5 text-xs uppercase text-slate-400">
                    Configuración
                </div>

                <a href="#" class="block px-5 py-2 hover:bg-slate-700">
                    Configuración
                </a>

            </nav>

        </aside>

        <!-- Contenido -->
        <main class="flex-1 p-8">

            <h1 class="text-3xl font-bold mb-6">
                Dashboard
            </h1>

            <div class="grid grid-cols-4 gap-6">

                <div class="bg-white rounded-lg shadow p-6">
                    Empleados
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    Asistencias Hoy
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    Horas Extras
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    Reportes
                </div>

            </div>

        </main>

    </div>

</x-app-layout>
