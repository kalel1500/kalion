<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Support\Reflection\Dto;

readonly class ComputedMetadata
{
    /**
     * @param string[] $contexts
     */
    public function __construct(
        public string  $methodName,
        public array   $contexts,
        public bool    $addOnFull,
        public bool    $isEnum,
        public bool    $isVo,
        public ?string $propsMethod,
    )
    {
    }
}

