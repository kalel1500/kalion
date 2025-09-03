<?php

namespace Thehouseofel\Kalion\Tests\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Infrastructure\Models\TagType;

/**
 * @extends Factory<TagType>
 */
class TagTypeFactory extends Factory
{
    protected $model = TagType::class;

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
        ];
    }
}
