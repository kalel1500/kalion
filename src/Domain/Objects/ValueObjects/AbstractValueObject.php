<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects;

use JsonSerializable;
use Thehouseofel\Kalion\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Abstracts\AbstractBoolVo;

/**
 * @template T of AbstractValueObject
 */
abstract class AbstractValueObject implements JsonSerializable
{
    protected const CLASS_REQUIRED = null;
    protected const CLASS_NULLABLE = null;

    protected bool $nullable = true;
    protected $value;

    public static function new($value): static
    {
        return new static($value);
    }

    abstract public function value(): mixed;

    protected function isNullReceived(): bool
    {
        return is_null($this->value);
    }

    public function isNull(): bool
    {
        return is_null($this->value());
    }

    public function isNotNull(): bool
    {
        return !$this->isNull();
    }

    public function toUppercase(): static
    {
        if ($this->isNotNull()) {
            $this->value = strtoupper($this->value);
        }
        return $this;
    }

    public function toLowercase(): static
    {
        if ($this->isNotNull()) {
            $this->value = strtolower($this->value);
        }
        return $this;
    }

    public function toCamelCase(): static
    {
        if ($this->isNotNull()) {
            $this->value = str_camel($this->value);
        }
        return $this;
    }

    public function toNoSpaces(): static
    {
        if ($this->isNotNull()) {
            $this->value = str_replace(' ', '', $this->value);
        }
        return $this;
    }

    public function toCleanString(): static
    {
        if ($this->isNotNull()) {
            // Eliminar acentos y convertir a caracteres sin tildes
            $this->value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $this->value);

            // Eliminar caracteres especiales excepto letras y números
            $this->value = preg_replace('/[^A-Za-z0-9 ]/', '', $this->value);
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function toNull()
    {
        $class = static::CLASS_NULLABLE;
        return $class::new($this->value);
    }

    /**
     * @return mixed
     */
    public function toNotNull()
    {
        $class = static::CLASS_REQUIRED;
        return $class::new($this->value);
    }

    protected function checkNullable($value): void
    {
        if (!$this->nullable && is_null($value)) {
            throw new InvalidValueException(sprintf('<%s> does not allow the value <%s>.', class_basename(static::class), 'null'));
        }
    }

    public function jsonSerialize(): mixed
    {
        return $this->value;
    }

    public function __toString()
    {
        return (string)$this->value() ?? '';
    }
}
