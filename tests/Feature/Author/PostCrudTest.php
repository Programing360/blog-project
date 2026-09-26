<?php

namespace Tests\Feature\Author;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostCrudTest extends TestCase
{
    use RefreshDatabase;

    private function makeCategoriesAndTags(): array
    {
        $category = Category::create(['name' => 'Laravel', 'slug' => 'laravel']);
        $otherCategory = Category::create(['name' => 'Vue', 'slug' => 'vue']);
        $tag = Tag::create(['name' => 'Testing', 'slug' => 'testing']);

        return [$category, $otherCategory, $tag];
    }

    /**
     * A fake image upload. Uses the declared MIME type rather than
     * UploadedFile::fake()->image() so the suite does not require ext-gd.
     */
    private function fakeImage(string $name = 'cover.jpg'): UploadedFile
    {
        return UploadedFile::fake()->create($name, 64, 'image/jpeg');
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/author/posts')->assertRedirect('/login');
        $this->get('/author/posts/create')->assertRedirect('/login');
    }

    public function test_index_only_lists_posts_owned_by_the_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        [$category] = $this->makeCategoriesAndTags();

        $mine = Post::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'My own post',
            'slug' => 'my-own-post',
            'content' => 'Body',
            'status' => 'published',
        ]);

        Post::create([
            'user_id' => $other->id,
            'category_id' => $category->id,
            'title' => 'Someone elses post',
            'slug' => 'someone-eles-post',
            'content' => 'Body',
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)->get('/author/posts');

        $response->assertOk();
        $response->assertViewIs('author.posts.index');
        $response->assertSee('My own post');
        $response->assertDontSee('Someone elses post');
        $this->assertSame(1, $response->viewData('posts')->total());
        $this->assertTrue($response->viewData('posts')->first()->relationLoaded('tags'));
    }

    public function test_create_form_receives_categories_and_tags(): void
    {
        $user = User::factory()->create();
        [$category, , $tag] = $this->makeCategoriesAndTags();

        $response = $this->actingAs($user)->get('/author/posts/create');

        $response->assertOk();
        $response->assertViewIs('author.posts.create');
        $this->assertTrue($response->viewData('categories')->contains($category));
        $this->assertTrue($response->viewData('tags')->contains($tag));
    }

    public function test_store_creates_post_with_slug_image_and_tags(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        [$category, , $tag] = $this->makeCategoriesAndTags();

        $response = $this->actingAs($user)->post('/author/posts', [
            'title' => 'Hello World Post',
            'category_id' => $category->id,
            'content' => 'Some content',
            'status' => 'published',
            'tags' => [$tag->id],
            'image' => $this->fakeImage('cover.jpg'),
        ]);

        $response->assertRedirect('/author/posts');
        $response->assertSessionHas('status');

        $post = Post::sole();

        $this->assertSame($user->id, $post->user_id);
        $this->assertSame('hello-world-post', $post->slug);
        $this->assertSame('published', $post->status);
        $this->assertNotNull($post->image);
        $this->assertStringStartsWith('posts/', $post->image);
        Storage::disk('public')->assertExists($post->image);
        $this->assertTrue($post->tags->contains($tag));
    }

    public function test_store_generates_a_unique_slug(): void
    {
        $user = User::factory()->create();
        [$category] = $this->makeCategoriesAndTags();

        foreach (range(1, 2) as $attempt) {
            $this->actingAs($user)->post('/author/posts', [
                'title' => 'Duplicate Title',
                'category_id' => $category->id,
                'content' => 'Body',
                'status' => 'draft',
            ])->assertRedirect('/author/posts');
        }

        $this->assertSame(
            ['duplicate-title', 'duplicate-title-2'],
            Post::orderBy('id')->pluck('slug')->all()
        );
    }

    public function test_store_validates_input(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/author/posts', [])
            ->assertSessionHasErrors(['title', 'category_id', 'content', 'status']);

        $this->assertSame(0, Post::count());
    }

    public function test_store_rejects_non_image_uploads(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        [$category] = $this->makeCategoriesAndTags();

        $this->actingAs($user)->post('/author/posts', [
            'title' => 'Bad upload',
            'category_id' => $category->id,
            'content' => 'Body',
            'status' => 'draft',
            'image' => UploadedFile::fake()->create('malware.php', 10, 'application/x-php'),
        ])->assertSessionHasErrors('image');
    }

    public function test_edit_denied_for_non_owner(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        [$category] = $this->makeCategoriesAndTags();

        $post = Post::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'title' => 'Private',
            'slug' => 'private',
            'content' => 'Body',
            'status' => 'draft',
        ]);

        $this->actingAs($intruder)->get("/author/posts/{$post->id}/edit")->assertForbidden();
        $this->actingAs($intruder)->put("/author/posts/{$post->id}", [
            'title' => 'Hijacked',
            'category_id' => $category->id,
            'content' => 'Body',
            'status' => 'draft',
        ])->assertForbidden();
        $this->actingAs($intruder)->delete("/author/posts/{$post->id}")->assertForbidden();

        $this->assertSame('Private', $post->fresh()->title);
    }

    public function test_edit_form_is_prefilled(): void
    {
        $user = User::factory()->create();
        [$category, , $tag] = $this->makeCategoriesAndTags();

        $post = Post::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Editable',
            'slug' => 'editable',
            'content' => 'Original body',
            'status' => 'draft',
        ]);
        $post->tags()->attach($tag);

        $response = $this->actingAs($user)->get("/author/posts/{$post->id}/edit");

        $response->assertOk();
        $response->assertViewIs('author.posts.edit');
        $this->assertTrue($response->viewData('post')->tags->contains($tag));
    }

    public function test_update_replaces_image_and_deletes_the_old_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        [$category, $otherCategory, $tag] = $this->makeCategoriesAndTags();

        $post = Post::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Original',
            'slug' => 'original',
            'content' => 'Original body',
            'status' => 'draft',
        ]);

        Storage::disk('public')->put('posts/original.jpg', 'old-bytes');
        $post->update(['image' => 'posts/original.jpg']);

        $this->actingAs($user)->put("/author/posts/{$post->id}", [
            'title' => 'Updated title',
            'category_id' => $otherCategory->id,
            'content' => 'Updated body',
            'status' => 'published',
            'tags' => [$tag->id],
            'image' => $this->fakeImage('new.jpg'),
        ])->assertRedirect('/author/posts');

        $post->refresh();

        $this->assertSame('Updated title', $post->title);
        $this->assertSame('Updated body', $post->content);
        $this->assertSame('published', $post->status);
        $this->assertSame($otherCategory->id, $post->category_id);

        Storage::disk('public')->assertMissing('posts/original.jpg');
        Storage::disk('public')->assertExists($post->image);
        $this->assertNotSame('posts/original.jpg', $post->image);

        $this->assertSame([$tag->id], $post->tags->pluck('id')->all());
    }

    public function test_update_keeps_image_when_none_uploaded(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        [$category] = $this->makeCategoriesAndTags();

        $post = Post::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Keep image',
            'slug' => 'keep-image',
            'content' => 'Body',
            'status' => 'draft',
            'image' => 'posts/keep.jpg',
        ]);
        Storage::disk('public')->put('posts/keep.jpg', 'bytes');

        $this->actingAs($user)->put("/author/posts/{$post->id}", [
            'title' => 'Keep image renamed',
            'category_id' => $category->id,
            'content' => 'Body',
            'status' => 'draft',
        ])->assertRedirect('/author/posts');

        $this->assertSame('posts/keep.jpg', $post->fresh()->image);
        Storage::disk('public')->assertExists('posts/keep.jpg');
    }

    public function test_update_detaches_removed_tags(): void
    {
        $user = User::factory()->create();
        [$category, , $tag] = $this->makeCategoriesAndTags();

        $post = Post::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Detach',
            'slug' => 'detach',
            'content' => 'Body',
            'status' => 'draft',
        ]);
        $post->tags()->attach($tag);

        $this->actingAs($user)->put("/author/posts/{$post->id}", [
            'title' => 'Detach',
            'category_id' => $category->id,
            'content' => 'Body',
            'status' => 'draft',
            'tags' => [],
        ])->assertRedirect('/author/posts');

        $this->assertCount(0, $post->fresh()->tags);
    }

    public function test_destroy_removes_post_and_image(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        [$category, , $tag] = $this->makeCategoriesAndTags();

        $post = Post::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Doomed',
            'slug' => 'doomed',
            'content' => 'Body',
            'status' => 'draft',
            'image' => 'posts/doomed.jpg',
        ]);
        Storage::disk('public')->put('posts/doomed.jpg', 'bytes');
        $post->tags()->attach($tag);

        $this->actingAs($user)->delete("/author/posts/{$post->id}")
            ->assertRedirect('/author/posts');

        $this->assertSame(0, Post::count());
        $this->assertDatabaseMissing('post_tag', ['post_id' => $post->id]);
        Storage::disk('public')->assertMissing('posts/doomed.jpg');
    }
}
