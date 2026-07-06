<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Utilities\Output;

use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleOutputRelay
{
    protected OutputInterface $output;

    public function __construct()
    {
        $this->output = new NullOutput();
    }

    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    public function info(string $message): void
    {
        $this->output->writeln("<info>{$message}</info>");
    }

    public function error(string $message): void
    {
        $this->output->writeln("<error>{$message}</error>");
    }

    public function warn(string $message): void
    {
        $this->output->writeln("<comment>{$message}</comment>");
    }

    public function line(string $message): void
    {
        $this->output->writeln($message);
    }
}
