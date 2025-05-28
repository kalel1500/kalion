<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Src\Shared\Infrastructure\Models\Post;
use Src\Shared\Infrastructure\Models\User;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'   => fake()->sentence(3),
            'content' => fake()->paragraph(),
            'slug'    => fake()->slug(),
            'user_id' => User::factory(),
        ];
    }
}
