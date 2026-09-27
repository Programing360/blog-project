<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Policies\PostPolicy;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostPolicyTest extends TestCase
{
    use RefreshDatabase;

    private function gate(): Gate
    {
        return app(Gate::class);
    }

    private function makePostFor(User $owner): Post
    {
        $category = Category::first() ?? Category::create(['name' => 'Laravel', 'slug' => 'laravel']);

        return Post::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'title' => 'Owned',
            'slug' => 'owned-'.uniqid(),
            'content' => 'Body',
            'status' => 'published',
        ]);
    }

    private function userWithRole(?string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_policy_is_auto_discovered_for_the_post_model(): void
    {
        $this->assertInstanceOf(PostPolicy::class, $this->gate()->getPolicyFor(Post::class));
    }

    public function test_owner_may_update_and_delete(): void
    {
        $owner = User::factory()->create();
        $post = $this->makePostFor($owner);

        $this->assertTrue($this->gate()->forUser($owner)->allows('update', $post));
        $this->assertTrue($this->gate()->forUser($owner)->allows('delete', $post));
    }

    public function test_non_owner_may_not_update_or_delete(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $post = $this->makePostFor($owner);

        $this->assertFalse($this->gate()->forUser($stranger)->allows('update', $post));
        $this->assertFalse($this->gate()->forUser($stranger)->allows('delete', $post));
    }

    public function test_admin_bypasses_ownership_via_before(): void
    {
        $owner = User::factory()->create();
        $admin = $this->userWithRole('admin');
        $post = $this->makePostFor($owner);

        $this->assertTrue($this->gate()->forUser($admin)->allows('update', $post));
        $this->assertTrue($this->gate()->forUser($admin)->allows('delete', $post));
    }

    /**
     * A `before()` that returned false for non-admins would short-circuit the
     * policy and deny the owner too. It must return null instead.
     */
    public function test_non_admins_still_reach_the_ability_methods(): void
    {
        $owner = User::factory()->create();
        $policy = new PostPolicy;

        $this->assertNull($policy->before($owner, 'update'));
        $this->assertNull($policy->before($owner, 'delete'));
        $this->assertTrue($policy->before($this->userWithRole('admin'), 'update'));
    }

    public function test_abilities_not_declared_are_denied(): void
    {
        $admin = $this->userWithRole('admin');
        $post = $this->makePostFor(User::factory()->create());

        $this->assertFalse($this->gate()->forUser($admin)->allows('restore', $post));
        $this->assertFalse($this->gate()->forUser($admin)->allows('forceDelete', $post));
    }
}
