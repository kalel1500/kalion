<?php

declare(strict_types=1);

namespace Src\Posts\Infrastructure\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Src\Posts\Application\GetViewDataPostListUseCase;
use Src\Posts\Application\GetViewDataPostDetailUseCase;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Controller;

final class PostController extends Controller
{
    public function __construct(
        public readonly GetViewDataPostListUseCase   $getViewDataPostListUseCase,
        public readonly GetViewDataPostDetailUseCase $getViewDataPostDetailUseCase,
    )
    {
    }

    public function list(Request $request): View
    {
        $data = $this->getViewDataPostListUseCase->__invoke($request->input('tag'));
        return view('pages.post.list', compact('data'));
    }

    public function detail(string $slug): View
    {
        $post = $this->getViewDataPostDetailUseCase->__invoke($slug);
        return view('pages.post.detail', compact('post'));
    }
}
