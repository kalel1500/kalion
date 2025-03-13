<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects;

use Illuminate\Contracts\Support\Jsonable;
use ReflectionClass;
use Thehouseofel\Kalion\Domain\Contracts\Arrayable;
use Thehouseofel\Kalion\Domain\Contracts\BuildArrayable;
use Thehouseofel\Kalion\Domain\Exceptions\AppException;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\ContractValueObject;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\ArrayVo;

abstract class ContractDataObject implements Arrayable, BuildArrayable, Jsonable
{
    protected const REFLECTION_ACTIVE = false;

    private static array $reflectionCache = [];

    private function getValue($value)
    {
        return ($value instanceof ContractValueObject) ? $value->value() : $value;
    }

    private function toArrayVisible(): array
    {
        $coll = [];
        foreach ($this as $clave => $valor) {
            $coll[$clave] = $this->getValue($valor);
        }
        return object_to_array($coll);
    }

    public function toArray(): array
    {
        return $this->toArrayVisible();
    }

    public function toArrayForBuild(): array
    {
        return $this->toArrayVisible();
    }

    public function toObject(): object|array
    {
        return array_to_object($this->toArrayVisible());
    }

    public function toArrayVo(): ArrayVo
    {
        return ArrayVo::new($this->toArray());
    }

    public static function fromArray(?array $data): static|null
    {
        if (is_null($data)) return null;
        return static::createFromArray($data);
    }

    public static function fromJson(?string $data): static|null
    {
        if (is_null($data)) return null;
        return static::fromArray(json_decode($data, true));
    }

    protected static function createFromArray(array $data): static
    {
        if (!static::REFLECTION_ACTIVE) {
            return new static(...array_values($data));
        }

        $className = static::class;

        // Usamos cache para evitar repetir la reflexión
        if (!isset(self::$reflectionCache[$className])) {
            $reflection  = new ReflectionClass($className);
            $constructor = $reflection->getConstructor();

            if (!$constructor) {
                throw new AppException("The " . static::class . " class has no constructor.");
            }

            self::$reflectionCache[$className] = $constructor->getParameters();
        }

        $parameters = self::$reflectionCache[$className];
        $args       = [];

        foreach ($parameters as $param) {
            $name = $param->getName();
            $type = $param->getType();

            if (!$type) {
                throw new AppException("The \$$name parameter in " . static::class . " does not have a defined type.");
            }

            $typeName = $type->getName();
            $value    = $data[$name] ?? null;

            $typeIsClass        = class_exists($typeName);
            $valueIsNotInstance = !($value instanceof $typeName);

            if ($typeIsClass && $valueIsNotInstance) {
                // Si el tipo es una clase y el valor NO es una instancia, creamos la instancia de la clase
                $method = match (true) {
                    is_a($typeName, \BackedEnum::class, true)         => 'from',
                    is_a($typeName, ContractValueObject::class, true) => 'new',
                    default                                           => 'fromArray',
                };

                $args[] = $typeName::$method($value);
            } else {
                // Si no, pasamos el valor directamente
                $args[] = $value;
            }
        }

        return new static(...$args);
    }

    public function toJson($options = 0): false|string
    {
        return json_encode($this->toArray(), $options);
    }

    public function __toString()
    {
        return $this->toJson();
    }
}
