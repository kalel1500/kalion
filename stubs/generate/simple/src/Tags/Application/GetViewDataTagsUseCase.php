<?php

declare(strict_types=1);

namespace Src\Tags\Application;

use Src\Tags\Domain\Objects\DataObjects\FrontTagsDto;
use Src\Tags\Domain\Objects\DataObjects\ViewTagsDto;
use Src\Shared\Domain\Contracts\Repositories\TagTypeRepositoryContract;
use Src\Shared\Domain\Services\Repository\TagTypeService;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelStringNull;

final readonly class GetViewDataTagsUseCase
{
    public function __construct(
        private TagTypeRepositoryContract $tagTypeRepository,
        private TagTypeService $tagTypeService,
    )
    {
    }

    public function __invoke(bool $expectsJson, ?string $currentTypeCode): ViewTagsDto|FrontTagsDto
    {
        $tagTypes       = $this->tagTypeRepository->all();
        $currentTagType = $this->tagTypeService->findByCode(ModelStringNull::new($currentTypeCode));
        if ($expectsJson) {
            return FrontTagsDto::fromArray([
                'currentTagType' => $currentTagType,
                'pluckedTypes' => $tagTypes->pluck('name', 'id'),
            ]);
        }
        return ViewTagsDto::fromArray([
            'currentTagType' => $currentTagType,
            'tagTypes'       => $tagTypes,
        ]);
    }
}
