<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\Concerns\UsesIsolatedTestDatabase;
use Tests\TestCase;

class RoleDashboardPermissionTest extends TestCase
{
    use UsesIsolatedTestDatabase;

    public function test_dashboard_permission_is_visible_and_can_be_managed_per_role(): void
    {
        $dashboardPermission = Permission::create(['name' => 'dashboard.view']);
        $roleEditPermission = Permission::create(['name' => 'roles.edit']);

        $manager = User::factory()->create(['username' => 'role-manager']);
        $manager->givePermissionTo($roleEditPermission);

        $targetRole = Role::create(['name' => 'target-role']);
        $targetRole->givePermissionTo($dashboardPermission);

        $targetUser = User::factory()->create(['username' => 'target-user']);
        $targetUser->assignRole($targetRole);

        $this->actingAs($manager)
            ->get(route('roles.edit', $targetRole))
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Lihat')
            ->assertSee('dashboard.view');

        $this->actingAs($manager)
            ->put(route('roles.update', $targetRole), [
                'name' => $targetRole->name,
                'permissions' => [],
            ])
            ->assertRedirect(route('roles.index'));

        $this->actingAs($targetUser)
            ->get(route('dashboard'))
            ->assertForbidden();

        $this->actingAs($manager)
            ->put(route('roles.update', $targetRole), [
                'name' => $targetRole->name,
                'permissions' => ['dashboard.view'],
            ])
            ->assertRedirect(route('roles.index'));

        $this->assertTrue($targetUser->fresh()->can('dashboard.view'));
    }
}
