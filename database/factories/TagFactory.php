<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();
    return [
        'name' => ucfirst($name),
        'slug' => Str::slug($name),
    ];
    }
}
