<?php

declare(strict_types=1);

namespace Src\Tags\Application;

use Src\Shared\Domain\Contracts\Repositories\TagTypeRepository;
use Src\Tags\Domain\Objects\DataObjects\FrontTagsDto;
use Src\Tags\Domain\Objects\DataObjects\ViewTagsDto;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringNullVo;

final readonly class GetViewDataTagsUseCase
{
    public function __construct(
        private TagTypeRepository $tagTypeRepository,
    )
    {
    }

    public function __invoke(bool $expectsJson, ?string $currentTypeCode): ViewTagsDto|FrontTagsDto
    {
        $tagTypes       = $this->tagTypeRepository->all();

        $code = StringNullVo::from($currentTypeCode);
        $currentTagType = $code->isNull()
            ? null :
            $this->tagTypeRepository->findByCode($code->toNotNull());

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
