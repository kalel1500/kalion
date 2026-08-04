<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Shared\Domain\Contracts\Repositories\CommentRepository;
use Src\Shared\Domain\Contracts\Repositories\PostRepository;
use Src\Shared\Domain\Contracts\Repositories\TagRepository;
use Src\Shared\Domain\Contracts\Repositories\TagTypeRepository;
use Src\Shared\Infrastructure\Repositories\Eloquent\EloquentCommentRepository;
use Src\Shared\Infrastructure\Repositories\Eloquent\EloquentPostRepository;
use Src\Shared\Infrastructure\Repositories\Eloquent\EloquentTagRepository;
use Src\Shared\Infrastructure\Repositories\Eloquent\EloquentTagTypeRepository;

final class DependencyServiceProvider extends ServiceProvider
{
    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        CommentRepository::class => EloquentCommentRepository::class,
        PostRepository::class    => EloquentPostRepository::class,
        TagRepository::class     => EloquentTagRepository::class,
        TagTypeRepository::class => EloquentTagTypeRepository::class,
    ];
}
