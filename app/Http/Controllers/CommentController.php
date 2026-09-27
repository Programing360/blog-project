<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // Store a new comment on a post
    public function store(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $post->comments()->create([
            'user_id' => Auth::id(),
            'body' => $validated['body'],
        ]);

        return back()->with('status', 'Comment posted successfully.');
    }

    // Delete a comment written by the current user on their own post
    public function destroy(Comment $comment): RedirectResponse
    {
        abort_if(! $this->canDelete($comment), 403, 'You are not authorized to delete this comment.');

        $comment->delete();

        return back()->with('status', 'Comment deleted successfully.');
    }

    /**
     * A comment may be removed by its own author or by the author of the post.
     */
    private function canDelete(Comment $comment): bool
    {
        return Auth::id() === $comment->user_id
            || Auth::id() === $comment->post->user_id;
    }
}
