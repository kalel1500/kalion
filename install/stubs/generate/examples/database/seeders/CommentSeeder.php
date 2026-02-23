<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Src\Shared\Infrastructure\Models\Comment;
use Src\Shared\Infrastructure\Models\Post;
use Src\Shared\Infrastructure\Models\User;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()->where('email', '!=', 'test@example.com')->limit(10)->get();
        $posts = Post::all();

        // Para cada post, añadir 3 comentarios de usuarios aleatorios
        $posts->each(function ($post) use ($users) {
            Comment::factory(3)->for($post)->create([
                'user_id' => $users->random()->id,
            ]);
        });

        // Para cada comentario, crear 3 respuestas
        Comment::all()->each(function ($comment) use ($users) {
            Comment::factory(3)->for($comment, 'comment')->create([
                'user_id' => $users->random()->id,
                'post_id' => null,
            ]);
        });
    }
}
