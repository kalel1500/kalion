<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelBool;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIdNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelStringNull;

class StatusEntity extends ContractEntity
{
    public function __construct(
        public readonly ModelId|ModelIdNull $id,
        public readonly ModelString     $name,
        public readonly ModelBool       $finalized,
        public readonly ModelString     $code,
        public readonly ModelString     $type,
        public readonly ModelStringNull $icon
    )
    {
    }


    /*----------------------------------------------------------------------------------------------------------------*/
    /*---------------------------------------------- Create Functions -----------------------------------------------*/

    protected static function createFromArray(array $data): static
    {
        return new static(
            ModelId::from($data['id'] ?? null),
            new ModelString($data['name']),
            new ModelBool($data['finalized']),
            new ModelString($data['code']),
            new ModelString($data['type']),
            new ModelStringNull($data['icon'])
        );
    }

    protected function toArrayProperties(): array
    {
        return [
            'id'        => $this->id->value(),
            'name'      => $this->name->value(),
            'finalized' => $this->finalized->value(),
            'code'      => $this->code->value(),
            'type'      => $this->type->value(),
            'icon'      => $this->icon->value(),
        ];
    }
}
