<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * List every category with its post count.
     */
    public function index(): View
    {
        $categories = Category::withCount('posts')->orderBy('name')->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show a single category along with its posts.
     */
    public function show(Category $category): View
    {
        return view('admin.categories.show', [
            'category' => $category,
            'posts' => $category->posts()->with('user')->latest()->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a category.
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Persist a new category.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        Category::create([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name']),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Category created successfully.');
    }

    /**
     * Show the form for editing a category.
     */
    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the given category.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate($this->rules($category));

        $category->update([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name'], $category->id),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Category updated successfully.');
    }

    /**
     * Delete a category that has no posts attached.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // posts.category_id is ON DELETE CASCADE, so removing a category that
        // still has posts would silently destroy them. Refuse instead.
        if ($category->posts()->exists()) {
            return back()->with('error', sprintf(
                'Cannot delete "%s": %d post(s) still use it. Reassign those posts first.',
                $category->name,
                $category->posts()->count()
            ));
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Category deleted successfully.');
    }

    /**
     * Validation rules shared by store and update.
     *
     * @return array<string, mixed>
     */
    private function rules(?Category $ignore = null): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($ignore?->id),
            ],
        ];
    }

    /**
     * Build a category slug that is not already taken.
     */
    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'category';
        $slug = $base;
        $suffix = 2;

        while (Category::where('slug', $slug)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
