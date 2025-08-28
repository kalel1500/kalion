<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Feature\Entities;

use Thehouseofel\Kalion\Domain\Traits\KalionAssertions;
use Thehouseofel\Kalion\Tests\TestCase;

class RelationsTest extends TestCase
{
    use KalionAssertions;

    public function test_post_relations()
    {
        $useCase = new \Thehouseofel\Kalion\Tests\Support\Application\GetPostDataUseCase();
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
}
