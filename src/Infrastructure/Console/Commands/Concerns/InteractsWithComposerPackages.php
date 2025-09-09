<?php

namespace Thehouseofel\Kalion\Infrastructure\Console\Commands\Concerns;

use Symfony\Component\Process\Process;

use function Illuminate\Support\php_binary;

/**
 * @internal This trait is not meant to be used or overwritten outside the package.
 */
trait InteractsWithComposerPackages
{
    /**
     * Installs the given Composer Packages into the application.
     *
     * @throws \RuntimeException
     * @throws \LogicException
     */
    protected function requireComposerPackages(string $composer, array $packages, bool $isRemove = false): bool
    {
        $action = $isRemove ? 'remove' : 'require';

        if ($composer !== 'global') {
            $command = [$this->phpBinary(), $composer, $action];
        }

        $command = array_merge(
            $command ?? ['composer', $action],
            $packages,
        );

        return ! (new Process($command, $this->laravel->basePath(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });
    }

    /**
     * Get the path to the appropriate PHP binary.
     */
    protected function phpBinary(): string
    {
        return php_binary();
    }
}
