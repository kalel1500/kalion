<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Support\Reflection\Dto;

readonly class ParamMetadata
{
    public function __construct(
        public string  $paramName,
        public ?string $typeName,
        public ?string $class,
        public bool    $isId,
        public bool    $allowsNull,
        public ?string $useMethod,
        public ?array  $makeParams,
        public bool    $isEnum,
        public bool    $isVo,
        public ?string $makeMethod,
        public ?string $propsMethod,
        public ?string $castType,
    )
    {
    }
}

