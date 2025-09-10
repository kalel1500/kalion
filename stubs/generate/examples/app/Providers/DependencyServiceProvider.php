<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

final class DependencyServiceProvider extends ServiceProvider
{
    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        \Src\Shared\Domain\Contracts\Repositories\CommentRepository::class => \Src\Shared\Infrastructure\Repositories\Eloquent\EloquentCommentRepository::class,
        \Src\Shared\Domain\Contracts\Repositories\PostRepository::class    => \Src\Shared\Infrastructure\Repositories\Eloquent\EloquentPostRepository::class,
        \Src\Shared\Domain\Contracts\Repositories\TagRepository::class     => \Src\Shared\Infrastructure\Repositories\Eloquent\EloquentTagRepository::class,
        \Src\Shared\Domain\Contracts\Repositories\TagTypeRepository::class => \Src\Shared\Infrastructure\Repositories\Eloquent\EloquentTagTypeRepository::class,
    ];
}
