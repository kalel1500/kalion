<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Src\Shared\Infrastructure\Models\Post;
use Src\Shared\Infrastructure\Models\Tag;
use Src\Shared\Infrastructure\Models\User;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()->where('email', '!=', 'test@example.com')->limit(10)->get();
        $tags = Tag::all();

        // Para cada usuario, crear 3 posts
        $users->each(function ($user) use ($users, $tags) {
            $posts = Post::factory(3)->for($user)->create();

            // Asignar 2-4 tags aleatorios por post
            $posts->each(function ($post) use ($tags) {
                $post->tags()->attach($tags->random(rand(2, 4))->pluck('id'));
            });
        });

    }
}
