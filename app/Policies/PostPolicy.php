<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Grant every ability to administrators.
     *
     * Returning null (not false) for everyone else is what lets the
     * per-ability methods below run for non-admins.
     */
    public function before(User $user, string $ability): ?bool
    {
        return $user->role === 'admin' ? true : null;
    }

    /**
     * Determine whether the user can update the post.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can delete the post.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }
}
