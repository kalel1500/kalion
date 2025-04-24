<?php

declare(strict_types=1);

namespace Src\Tags\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Tags\Application\UpdateOrCreateTagUseCase;
use Src\Tags\Application\DeleteTagUseCase;
use Src\Tags\Application\GetTagListUseCase;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Controller;

final class AjaxTagController extends Controller
{
    public function __construct(
        public readonly GetTagListUseCase        $getTagsListUseCase,
        public readonly UpdateOrCreateTagUseCase $updateOrCreateTagUseCase,
        public readonly DeleteTagUseCase         $deleteTagUseCase,
    )
    {
    }

    public function tags(string $type = null): JsonResponse
    {
        $data = $this->getTagsListUseCase->__invoke($type);
        return response_json(true, 'success', $data->toArray());
    }

    public function create(Request $request): JsonResponse
    {
        $params = $request->validate([
            'name'        => 'required',
            'code'        => 'required',
            'tag_type_id' => 'required',
        ]);
        try {
            $this->updateOrCreateTagUseCase->__invoke($params);
            return response_json(true, __('k::database.model_created_successfully', ['model' => 'Tag']));
        } catch (\Throwable $th) {
            return response_json_error($th);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $params = $request->validate([
            'name'        => 'required',
            'code'        => 'required',
            'tag_type_id' => 'required',
        ]);
        $params = ['id' => $id, ...$params];

        try {
            $this->updateOrCreateTagUseCase->__invoke($params);
            return response_json(true, __('k::database.model_updated_successfully', ['model' => 'Tag']));
        } catch (\Throwable $th) {
            return response_json_error($th);
        }
    }

    public function delete(int $id): JsonResponse
    {
        try {
            $this->deleteTagUseCase->__invoke($id);
            return response_json(true, __('k::database.model_deleted_successfully', ['model' => 'Tag']));
        } catch (\Throwable $th) {
            return response_json_error($th);
        }
    }
}
