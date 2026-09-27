<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard with summary stats and recent activity.
     */
    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'totalPosts' => Post::count(),
            'totalUsers' => User::count(),
            'totalComments' => Comment::count(),
            'totalCategories' => Category::count(),
            'recentPosts' => Post::with(['user', 'category'])->latest()->take(5)->get(),
            'recentUsers' => User::withCount('posts')->latest()->take(5)->get(),
        ]);
    }
}
