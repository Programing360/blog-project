<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_users_with_activity_counts(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        User::factory()->create(['name' => 'Regular Person']);

        $this->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Regular Person')
            ->assertViewHas('users', function ($users) {
                return $users->count() === 2;
            });
    }

    public function test_create_and_store_routes_do_not_exist(): void
    {
        $this->assertFalse(Route::has('admin.users.create'));
        $this->assertFalse(Route::has('admin.users.store'));
    }

    public function test_edit_form_renders_available_roles(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $user = User::factory()->create(['role' => 'user']);

        $this->get(route('admin.users.edit', $user))
            ->assertOk()
            ->assertSee('Admin')
            ->assertSee('Author');
    }

    public function test_update_changes_a_users_role(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $user = User::factory()->create(['role' => 'user']);

        $this->put(route('admin.users.update', $user), ['role' => 'author'])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('status');

        $this->assertSame('author', $user->fresh()->role);
    }

    public function test_update_can_promote_a_user_to_admin(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $user = User::factory()->create(['role' => 'user']);

        $this->put(route('admin.users.update', $user), ['role' => 'admin'])
            ->assertRedirect(route('admin.users.index'));

        $this->assertSame('admin', $user->fresh()->role);
    }

    public function test_update_rejects_an_invalid_role(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $user = User::factory()->create(['role' => 'user']);

        $this->put(route('admin.users.update', $user), ['role' => 'superuser'])
            ->assertSessionHasErrors('role');

        $this->assertSame('user', $user->fresh()->role);
    }

    public function test_update_requires_a_role(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $user = User::factory()->create(['role' => 'user']);

        $this->put(route('admin.users.update', $user), ['role' => ''])
            ->assertSessionHasErrors('role');
    }

    public function test_admin_cannot_demote_themselves(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->from(route('admin.users.index'))
            ->put(route('admin.users.update', $admin), ['role' => 'user'])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('error');

        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_admin_can_update_another_admin_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $other = User::factory()->create(['role' => 'admin']);

        $this->put(route('admin.users.update', $other), ['role' => 'user'])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame('user', $other->fresh()->role);
    }

    public function test_destroy_deletes_a_user(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $user = User::factory()->create(['role' => 'user']);

        $this->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->from(route('admin.users.index'))
            ->delete(route('admin.users.destroy', $admin))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_cannot_delete_the_last_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->from(route('admin.users.index'))
            ->delete(route('admin.users.destroy', $admin))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_non_admin_cannot_manage_users(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'user']));
        $user = User::factory()->create();

        $this->get(route('admin.users.index'))->assertForbidden();
        $this->get(route('admin.users.edit', $user))->assertForbidden();
        $this->put(route('admin.users.update', $user), ['role' => 'admin'])->assertForbidden();
        $this->delete(route('admin.users.destroy', $user))->assertForbidden();
    }
}
