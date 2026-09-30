<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Collections;

use JsonException;
use Thehouseofel\Kalion\Core\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Abstracts\AbstractCollectionDto;

abstract class TabulatorDataCollection extends AbstractCollectionDto
{
    /**
     * Tabulator codifica los filtros como urlencode(json_encode($filters))
     *
     * @throws InvalidValueException
     */
    public static function fromTabulator(string|array|null $value): static
    {
        if (is_null($value)) return static::empty();

        if (is_string($value)) {
            try {
                $value = json_decode(
                    json       : urldecode($value),
                    associative: true,
                    flags      : JSON_THROW_ON_ERROR,
                );
            } catch (JsonException $exception) {
                throw new InvalidValueException(
                    message  : 'The Tabulator value must contain valid JSON.',
                    previous : $exception,
                    debugData: ['tabulator_value' => $value],
                );
            }

            if (! is_array($value)) {
                throw new InvalidValueException('The Tabulator JSON value must decode to an array.');
            }
        }

        return static::fromArray($value);
    }

    public function toTabulator(): string
    {
        return urlencode(json_encode($this->toArray()));
    }
}
