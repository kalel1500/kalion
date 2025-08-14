<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIdNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelInt;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIntNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;

class JobEntity extends AbstractEntity
{
    public function __construct(
        public readonly ModelId|ModelIdNull $id,
        public readonly ModelString     $queue,
        public readonly ModelString     $payload,
        public readonly ModelInt        $attempts,
        public readonly ModelIntNull    $reserved_at,
        public readonly ModelInt        $available_at,
        public readonly ModelString     $created_at
    )
    {
    }


    /*----------------------------------------------------------------------------------------------------------------*/
    /*---------------------------------------------- Create Functions -----------------------------------------------*/

    protected static function createFromArray(array $data): static
    {
        return new static(
            ModelId::from($data['id'] ?? null),
            ModelString::new($data['queue']),
            ModelString::new($data['payload']),
            ModelInt::new($data['attempts']),
            ModelIntNull::new($data['reserved_at'] ?? null),
            ModelInt::new($data['available_at']),
            ModelString::new($data['created_at'])
        );
    }

    protected function toArrayProperties(): array
    {
        return [
            'id'           => $this->id->value(),
            'queue'        => $this->queue->value(),
            'payload'      => $this->payload->value(),
            'attempts'     => $this->attempts->value(),
            'reserved_at'  => $this->reserved_at->value(),
            'available_at' => $this->available_at->value(),
            'created_at'   => $this->created_at->value(),
        ];
    }
}
