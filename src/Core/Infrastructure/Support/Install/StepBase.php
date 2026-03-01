<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Install;

use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Objects\StepDto;

abstract class StepBase
{
    public function __construct(
        protected readonly StepDto $data,
    )
    {
    }

    public function print(string $message): void
    {
        if (is_null($this->data->command)) {
            return;
        }

        $text = "      $message";
        $this->data->command->line($text);
    }

    public function prepare(): void
    {
        //
    }

    abstract public function up(): void;

    abstract public function down(): void;
}
