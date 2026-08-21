<aside
    class="sidebar-scroll sticky top-16 h-[calc(100vh-4rem)] w-64 flex-shrink-0 overflow-y-auto bg-blue-600 text-white">
    <nav class="space-y-2 px-3 py-4 pb-8">
        @can('dashboard.view')
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-white text-blue-700 shadow-sm' : 'text-blue-50 hover:bg-blue-700' }}">
                <span
                    class="flex h-6 w-6 items-center justify-center rounded-md {{ request()->routeIs('dashboard') ? 'bg-blue-100' : 'bg-blue-500' }}"
                    aria-hidden="true">⌂</span>
                Dashboard
            </a>
        @endcan
        @canany(['employees.view', 'users.view'])
            <div x-data="{ open: {{ request()->routeIs('employees.*', 'users.*') ? 'true' : 'false' }} }" class="pt-3">
                <button type="button" @click="open = !open" :aria-expanded="open.toString()"
                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-xs font-semibold uppercase tracking-wider text-blue-200 transition hover:bg-blue-700 hover:text-white">
                    <span>Personal</span>
                    <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" viewBox="0 0 20 20"
                        fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08.02l-4.25-4.5a.75.75 0 01.02-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-cloak x-show="open" x-transition class="mt-1 space-y-1 border-l border-blue-400/60 pl-3">
                    @can('employees.view')
                        <a href="{{ route('employees.index') }}"
                            class="block rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('employees.*') ? 'bg-blue-800 font-semibold text-white' : 'text-blue-100 hover:bg-blue-700' }}">Empleados</a>
                    @endcan
                    @can('users.view')
                        <a href="{{ route('users.index') }}"
                            class="block rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('users.*') ? 'bg-blue-800 font-semibold text-white' : 'text-blue-100 hover:bg-blue-700' }}">Usuarios</a>
                    @endcan
                </div>
            </div>
        @endcanany

        @canany(['attendance.view', 'attendance.create', 'attendance-records.view', 'work-schedules-adjustments.view',
            'work-schedules.view'])
            <div x-data="{ open: {{ request()->routeIs('attendance.*', 'attendance-records.*', 'work-schedule-adjustments.*', 'work-schedules.*') ? 'true' : 'false' }} }" class="pt-3">
                <button type="button" @click="open = !open" :aria-expanded="open.toString()"
                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-xs font-semibold uppercase tracking-wider text-blue-200 transition hover:bg-blue-700 hover:text-white">
                    <span>Asistencia y jornadas</span>
                    <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" viewBox="0 0 20 20"
                        fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08.02l-4.25-4.5a.75.75 0 01.02-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-cloak x-show="open" x-transition class="mt-1 space-y-1 border-l border-blue-400/60 pl-3">
                    @canany(['attendance.view', 'attendance.create'])
                        <a href="{{ route('attendance.register') }}"
                            class="block rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('attendance.*') ? 'bg-blue-800 font-semibold text-white' : 'text-blue-100 hover:bg-blue-700' }}">Registrar
                            asistencia</a>
                    @endcanany
                    @can('attendance-records.view')
                        <a href="{{ route('attendance-records.index') }}"
                            class="block rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('attendance-records.*') ? 'bg-blue-800 font-semibold text-white' : 'text-blue-100 hover:bg-blue-700' }}">Historial
                            de asistencia</a>
                    @endcan
                    @can('work-schedules-adjustments.view')
                        <a href="{{ route('work-schedule-adjustments.index') }}"
                            class="block rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('work-schedule-adjustments.*') ? 'bg-blue-800 font-semibold text-white' : 'text-blue-100 hover:bg-blue-700' }}">Ajustes
                            de jornada</a>
                    @endcan
                    @can('work-schedules.view')
                        <a href="{{ route('work-schedules.index') }}"
                            class="block rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('work-schedules.*') ? 'bg-blue-800 font-semibold text-white' : 'text-blue-100 hover:bg-blue-700' }}">Horarios</a>
                    @endcan
                </div>
            </div>
        @endcanany

        @can('reports.view')
            <div x-data="{ open: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }" class="pt-3">
                <button type="button" @click="open = !open" :aria-expanded="open.toString()"
                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-xs font-semibold uppercase tracking-wider text-blue-200 transition hover:bg-blue-700 hover:text-white">
                    <span>Reportes</span>
                    <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" viewBox="0 0 20 20"
                        fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08.02l-4.25-4.5a.75.75 0 01.02-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-cloak x-show="open" x-transition class="mt-1 space-y-1 border-l border-blue-400/60 pl-3">
                    <a href="{{ route('reports.attendance') }}"
                        class="block rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('reports.*') ? 'bg-blue-800 font-semibold text-white' : 'text-blue-100 hover:bg-blue-700' }}">Reporte
                        de asistencia</a>
                </div>
            </div>
        @endcan

        @canany(['departments.view', 'positions.view'])
            <div x-data="{ open: {{ request()->routeIs('departments.*', 'positions.*') ? 'true' : 'false' }} }" class="pt-3">
                <button type="button" @click="open = !open" :aria-expanded="open.toString()"
                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-xs font-semibold uppercase tracking-wider text-blue-200 transition hover:bg-blue-700 hover:text-white">
                    <span>Catálogos</span>
                    <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" viewBox="0 0 20 20"
                        fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08.02l-4.25-4.5a.75.75 0 01.02-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-cloak x-show="open" x-transition class="mt-1 space-y-1 border-l border-blue-400/60 pl-3">
                    @can('departments.view')
                        <a href="{{ route('departments.index') }}"
                            class="block rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('departments.*') ? 'bg-blue-800 font-semibold text-white' : 'text-blue-100 hover:bg-blue-700' }}">Departamentos</a>
                    @endcan
                    @can('positions.view')
                        <a href="{{ route('positions.index') }}"
                            class="block rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('positions.*') ? 'bg-blue-800 font-semibold text-white' : 'text-blue-100 hover:bg-blue-700' }}">Cargos</a>
                    @endcan
                </div>
            </div>
        @endcanany

        @canany(['holidays.view', 'document-types.view', 'settings.view'])
            <div x-data="{ open: {{ request()->routeIs('holidays.*', 'document-types.*', 'settings.*') ? 'true' : 'false' }} }" class="pt-3">
                <button type="button" @click="open = !open" :aria-expanded="open.toString()"
                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-xs font-semibold uppercase tracking-wider text-blue-200 transition hover:bg-blue-700 hover:text-white">
                    <span>Administración</span>
                    <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" viewBox="0 0 20 20"
                        fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08.02l-4.25-4.5a.75.75 0 01.02-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-cloak x-show="open" x-transition class="mt-1 space-y-1 border-l border-blue-400/60 pl-3">
                    @can('holidays.view')
                        <a href="{{ route('holidays.index') }}"
                            class="block rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('holidays.*') ? 'bg-blue-800 font-semibold text-white' : 'text-blue-100 hover:bg-blue-700' }}">Festivos</a>
                    @endcan
                    @can('document-types.view')
                        <a href="{{ route('document-types.index') }}"
                            class="block rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('document-types.*') ? 'bg-blue-800 font-semibold text-white' : 'text-blue-100 hover:bg-blue-700' }}">Tipos
                            de documento</a>
                    @endcan
                    @can('settings.view')
                        <a href="{{ route('settings.index') }}"
                            class="block rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('settings.*') ? 'bg-blue-800 font-semibold text-white' : 'text-blue-100 hover:bg-blue-700' }}">Configuración</a>
                    @endcan
                </div>
            </div>
        @endcanany
    </nav>
</aside>
