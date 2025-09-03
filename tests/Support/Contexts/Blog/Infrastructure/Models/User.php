<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Infrastructure\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Thehouseofel\Kalion\Infrastructure\Models\User as KalionUser;
use Thehouseofel\Kalion\Tests\Support\Database\Factories\UserFactory;

class User extends KalionUser
{
    static string $factory = UserFactory::class;

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
