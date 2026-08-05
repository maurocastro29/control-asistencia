@csrf

<div class="grid grid-cols-2 gap-6">

    <x-form.input name="username" label="Usuario" :value="old('username', $user->username ?? '')" required />

    <x-form.input name="first_name" label="Primer nombre" :value="old('first_name', $user->first_name ?? '')" required />

    <x-form.input name="middle_name" label="Segundo nombre" :value="old('middle_name', $user->middle_name ?? '')" />

    <x-form.input name="first_last_name" label="Primer apellido" :value="old('first_last_name', $user->first_last_name ?? '')" required />

    <x-form.input name="second_last_name" label="Segundo apellido" :value="old('second_last_name', $user->second_last_name ?? '')" />

    <x-form.input type="password" name="password" label="Contraseña" />

    <x-form.input type="password" name="password_confirmation" label="Confirmar contraseña" />

</div>

<div class="mt-6">

    <x-form.switch name="is_active" label="Usuario activo" :checked="old('is_active', $user->is_active ?? true)" />

</div>

<div class="flex justify-end mt-8 gap-3">

    <x-button.secondary :href="route('users.index')">

        Cancelar

    </x-button.secondary>

    <x-button.primary>

        Guardar

    </x-button.primary>

</div>
