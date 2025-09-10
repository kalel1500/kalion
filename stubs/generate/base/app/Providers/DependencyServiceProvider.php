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
        \Src\Shared\Domain\Contracts\Repositories\CommentRepositoryContract::class => \Src\Shared\Infrastructure\Repositories\Eloquent\EloquentCommentRepository::class,
        \Src\Shared\Domain\Contracts\Repositories\PostRepositoryContract::class    => \Src\Shared\Infrastructure\Repositories\Eloquent\EloquentPostRepository::class,
        \Src\Shared\Domain\Contracts\Repositories\TagRepositoryContract::class     => \Src\Shared\Infrastructure\Repositories\Eloquent\EloquentTagRepository::class,
        \Src\Shared\Domain\Contracts\Repositories\TagTypeRepositoryContract::class => \Src\Shared\Infrastructure\Repositories\Eloquent\EloquentTagTypeRepository::class,
    ];
}
