<?php

declare(strict_types=1);

use Thehouseofel\Kalion\Core\Domain\Concerns\KalionAssertions;
use Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Domain\Objects\Entities\Collections\OtherCollection;
use Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Domain\Objects\Entities\OtherEntity;
use Thehouseofel\Kalion\Tests\TestCase;

class BlogCollectionsTest extends TestCase
{
    use KalionAssertions;

    public function test_collection_entity_map()
    {
        $test = OtherCollection::fromArray([
            [
                'id'      => 1,
                'title'   => 'aa',
                'content' => 'aa',
            ],
            [
                'id'      => 2,
                'title'   => 'bb',
                'content' => 'bb',
            ],
            [
                'id'      => 3,
                'title'   => 'cc',
                'content' => 'cc',
            ],
        ]);

        $result = $test->map(function (OtherEntity $item) {
            $item->otherData = 2;
            return $item;
        })->toArray();

//        dd($result);

        $this->assertEquals([
            [
                'id'        => 1,
                'title'     => 'aa',
                'content'   => 'aa',
                'otherData' => 2,
            ],
            [
                'id'        => 2,
                'title'     => 'bb',
                'content'   => 'bb',
                'otherData' => 2,
            ],
            [
                'id'        => 3,
                'title'     => 'cc',
                'content'   => 'cc',
                'otherData' => 2,
            ],
        ], $result);
    }
}
