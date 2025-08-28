<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Application;

use Thehouseofel\Kalion\Tests\Support\Domain\Exceptions\TestException;
use Thehouseofel\Kalion\Tests\Support\Domain\Objects\Entities\Collections\PostCollection;
use Thehouseofel\Kalion\Tests\Support\Models\Post;

final class GetPostDataUseCase
{
    public function getPostsWithRelations(): PostCollection
    {
        $data = Post::query()
            ->with(['tags.tagType', 'tags.posts', 'comments' => fn($q) => $q->where('id', 99999)])
            ->limit(3)
            ->get();
        $posts = PostCollection::fromArray($data->toArray(), ['tags:f' => ['tagType:f', 'posts'], 'comments'], true);
        // $posts = PostCollection::fromArray($data->toArray(), ['tags:f.tagType:f', 'tags.posts'], 'comments);

        if ($posts->countInt()->isLessThan(1)) {
            throw TestException::emptyCollection('Posts');
        }

        return $posts;
    }

    public function getPluckData()
    {

        $data = Post::query()
            ->with(['tags.tagType', 'tags.posts', 'comments' => fn($q) => $q->where('id', 99999)])
            ->limit(3)
            ->get();
        $posts = PostCollection::fromArray($data->toArray(), ['tags:f' => ['tagType:f', 'posts'], 'comments'], true);
        // $posts = PostCollection::fromArray($data->toArray(), ['tags:f.tagType:f', 'tags.posts'], 'comments);

        $test = $posts->pluck('number_comments');
        dd($test);
    }
}
