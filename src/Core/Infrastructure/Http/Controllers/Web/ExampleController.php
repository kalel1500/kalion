<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Http\Controllers\Web;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Thehouseofel\Kalion\Application\GetIconsUseCase;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Controller;

final class ExampleController extends Controller
{
    public function example1(): View
    {
        return view('kal::pages.examples.example1');
    }

    public function example2(): View
    {
        return view('kal::pages.examples.example2');
    }

    public function example3(): View
    {
        return view('kal::pages.examples.example3');
    }

    public function example4(): View
    {
        return view('kal::pages.examples.example4');
    }

    public function compareHtml(): View
    {
        return view('kal::pages.examples.compare-html');
    }

    public function modifyCookie(): View
    {
        return view('kal::pages.examples.modify-cookie');
    }

    public function icons(Request $request, GetIconsUseCase $useCase): View
    {
        $isShort = $request->input('name') === 'short';

        $data = $useCase->__invoke($isShort);

        // Retornar los nombres a la vista
        return view('kal::pages.examples.icons', compact('data'));
    }
}
