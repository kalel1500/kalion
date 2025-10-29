<?php

declare(strict_types=1);

use Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Domain\Objects\Entities\PostEntity;
use Thehouseofel\Kalion\Tests\TestCase;

class BlogEntitiesTest extends TestCase
{
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
}
