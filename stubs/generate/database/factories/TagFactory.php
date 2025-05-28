<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Src\Shared\Infrastructure\Models\Tag;
use Src\Shared\Infrastructure\Models\TagType;

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
