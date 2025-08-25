<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Feature\Database;

use Thehouseofel\Kalion\Domain\Objects\Collections\CollectionAny;
use Thehouseofel\Kalion\Tests\Support\Domain\Objects\Entities\Collections\PostCollection;
use Thehouseofel\Kalion\Tests\Support\Domain\Objects\Entities\PostEntity;
use Thehouseofel\Kalion\Tests\Support\Repositories\Eloquent\PostRepository;
use Thehouseofel\Kalion\Tests\TestCase;

class PostRepositoryTest extends TestCase
{
    public function test_all_returns_post_collection_with_relations_and_pluck_works()
    {

        $repo = new PostRepository();

        $posts = $repo->all();

        $this->assertInstanceOf(PostCollection::class, $posts);
        $this->assertGreaterThan(0, $posts->count(), 'No hay posts en la colección (revisa el seeder).');

        // pluck
        $titles = $posts->pluck('title');
        $this->assertInstanceOf(CollectionAny::class, $titles);
        $this->assertNotEmpty($titles->toArray());

        /** @var PostEntity $first */
        $first = $posts->first();
        $this->assertNotNull($first);
        $this->assertTrue(method_exists($first, 'comments')); // existe la relación en la entity
        $comments = $first->comments();
        $this->assertNotNull($comments);
        $this->assertGreaterThanOrEqual(0, $comments->count());
    }
}
