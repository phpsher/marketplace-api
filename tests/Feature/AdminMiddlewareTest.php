<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private Role $adminRole;

    private User $user;
    private Role $userRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRole  = Role::factory()->create(['role' => 'user']);
        $this->adminRole = Role::factory()->create(['role' => 'admin']);

        $this->adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id,
        ]);

        $this->user = User::factory()->create([
            'role_id' => $this->userRole->id,
        ]);

        $this->adminUser = $this->adminUser->fresh('role');
        $this->user      = $this->user->fresh('role');
    }

    public function test_admin_can_access_protected_route(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('api/v1/protected-route-admin');

        $response->assertStatus(200);
    }

    public function test_user_cannot_access_protected_route(): void
    {
        $this->actingAs($this->user);

        $response = $this->get('api/v1/protected-route-admin');

        $response->assertStatus(401);
    }
}
