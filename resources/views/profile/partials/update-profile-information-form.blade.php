<section>
    <header>
        <h2 class="text-lg font-semibold text-slate-800">
            Información personal
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Actualiza los datos con los que te identifican dentro del sistema.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-form.input name="username" label="Usuario" :value="old('username', $user->username)" required autocomplete="username" />
            <x-form.input name="first_name" label="Primer nombre" :value="old('first_name', $user->first_name)" required autocomplete="given-name" />
            <x-form.input name="middle_name" label="Segundo nombre" :value="old('middle_name', $user->middle_name)" autocomplete="additional-name" />
            <x-form.input name="first_last_name" label="Primer apellido" :value="old('first_last_name', $user->first_last_name)" required autocomplete="family-name" />
            <x-form.input name="second_last_name" label="Segundo apellido" :value="old('second_last_name', $user->second_last_name)" />
        </div>

        <div class="mt-6 flex justify-end">
            <x-button.primary type="submit">Guardar información</x-button.primary>
        </div>
    </form>
</section>
