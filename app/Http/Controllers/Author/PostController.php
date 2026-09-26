<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Display a paginated list of posts belonging to the authenticated user.
     */
    public function index(): View
    {
        $posts = Post::where('user_id', Auth::id())
            ->with(['category', 'tags'])
            ->latest()
            ->paginate(10);

        return view('author.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create(): View
    {
        return view('author.posts.create', [
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created post.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        $post = Auth::user()->posts()->create([
            'title' => $validated['title'],
            'category_id' => $validated['category_id'],
            'content' => $validated['content'],
            'status' => $validated['status'],
            'slug' => $this->uniqueSlug($validated['title']),
            'image' => $this->storeImage($request),
        ]);

        $post->tags()->sync($validated['tags'] ?? []);

        return redirect()
            ->route('author.posts.index')
            ->with('status', 'Post created successfully.');
    }

    /**
     * Show the form for editing the given post.
     */
    public function edit(Post $post): View
    {
        $this->authorizeOwnership($post);

        return view('author.posts.edit', [
            'post' => $post->load('tags'),
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the given post.
     */
    public function update(Request $request, Post $post): RedirectResponse
    {
        $this->authorizeOwnership($post);

        $validated = $request->validate($this->rules());

        if ($request->hasFile('image')) {
            $this->deleteImage($post->image);
        }

        $post->update([
            'title' => $validated['title'],
            'category_id' => $validated['category_id'],
            'content' => $validated['content'],
            'status' => $validated['status'],
            'image' => $request->hasFile('image') ? $this->storeImage($request) : $post->image,
        ]);

        $post->tags()->sync($validated['tags'] ?? []);

        return redirect()
            ->route('author.posts.index')
            ->with('status', 'Post updated successfully.');
    }

    /**
     * Remove the given post.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $this->authorizeOwnership($post);

        $this->deleteImage($post->image);

        $post->delete();

        return redirect()
            ->route('author.posts.index')
            ->with('status', 'Post deleted successfully.');
    }

    /**
     * Validation rules shared by store and update.
     *
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'content' => ['required', 'string'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ];
    }

    /**
     * Ensure the authenticated user owns the post.
     */
    private function authorizeOwnership(Post $post): void
    {
        abort_if($post->user_id !== Auth::id(), 403, 'You are not authorized to modify this post.');
    }

    /**
     * Persist the uploaded image on the public disk and return its path.
     */
    private function storeImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        return $request->file('image')->store('posts', 'public');
    }

    /**
     * Delete a previously uploaded image from the public disk.
     */
    private function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Build a slug that is not already taken by another post.
     */
    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'post';
        $slug = $base;
        $suffix = 2;

        while (Post::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
