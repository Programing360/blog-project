<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_admin_area(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_authenticated_non_admin_receives_access_denied(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_author_role_cannot_reach_admin_area(): void
    {
        $author = User::factory()->create(['role' => 'author']);

        $this->actingAs($author)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_can_reach_the_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('totalPosts')
            ->assertViewHas('totalUsers')
            ->assertViewHas('totalComments')
            ->assertViewHas('totalCategories');
    }

    public function test_forbidden_response_carries_the_expected_message(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        try {
            $this->withoutExceptionHandling()
                ->actingAs($user)
                ->get(route('admin.dashboard'));

            $this->fail('Expected a 403 response for a non-admin user.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
            $this->assertSame('Access Denied: Admins Only!', $exception->getMessage());
        }
    }

    public function test_every_admin_route_uses_the_admin_middleware(): void
    {
        $routes = collect(app('router')->getRoutes())
            ->filter(fn ($route) => str_starts_with((string) $route->getName(), 'admin.'));

        $this->assertGreaterThan(0, $routes->count());

        foreach ($routes as $route) {
            $this->assertContains('admin', $route->gatherMiddleware(), $route->getName());
        }
    }

    public function test_admin_navigation_link_is_only_rendered_for_admins(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.dashboard'), false);

        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get(route('posts.index'))
            ->assertOk()
            ->assertDontSee(route('admin.dashboard'), false);
    }

    public function test_category_count_and_posts_are_visible_on_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        Post::factory()->create(['category_id' => $category->id]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('1')
            ->assertViewHas('recentPosts', function ($posts) {
                return $posts->count() === 1;
            });
    }
}
