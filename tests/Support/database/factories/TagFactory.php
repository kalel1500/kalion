<?php

namespace Thehouseofel\Kalion\Tests\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Infrastructure\Models\Tag;
use Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Infrastructure\Models\TagType;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    protected $model = Tag::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $word = fake()->unique()->word();
        return [
            'name' => $word,
            'code' => $word,
            'tag_type_id' => TagType::factory(),
        ];
    }
}
