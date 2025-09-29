<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Contexts\InferenceProblem;

final class ExecuteUseCase
{
    public ExecuteParamsDto $params;
    public UserCollection $users;

    public function __construct()
    {
        $this->params = new ExecuteParamsDto();
        $this->users = new UserCollection([]);
    }

    public function __invoke(): void
    {
        $params = $this->params;

        $comments1 = $this->users->pluck('comments')->toCollection(CommentCollection::class);
        $comments2 = $params->users->pluck('comments')->toCollection(CommentCollection::class);

        UserService::saveComments(comments: $comments1);
        UserService::saveComments(comments: $comments2);
    }
}
