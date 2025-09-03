<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Application;

use Thehouseofel\Kalion\Tests\Support\Contexts\Shared\Domain\Exceptions\TestException;
use Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Domain\Objects\Entities\Collections\PostCollection;
use Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Domain\Objects\Entities\Collections\TagCollection;
use Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Domain\Objects\Entities\TagEntity;
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

    public function getPluckData(): bool
    {
        $data = Post::query()
            ->with(['tags.tagType', 'tags.posts', 'comments'])
            ->limit(3)
            ->get();
        $posts = PostCollection::fromArray($data->toArray(), ['tags:f' => ['tagType:f', 'posts'], 'comments'], true);
        // $posts = PostCollection::fromArray($data->toArray(), ['tags:f.tagType:f', 'tags.posts'], 'comments);

        $numberComments = $posts->pluck('number_comments');

        if ($numberComments->countInt()->isLessThan(1)) {
            throw TestException::emptyCollection('$numberComments');
        }

        $tags = $posts->pluck('tags')->collapse()->toCollection(TagCollection::class);

        $tagTypes = $tags->pluck('tagType')->toArray();

        if (!isset($tagTypes[0]['slug'])) {
            throw new TestException('No se ha encontrado la key "slug" en el array de $tagTypes');
        }

        $tagTypes = $tags->pluck('type_slug')->toArray();

        $typeSlugs = [];
        foreach ($tags as $tag) {
            /** @var TagEntity $tag */
            $typeSlugs[] = $tag->tagType()->slug();
        }

        if ($tagTypes != $typeSlugs) {
            throw new TestException('Los tipos de tags son diferentes entre el pluck y la relacion');
        }

        return true;
    }
}
