<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RolePermissionGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_update_rejects_permission_ids_from_another_guard(): void
    {
        $webPermission = Permission::create([
            'name' => 'inactivity-days.view',
            'display_name' => 'Inactividad: consultar',
            'guard_name' => 'web',
            'is_active' => true,
        ]);
        $apiPermission = Permission::create([
            'name' => 'inactivity-days.view',
            'display_name' => 'API: consultar',
            'guard_name' => 'api',
            'is_active' => true,
        ]);
        $settingsView = Permission::create([
            'name' => 'settings.view',
            'display_name' => 'Configuración: consultar',
            'guard_name' => 'web',
            'is_active' => true,
        ]);
        $settingsEdit = Permission::create([
            'name' => 'settings.edit',
            'display_name' => 'Configuración: administrar',
            'guard_name' => 'web',
            'is_active' => true,
        ]);

        $role = Role::create([
            'name' => 'Supervisor',
            'guard_name' => 'web',
            'is_active' => true,
        ]);
        $role->givePermissionTo($webPermission);

        $user = User::factory()->create();
        $user->givePermissionTo([$settingsView, $settingsEdit]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs($user)
            ->get(route('settings.roles.edit', $role))
            ->assertOk()
            ->assertSee('Inactividad: consultar')
            ->assertDontSee('API: consultar');

        $this->from(route('settings.roles.edit', $role))
            ->put(route('settings.roles.update', $role), [
                'permission_ids' => [$webPermission->id, $apiPermission->id],
            ])
            ->assertRedirect(route('settings.roles.edit', $role))
            ->assertSessionHasErrors('permission_ids.1');

        $this->assertDatabaseHas('role_has_permissions', [
            'role_id' => $role->id,
            'permission_id' => $webPermission->id,
        ]);
        $this->assertDatabaseMissing('role_has_permissions', [
            'role_id' => $role->id,
            'permission_id' => $apiPermission->id,
        ]);
    }
}