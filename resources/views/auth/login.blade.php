<x-guest-layout :wide="true">
    <div
        class="grid w-full overflow-hidden rounded-3xl bg-white shadow-2xl shadow-slate-300/60 lg:grid-cols-[1.05fr_0.95fr]">
        <section
            class="relative hidden overflow-hidden bg-slate-950 p-8 text-white lg:flex lg:h-[calc(100dvh-2rem)] lg:max-h-[620px] lg:flex-col lg:justify-between">
            <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full border-[28px] border-cyan-400/20"></div>
            <div class="absolute -bottom-36 -left-24 h-80 w-80 rounded-full border-[36px] border-blue-500/20"></div>

            <div class="relative">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-cyan-400 font-black text-slate-950">
                        CA</div>
                    <span class="text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">Control de
                        asistencia</span>
                </div>
                <h1 class="mt-12 max-w-md text-4xl font-bold leading-tight tracking-tight">Jornadas claras. Equipos
                    coordinados.</h1>
                <p class="mt-4 max-w-md text-sm leading-6 text-slate-300">Gestiona la asistencia, los horarios y los
                    reportes de tu equipo desde un solo lugar.</p>
            </div>

            <div class="relative grid grid-cols-2 gap-3">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm">
                    <p class="text-xl font-bold text-cyan-300">24/7</p>
                    <p class="mt-1 text-xs text-slate-400">Información disponible</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm">
                    <p class="text-xl font-bold text-cyan-300">100%</p>
                    <p class="mt-1 text-xs text-slate-400">Trazabilidad de jornadas</p>
                </div>
            </div>
        </section>

        <section class="flex flex-col justify-center px-6 py-8 sm:px-12 lg:h-[calc(100dvh-2rem)] lg:max-h-[620px] lg:py-6">
            <div class="mx-auto w-full max-w-md">
                <div class="mb-6 lg:hidden">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-950 font-black text-cyan-300">
                            CA</div>
                        <span class="text-sm font-bold uppercase tracking-[0.15em] text-slate-700">Control de
                            asistencia</span>
                    </div>
                </div>
                <div class="mb-6">
                    <p class="text-sm font-semibold uppercase tracking-widest text-cyan-600">Acceso seguro</p>
                    <h2 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Bienvenido de nuevo</h2>
                    <p class="mt-1 text-sm leading-6 text-slate-500">Ingresa tus credenciales para continuar con la
                        gestión de tu equipo.</p>
                </div>
                <x-auth-session-status class="mb-5" :status="session('status')" />
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="username" value="Usuario"
                            class="mb-2 text-sm font-semibold text-slate-700" />
                        <x-text-input id="username"
                            class="block w-full rounded-xl border-slate-300 px-4 py-2.5 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                            type="text" name="username" :value="old('username')" required autofocus autocomplete="username"
                            placeholder="Ingresa tu usuario" />
                        <x-input-error :messages="$errors->get('username')" class="mt-2" />
                    </div>
                    <div>
                        <div class="flex items-center justify-between">
                            <x-input-label for="password" value="Contraseña"
                                class="mb-2 text-sm font-semibold text-slate-700" />
                        </div>
                        <x-text-input id="password"
                            class="block w-full rounded-xl border-slate-300 px-4 py-2.5 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                            type="password" name="password" required autocomplete="current-password"
                            placeholder="Ingresa tu contraseña" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <label for="remember_me" class="flex cursor-pointer items-center gap-3 text-sm text-slate-500">
                        <input id="remember_me" type="checkbox"
                            class="rounded border-slate-300 text-cyan-600 shadow-sm focus:ring-cyan-500"
                            name="remember">
                        <span>Recordar mi sesión</span>
                    </label>
                    <button type="submit"
                        class="flex w-full items-center justify-center rounded-xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-900/20 transition hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">
                        Ingresar al sistema
                        <span class="ml-3 text-lg text-cyan-300" aria-hidden="true">&rarr;</span>
                    </button>
                </form>
                <p class="mt-5 text-center text-xs leading-5 text-slate-400">Acceso exclusivo para usuarios autorizados
                    por la organización.
                </p>
            </div>
        </section>
    </div>
</x-guest-layout>
