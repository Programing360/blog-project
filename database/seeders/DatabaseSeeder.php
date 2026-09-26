<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        // Create 5 Categories
        $categories = Category::factory(5)->create();

        // Create 10 Tags
        $tags = Tag::factory(10)->create();

        // Create 20 Dummy Posts with Tags
        Post::factory(20)->create()->each(function ($post) use ($tags) {
            $post->tags()->attach($tags->random(rand(1, 3))->pluck('id'));
        });
    }
}