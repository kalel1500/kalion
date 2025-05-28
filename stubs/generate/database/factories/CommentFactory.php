<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Src\Shared\Infrastructure\Models\Comment;
use Src\Shared\Infrastructure\Models\User;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => fake()->paragraph(),
            'user_id' => User::factory(),
            'post_id'  => null,
            'comment_id' => null,
        ];
    }
}
