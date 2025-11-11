<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\Base;

use Thehouseofel\Kalion\Core\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Core\Domain\Exceptions\RequiredDefinitionException;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\AbstractValueObject;

abstract class AbstractBaseEnumVo extends AbstractValueObject
{
    protected ?array $permittedValues  = null;
    protected bool   $caseSensitive    = true;
    protected ?array $translatedValues = null;

    /**
     * @var string|null
     */
    public $value;

    public function __construct($value)
    {
        if (is_null($this->getPermittedValues())) {
            throw new RequiredDefinitionException(sprintf('<%s> needs to define <%s> %s.', class_basename(static::class), '$permittedValues', 'property'));
        }
        $this->ensureIsValidValue($value);
        $this->value = $value;
    }

    protected function ensureIsValidValue(?string $value): void
    {
        $this->checkNullable($value);
        $this->checkPermittedValues($value);
    }

    protected function checkPermittedValues(?string $value)
    {
        if (is_null($value)) return;

        $permittedValues               = $this->getPermittedValues();
        $failPermittedValuesValidation = ($this->caseSensitive)
            ? (! in_array($value, $permittedValues))
            : (! in_array(strtolower($value), array_map('strtolower', $permittedValues)));

        if ($failPermittedValuesValidation) {
            $permittedValuesString = '[' . implode(', ', $permittedValues) . ']';
            throw new InvalidValueException(sprintf('<%s> no permite el valor <%s>. Valores permitidos: <%s>', class_basename(static::class), $value, $permittedValuesString));
        }
    }

    protected function getPermittedValues(): ?array
    {
        return $this->permittedValues;
    }

    public function translatedValue(bool $ucfirst = false): ?string
    {
        if (! is_array($this->translatedValues)) {
            throw new RequiredDefinitionException(sprintf('<%s> necesita definir la variable <$translatedValues>', class_basename(static::class)));
        }
        if ($this->isNull()) {
            return null;
        }
        $value = $this->translatedValues[$this->value];
        return ($ucfirst) ? ucfirst($value) : $value;
    }

}
