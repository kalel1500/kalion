<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Application;

use Thehouseofel\Kalion\Tests\Support\Contexts\Shared\Domain\Exceptions\TestException;
use Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Domain\Objects\Entities\Collections\PostCollection;
use Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Infrastructure\Models\Post;

final class GetPostDataUseCase
{
    public function getPostsWithRelations(): PostCollection
    {
        $data = Post::query()
            ->with(['tags.tagType', 'tags.posts', 'comments'])
            ->limit(3)
            ->get();
        $posts = PostCollection::fromArray($data->toArray(), ['tags:f' => ['tagType:f', 'posts'], 'comments'], true);
        // $posts = PostCollection::fromArray($data->toArray(), ['tags:f.tagType:f', 'tags.posts'], 'comments);

        if ($posts->countInt()->isLessThan(1)) {
            throw TestException::emptyCollection('Posts');
        }

        return $posts;
    }
}
