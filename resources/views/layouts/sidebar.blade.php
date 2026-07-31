<aside class="w-64 bg-slate-800 text-white min-h-screen">

    <div class="p-6 border-b border-slate-700">

        <h2 class="text-xl font-bold">
            Menú
        </h2>

    </div>

    <nav class="mt-4">

        <a href="{{ route('dashboard') }}" class="block px-6 py-3 hover:bg-slate-700">
            Dashboard
        </a>

        <div class="mt-6 px-6 text-xs uppercase text-slate-400">
            Administración
        </div>

        <a href="{{ route('users.index') }}" class="block px-6 py-2 hover:bg-slate-700">
            Usuarios
        </a>

        <a href="{{ route('roles.index') }}" class="block px-6 py-2 hover:bg-slate-700">
            Roles
        </a>

        <div class="mt-6 px-6 text-xs uppercase text-slate-400">
            Catálogos
        </div>

        <a href="{{ route('document-types.index') }}" class="block px-6 py-2 hover:bg-slate-700">
            Tipos de documento
        </a>

        <a href="{{ route('departments.index') }}" class="block px-6 py-2 hover:bg-slate-700">
            Departamentos
        </a>

        <a href="{{ route('positions.index') }}" class="block px-6 py-2 hover:bg-slate-700">
            Cargos
        </a>

        <div class="mt-6 px-6 text-xs uppercase text-slate-400">
            Personal
        </div>

        <a href="{{ route('employees.index') }}" class="block px-6 py-2 hover:bg-slate-700">
            Empleados
        </a>

        <div class="mt-6 px-6 text-xs uppercase text-slate-400">
            Asistencia
        </div>

        <a href="{{ route('attendance.register') }}" class="block px-6 py-2 hover:bg-slate-700">
            Registrar asistencia
        </a>

        <a href="{{ route('attendance-records.index') }}" class="block px-6 py-2 hover:bg-slate-700">
            Historial
        </a>

        <a href="{{ route('work-schedules.index') }}" class="block px-6 py-2 hover:bg-slate-700">
            Horarios
        </a>

        <div class="mt-6 px-6 text-xs uppercase text-slate-400">
            Reportes
        </div>

        <a href="#" class="block px-6 py-2 hover:bg-slate-700">
            Reportes
        </a>

        <div class="mt-6 px-6 text-xs uppercase text-slate-400">
            Configuración
        </div>

        <a href="{{ route('settings.index') }}" class="block px-6 py-2 hover:bg-slate-700">
            Configuración
        </a>

    </nav>

</aside>
