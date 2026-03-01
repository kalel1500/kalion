<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Install;

use Illuminate\Console\Command;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Objects\InstallDto;

class ProcessorBuilder
{
    protected ?InstallDto $data = null;
    protected ?Command $command = null;

    public function withData(InstallDto $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function withCommand(Command $command): self
    {
        $this->command = $command;
        return $this;
    }

    public function build(): InstallStepProcessor
    {
        return new InstallStepProcessor(
            $this->data,
            $this->command
        );
    }
}
