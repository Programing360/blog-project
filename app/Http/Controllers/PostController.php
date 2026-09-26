<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // Home Page: Display all published posts with search & category filter
    public function index(Request $request)
    {
        $query = Post::with(['user', 'category'])
            ->where('status', 'published')
            ->latest();

        // Search Filter
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        // Category Filter
        if ($request->has('category') && $request->category != '') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $posts = $query->paginate(6)->withQueryString();

        $categories = Category::all();
        $tags = Tag::all();

        return view('posts.index', compact('posts', 'categories', 'tags'));
    }

    // Single Post View Page
    public function show($slug)
    {
        $post = Post::with([
            'user',
            'category',
            'tags',
            'comments.user'
        ])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('posts.show', compact('post'));
    }
}
