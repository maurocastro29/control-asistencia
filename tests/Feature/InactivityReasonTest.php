<?php

namespace Tests\Feature;

use App\Models\InactivityReason;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class InactivityReasonTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_view_permission_can_list_reasons(): void
    {
        $user = User::factory()->create();
        $this->grantPermission($user, 'inactivity-reasons.view');
        InactivityReason::create(['name' => 'Vacaciones', 'is_active' => true]);

        $this->actingAs($user)
            ->get(route('inactivity-reasons.index'))
            ->assertOk()
            ->assertSee('Vacaciones');
    }

    public function test_user_without_view_permission_cannot_open_reasons(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('inactivity-reasons.index'))
            ->assertForbidden();
    }

    public function test_user_with_management_permissions_can_create_update_and_deactivate_reason(): void
    {
        $user = User::factory()->create();
        foreach (['view', 'create', 'edit', 'delete'] as $action) {
            $this->grantPermission($user, "inactivity-reasons.{$action}");
        }

        $this->actingAs($user)
            ->get(route('inactivity-reasons.create'))
            ->assertOk()
            ->assertSee('Nombre del motivo');

        $this->actingAs($user)
            ->post(route('inactivity-reasons.store'), [
                'name' => 'Vacaciones',
                'description' => 'Descanso anual',
                'is_active' => '1',
            ])
            ->assertRedirect(route('inactivity-reasons.index'));

        $reason = InactivityReason::firstOrFail();
        $this->assertDatabaseHas('inactivity_reasons', [
            'id' => $reason->id,
            'name' => 'Vacaciones',
            'is_active' => true,
        ]);

        $this->get(route('inactivity-reasons.show', $reason))
            ->assertOk()
            ->assertSee('Vacaciones');

        $this->get(route('inactivity-reasons.edit', $reason))
            ->assertOk()
            ->assertSee('Nombre del motivo');

        $this->put(route('inactivity-reasons.update', $reason), [
            'name' => 'Vacaciones anuales',
            'description' => 'Descanso programado',
            'is_active' => '1',
        ])->assertRedirect(route('inactivity-reasons.index'));

        $this->delete(route('inactivity-reasons.destroy', $reason))
            ->assertRedirect(route('inactivity-reasons.index'));

        $this->assertDatabaseHas('inactivity_reasons', [
            'id' => $reason->id,
            'name' => 'Vacaciones anuales',
            'is_active' => false,
        ]);
    }

    private function grantPermission(User $user, string $name): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $permission = Permission::findOrCreate($name, 'web');
        $user->givePermissionTo($permission);
    }
}