<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\DataObjects;

use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use Thehouseofel\Kalion\Core\Domain\Contracts\ArrayConvertible;
use Thehouseofel\Kalion\Core\Domain\Contracts\ArrayResolvable;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Contracts\MakeArrayable;
use Thehouseofel\Kalion\Core\Domain\Support\Reflection\Dto\ReflectionConfig;
use Thehouseofel\Kalion\Core\Domain\Support\Reflection\ReflectionResolvable;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\ArrayVo;

abstract class AbstractDataTransferObject implements ArrayConvertible, ArrayResolvable, MakeArrayable, Jsonable, JsonSerializable
{
    use ReflectionResolvable;

    protected static function reflectionConfig(): ReflectionConfig
    {
        return new ReflectionConfig(
            props_from_public       : true,
            allow_id_union          : false,
            allow_disable_reflection: true,
            expected_exception_class: AbstractDataTransferObject::class,
        );
    }

    /**
     * @template T of array|null
     * @param T $data
     * @return (T is null ? null : static)
     */
    public static function fromArray(?array $data): ?static
    {
        if (empty($data)) return null;
        return static::make($data);
    }

    /**
     * @template T of array|null
     * @param T $data
     * @return (T is null ? null : static)
     */
    public static function resolveFromArray(?array $data): ?static
    {
        if (empty($data)) return null;
        return static::make($data, true);
    }

    /**
     * @template T of string|null
     * @param T $data
     * @return (T is null ? null : static)
     */
    public static function fromJson(?string $data): static|null
    {
        if (is_null($data)) return null;
        return static::fromArray(json_decode($data, true));
    }

    public static function tryFromArray($data): ?static
    {
        try {
            return static::fromArray($data);
        } catch (\Throwable) {
            return null;
        }
    }

    public static function tryFromJson($data): ?static
    {
        try {
            return static::fromJson($data);
        } catch (\Throwable) {
            return null;
        }
    }


    public function toArray(): array
    {
        return $this->props();
    }

    public function toMakeArray(): array
    {
        return $this->props();
    }

    public function toArrayVo(): ArrayVo
    {
        return ArrayVo::from($this->toArray());
    }

    public function toObject(): object|array
    {
        return legacy_json_to_object($this->toArray());
    }

    public function toJson($options = 0): false|string
    {
        return json_encode($this->toArray(), $options);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __toString()
    {
        return $this->toJson();
    }
}
