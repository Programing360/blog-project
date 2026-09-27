<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    private function makePost(?User $owner = null): Post
    {
        $category = Category::first() ?? Category::create(['name' => 'Laravel', 'slug' => 'laravel']);

        return Post::create([
            'user_id' => ($owner ?? User::factory()->create())->id,
            'category_id' => $category->id,
            'title' => 'Commentable Post',
            'slug' => 'commentable-post-'.uniqid(),
            'content' => 'Body',
            'status' => 'published',
        ]);
    }

    // ---------------- store ----------------

    public function test_guests_cannot_comment(): void
    {
        $post = $this->makePost();

        $this->post("/posts/{$post->id}/comments", ['body' => 'Sneaky'])
            ->assertRedirect('/login');

        $this->assertSame(0, Comment::count());
    }

    public function test_authenticated_user_can_comment(): void
    {
        $post = $this->makePost();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from("/posts/{$post->slug}")
            ->post("/posts/{$post->id}/comments", ['body' => 'Great read!']);

        $response->assertRedirect("/posts/{$post->slug}");
        $response->assertSessionHas('status', 'Comment posted successfully.');

        $comment = Comment::sole();
        $this->assertSame($user->id, $comment->user_id);
        $this->assertSame($post->id, $comment->post_id);
        $this->assertSame('Great read!', $comment->body);
    }

    public function test_comment_body_is_required_and_capped_at_1000_characters(): void
    {
        $post = $this->makePost();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post("/posts/{$post->id}/comments", ['body' => ''])
            ->assertSessionHasErrors('body');

        $this->actingAs($user)
            ->post("/posts/{$post->id}/comments", ['body' => str_repeat('a', 1001)])
            ->assertSessionHasErrors('body');

        $this->actingAs($user)
            ->post("/posts/{$post->id}/comments", ['body' => str_repeat('a', 1000)])
            ->assertSessionHasNoErrors();

        $this->assertSame(1, Comment::count());
    }

    public function test_comment_appears_on_the_post_page(): void
    {
        $post = $this->makePost();
        $user = User::factory()->create(['name' => 'Ada Lovelace']);

        $this->actingAs($user)->post("/posts/{$post->id}/comments", ['body' => 'Insightful.']);

        $response = $this->get("/posts/{$post->slug}");

        $response->assertOk();
        $response->assertSee('Ada Lovelace');
        $response->assertSee('Insightful.');
        // Relative timestamp, not a formatted date.
        $response->assertSee($post->comments->first()->created_at->diffForHumans());
        $response->assertSee('(1)');
    }

    // ---------------- destroy ----------------

    public function test_guests_cannot_delete_comments(): void
    {
        $post = $this->makePost();
        $author = User::factory()->create();
        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $author->id,
            'body' => 'Mine',
        ]);

        $this->delete("/comments/{$comment->id}")->assertRedirect('/login');

        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }

    public function test_comment_author_can_delete_their_comment(): void
    {
        $post = $this->makePost();
        $author = User::factory()->create();
        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $author->id,
            'body' => 'Mine',
        ]);

        $response = $this->actingAs($author)
            ->from("/posts/{$post->slug}")
            ->delete("/comments/{$comment->id}");

        $response->assertRedirect("/posts/{$post->slug}");
        $response->assertSessionHas('status', 'Comment deleted successfully.');

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_post_author_can_delete_someone_elses_comment(): void
    {
        $owner = User::factory()->create();
        $post = $this->makePost($owner);
        $commenter = User::factory()->create();

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $commenter->id,
            'body' => 'Guest comment',
        ]);

        $this->actingAs($owner)
            ->delete("/comments/{$comment->id}")
            ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_unrelated_user_cannot_delete_a_comment(): void
    {
        $post = $this->makePost();
        $commenter = User::factory()->create();
        $stranger = User::factory()->create();

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $commenter->id,
            'body' => 'Not yours',
        ]);

        $this->actingAs($stranger)
            ->delete("/comments/{$comment->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }

    public function test_delete_button_is_only_rendered_for_permitted_users(): void
    {
        $owner = User::factory()->create();
        $post = $this->makePost($owner);
        $commenter = User::factory()->create();
        $stranger = User::factory()->create();

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $commenter->id,
            'body' => 'Visible delete',
        ]);

        $route = route('comments.destroy', $comment);

        $this->actingAs($commenter)->get("/posts/{$post->slug}")->assertSee($route);
        $this->actingAs($owner)->get("/posts/{$post->slug}")->assertSee($route);
        $this->actingAs($stranger)->get("/posts/{$post->slug}")->assertDontSee($route);
        $this->get("/posts/{$post->slug}")->assertDontSee($route);
    }
}
