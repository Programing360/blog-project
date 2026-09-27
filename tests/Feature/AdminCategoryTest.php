<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        return $admin;
    }

    public function test_index_lists_categories_with_post_counts(): void
    {
        $this->admin();
        $category = Category::factory()->create(['name' => 'Laravel']);
        Post::factory()->count(2)->create(['category_id' => $category->id]);

        $this->get(route('admin.categories.index'))
            ->assertOk()
            ->assertSee('Laravel')
            ->assertViewHas('categories', function ($categories) {
                return $categories->first()->posts_count === 2;
            });
    }

    public function test_create_form_renders(): void
    {
        $this->admin();

        $this->get(route('admin.categories.create'))->assertOk();
    }

    public function test_store_persists_a_new_category_with_a_generated_slug(): void
    {
        $this->admin();

        $this->post(route('admin.categories.store'), ['name' => 'Laravel Tips'])
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('categories', [
            'name' => 'Laravel Tips',
            'slug' => 'laravel-tips',
        ]);
    }

    public function test_store_rejects_a_duplicate_name(): void
    {
        $this->admin();
        Category::factory()->create(['name' => 'Laravel']);

        $this->post(route('admin.categories.store'), ['name' => 'Laravel'])
            ->assertSessionHasErrors('name');

        $this->assertSame(1, Category::where('name', 'Laravel')->count());
    }

    public function test_store_requires_a_name(): void
    {
        $this->admin();

        $this->post(route('admin.categories.store'), ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    public function test_generated_slugs_stay_unique(): void
    {
        $this->admin();

        // An older record already owns the "laravel" slug even though its name differs.
        Category::factory()->create(['name' => 'Legacy Section', 'slug' => 'laravel']);

        $this->post(route('admin.categories.store'), ['name' => 'Laravel'])
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'name' => 'Laravel',
            'slug' => 'laravel-2',
        ]);
    }

    public function test_update_keeps_the_slug_unique_against_another_category(): void
    {
        $this->admin();
        Category::factory()->create(['name' => 'Legacy Section', 'slug' => 'laravel']);
        $category = Category::factory()->create(['name' => 'Old Name', 'slug' => 'old-name']);

        $this->put(route('admin.categories.update', $category), ['name' => 'Laravel'])
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame('laravel-2', $category->fresh()->slug);
    }

    public function test_show_lists_the_category_posts(): void
    {
        $this->admin();
        $category = Category::factory()->create(['name' => 'Testing']);
        Post::factory()->create(['category_id' => $category->id, 'title' => 'Deep Dive']);

        $this->get(route('admin.categories.show', $category))
            ->assertOk()
            ->assertSee('Deep Dive');
    }

    public function test_edit_form_renders_with_the_current_values(): void
    {
        $this->admin();
        $category = Category::factory()->create(['name' => 'Legacy']);

        $this->get(route('admin.categories.edit', $category))
            ->assertOk()
            ->assertSee('Legacy');
    }

    public function test_update_changes_name_and_regenerates_slug(): void
    {
        $this->admin();
        $category = Category::factory()->create(['name' => 'Old Name', 'slug' => 'old-name']);

        $this->put(route('admin.categories.update', $category), ['name' => 'Brand New'])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Brand New',
            'slug' => 'brand-new',
        ]);
    }

    public function test_update_allows_keeping_its_own_name(): void
    {
        $this->admin();
        $category = Category::factory()->create(['name' => 'Stable', 'slug' => 'stable']);

        $this->put(route('admin.categories.update', $category), ['name' => 'Stable'])
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Stable']);
    }

    public function test_destroy_deletes_an_unused_category(): void
    {
        $this->admin();
        $category = Category::factory()->create();

        $this->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_destroy_refuses_to_delete_a_category_with_posts(): void
    {
        $this->admin();
        $category = Category::factory()->create();
        Post::factory()->create(['category_id' => $category->id]);

        $this->from(route('admin.categories.index'))
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_non_admin_cannot_manage_categories(): void
    {
        $category = Category::factory()->create();

        $this->actingAs(User::factory()->create(['role' => 'user']));

        $this->get(route('admin.categories.index'))->assertForbidden();
        $this->get(route('admin.categories.create'))->assertForbidden();
        $this->post(route('admin.categories.store'), ['name' => 'Nope'])->assertForbidden();
        $this->get(route('admin.categories.show', $category))->assertForbidden();
        $this->get(route('admin.categories.edit', $category))->assertForbidden();
        $this->put(route('admin.categories.update', $category), ['name' => 'Nope'])->assertForbidden();
        $this->delete(route('admin.categories.destroy', $category))->assertForbidden();
    }
}
