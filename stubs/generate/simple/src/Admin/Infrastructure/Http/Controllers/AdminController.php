<?php

declare(strict_types=1);

namespace Src\Admin\Infrastructure\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Admin\Application\GetViewDataTagsUseCase;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Controller;

final class AdminController extends Controller
{
    public function __construct(
        public readonly GetViewDataTagsUseCase $getViewDataTagsUseCase,
    )
    {
    }

    public function tags(Request $request, string $type = null): View | JsonResponse
    {
        $isJson = $request->expectsJson();
        $data = $this->getViewDataTagsUseCase->__invoke($isJson, $type);
        return $isJson
            ? response_json(true, 'Ok', $data->toArray())
            : view('pages.admin.tags', compact('data'));
    }
}
