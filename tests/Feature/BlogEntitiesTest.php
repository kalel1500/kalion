<?php

declare(strict_types=1);

use Thehouseofel\Kalion\Domain\Concerns\KalionAssertions;
use Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Domain\Objects\DataObjects\DetailDto;
use Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Domain\Objects\Entities\PostEntity;
use Thehouseofel\Kalion\Tests\TestCase;

class BlogEntitiesTest extends TestCase
{
    use KalionAssertions;

    public function test_create_entity_without_id()
    {
        $test = PostEntity::fromArray([
            'title'   => 'aa',
            'content' => 'bb',
            'slug'    => 'cc',
            'user_id' => 1,
        ]);
        $this->assertEquals('aa', $test->title->value);
    }

    public function test_create_a_dto_with_entity_with_relation()
    {
        $detail = DetailDto::fromArray([
            'name' => 'aa',
            'post' => [
                'title'   => 'aa',
                'content' => 'bb',
                'slug'    => 'cc',
                'user_id' => 1,
                'comments' => [
                    [
                        'id'         => 1,
                        'content'    => 'aaaa',
                        'user_id'    => 1,
                        'post_id'    => 1,
                        'comment_id' => null,
                    ],
                    [
                        'id'         => 1,
                        'content'    => 'aaaa',
                        'user_id'    => 1,
                        'post_id'    => 1,
                        'comment_id' => null,
                    ]
                ]
            ],
            'tagType' => [
                'id' => 1,
                'name' => 'qqq',
                'code' => 'qqq',
            ]
        ]);
        $detailArray = $detail->toArray();

        $this->assertArrayStructure([
            'name',
            'post' => [
                'id',
                'title',
                'content',
                'slug',
                'user_id',
                'created_at',
                'number_comments',
                'comments' => [
                    '*' => [
                        'id',
                        'content',
                        'user_id',
                        'post_id',
                        'comment_id',
                    ]
                ]
            ],
            'tagType' => [
                'id',
                'name',
                'code',
                'slug',
            ]
        ], $detailArray);
        $this->assertEquals(2, $detailArray['post']['number_comments']);
        $this->assertEquals('qqq', $detailArray['tagType']['slug']);
    }
}
