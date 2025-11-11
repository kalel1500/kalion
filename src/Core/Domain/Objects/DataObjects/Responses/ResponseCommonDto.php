<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Responses;

use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\AbstractDataTransferObject;

class ResponseCommonDto extends AbstractDataTransferObject
{
    public function __construct(
        public readonly ?int    $statusCode,
        public readonly bool    $success,
        public readonly ?string $message,
        public readonly ?array  $data = null
    )
    {
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data'    => $this->data,
        ];
    }
}
