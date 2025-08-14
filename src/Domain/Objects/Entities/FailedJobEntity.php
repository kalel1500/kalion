<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIdNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;

class FailedJobEntity extends AbstractEntity
{
    public function __construct(
        public readonly ModelId|ModelIdNull $id,
        public readonly ModelString     $uuid,
        public readonly ModelString     $connection,
        public readonly ModelString     $queue,
        public readonly ModelString     $payload,
        public readonly ModelString     $exception,
        public readonly ModelString     $failed_at
    )
    {
    }


    /*----------------------------------------------------------------------------------------------------------------*/
    /*---------------------------------------------- Create Functions -----------------------------------------------*/

    protected static function createFromArray(array $data): static
    {
        return new static(
            ModelId::from($data['id'] ?? null),
            ModelString::new($data['uuid']),
            ModelString::new($data['connection']),
            ModelString::new($data['queue']),
            ModelString::new($data['payload']),
            ModelString::new($data['exception']),
            ModelString::new($data['failed_at'])
        );
    }

    protected function toArrayProperties(): array
    {
        return [
            'id'            => $this->id->value(),
            'uuid'          => $this->uuid->value(),
            'connection'    => $this->connection->value(),
            'queue'         => $this->queue->value(),
            'payload'       => $this->payload->value(),
            'exception'     => $this->exception->value(),
            'failed_at'     => $this->failed_at->value(),
        ];
    }
}
