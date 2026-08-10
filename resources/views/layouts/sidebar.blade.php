<aside class="w-64 bg-slate-800 text-white min-h-screen">

    <div class="p-6 border-b border-slate-700">

        <h2 class="text-xl font-bold">
            Menú
        </h2>

    </div>

    <nav class="mt-4">
        @can('dashboard.view')
            <a href="{{ route('dashboard') }}" class="block px-6 py-3 hover:bg-slate-700">
                Dashboard
            </a>
        @endcan

        <div class="mt-6 px-6 text-xs uppercase text-slate-400">
            Administración
        </div>

        @can('users.view')
            <a href="{{ route('users.index') }}" class="block px-6 py-2 hover:bg-slate-700">
                Usuarios
            </a>
        @endcan


        <div class="mt-6 px-6 text-xs uppercase text-slate-400">
            Catálogos
        </div>

        @can('document-types.view')
            <a href="{{ route('document-types.index') }}" class="block px-6 py-2 hover:bg-slate-700">
                Tipos de documento
            </a>
        @endcan

        @can('departments.view')
            <a href="{{ route('departments.index') }}" class="block px-6 py-2 hover:bg-slate-700">
                Departamentos
            </a>
        @endcan

        @can('positions.view')
            <a href="{{ route('positions.index') }}" class="block px-6 py-2 hover:bg-slate-700">
                Cargos
            </a>
        @endcan

        <div class="mt-6 px-6 text-xs uppercase text-slate-400">
            Personal
        </div>

        @can('employees.view')
            <a href="{{ route('employees.index') }}" class="block px-6 py-2 hover:bg-slate-700">
                Empleados
            </a>
        @endcan

        <div class="mt-6 px-6 text-xs uppercase text-slate-400">
            Asistencia
        </div>

        @can('attendance.view')
            <a href="{{ route('attendance.register') }}" class="block px-6 py-2 hover:bg-slate-700">
                Registrar asistencia
            </a>
        @endcan

        @can('attendance-records.view')
            <a href="{{ route('attendance-records.index') }}" class="block px-6 py-2 hover:bg-slate-700">
                Historial
            </a>
        @endcan

        @can('work-schedules.view')
            <a href="{{ route('work-schedules.index') }}" class="block px-6 py-2 hover:bg-slate-700">
                Horarios
            </a>
        @endcan

        <div class="mt-6 px-6 text-xs uppercase text-slate-400">
            Reportes
        </div>

        @can('reports.view')
            <a href="{{ route('reports') }}" class="block px-6 py-2 hover:bg-slate-700">
                Reportes
            </a>
        @endcan

        <div class="mt-6 px-6 text-xs uppercase text-slate-400">
            Configuración
        </div>

        @can('settings.view')
            <a href="{{ route('settings.index') }}" class="block px-6 py-2 hover:bg-slate-700">
                Configuración
            </a>
        @endcan

    </nav>

</aside>
