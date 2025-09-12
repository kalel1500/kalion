<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Thehouseofel\Kalion\Domain\Concerns\KalionAssertions;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters\CheckableProcessVo;
use Thehouseofel\Kalion\Tests\Support\Contexts\Shared\Domain\Objects\DataObjects\ExampleDto;
use Thehouseofel\Kalion\Tests\Support\Contexts\Shared\Domain\Objects\DataObjects\ExampleDtoCollection;
use Thehouseofel\Kalion\Tests\TestCase;

class BlogRelationsTest extends TestCase
{
    use KalionAssertions;

    public function test_post_relations()
    {
        $useCase = new \Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Application\GetPostDataUseCase();
        $posts = $useCase->getPostsWithRelations();

        $this->assertArrayStructure([
            '*' => [
                'number_comments',
                'tags' => [
                    '*' => [
                        'type_name',
                        'type_slug',
                        'tag_type' => ['slug',],
                        'posts' => ['*' => ['id', 'title',]]
                    ]
                ],
                'comments' => [
                    '*' => [
                        'id',
                        'content',
                        'user_id',
                        'post_id',
                    ]
                ]
            ]
        ], $posts->toArray());
    }

    public function test_post_pluck()
    {
        $useCase = new \Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Application\GetPostDataUseCase();
        $pluck = $useCase->getPluckData();
        $this->assertTrue($pluck);
    }

    public static function getDto(): array
    {
        return [
            [new ExampleDtoCollection(
                new ExampleDto('aaa', 'bbb', 3, CheckableProcessVo::queue),
                new ExampleDto('aaa', 'bbb', 3, CheckableProcessVo::queue),
            )],
        ];
    }

    #[DataProvider('getDto')]
    public function test_dto_pluck_with_backed_enum(ExampleDtoCollection $dto)
    {
        $firstCheck = $dto->pluck('enum')->first();
        $this->assertEquals(CheckableProcessVo::queue->value, $firstCheck);
    }

    #[DataProvider('getDto')]
    public function test_dto_pluck_field_only_in_to_array(ExampleDtoCollection $dto)
    {
        $firstOnlyInToArray = $dto->pluck('only_in_to_array')->first();
        $this->assertEquals('text', $firstOnlyInToArray);
    }
}
