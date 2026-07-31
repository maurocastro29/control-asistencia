<header class="bg-white shadow h-16 flex items-center justify-between px-8">

    <h2 class="text-xl font-semibold">
        Sistema de Control de Asistencia
    </h2>

    <div class="flex items-center gap-4">

        <span class="text-gray-600">
            {{ auth()->user()->first_name }}
        </span>

        <a href="{{ route('profile.edit') }}" class="text-blue-600 hover:underline">
            Perfil
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button class="text-red-600 hover:underline">
                Cerrar sesión
            </button>

        </form>

    </div>

</header>
