<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Install;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Title;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Objects\InstallDto;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Objects\StepDto;
use function Illuminate\Filesystem\join_paths;

class InstallStepProcessor
{
    protected int   $steps;
    protected int   $number = 0;
    protected array $cached = [];

    public function __construct(
        protected readonly InstallDto $data,
        protected readonly ?Command   $command = null,
    )
    {
    }

    public function execute(): void
    {
        $method = $this->data->reset ? 'down' : 'up';

        $files = glob($this->data->pattern);
        natsort($files); // Orden natural: 1, 2, 10 en lugar de 1, 10, 2

        if ($this->data->selectedStep) {
            $this->saveFileData($this->data->selectedStep, $method);
        } else {
            foreach ($files as $file) {
                $this->saveFileData($file, $method);
            }
        }

        $this->steps = collect($this->cached)->where('skip', false)->count();

        foreach ($this->cached as $item) {
            try {
                if ($item['skip']) {
                    continue;
                }

                $this->number++;

                $message = sprintf($item['title'], ...$item['values']);
                $this->command?->line("  - <fg=yellow>$this->number/$this->steps</> $message");

                /** @var StepBase $class */
                $class = new $item['className'](new StepDto(
                    command           : $this->command,
                    withExamples      : $this->data->withExamples,
                    developMode       : $this->data->developMode,
                    keepMigrationsDate: $this->data->keepMigrationsDate,
                    pathGenBase       : $this->data->pathGenBase,
                    pathGenExamples   : $this->data->pathGenExamples,
                    stepPaths         : $item['paths'],
                    fromUp            : $item['fromUp'],
                    from              : $item['from'],
                    to                : $item['to'],
                ));
                $class->prepare();
                $class->{$method}();

                $this->command?->line("      <fg=green>=> OK</>");
            } catch (\Throwable $th) {
                $this->handleError($th, 'k::error.failed_executing_$step', $item['className']);
            }
        }
    }

    protected function saveFileData(string $file, string $method): void
    {
        try {
            require_once $file;

            $className = $this->getClassNameFromFile($file);

            $reflection = new \ReflectionClass($className);

            $attributes = $reflection->getAttributes(Step::class);

            if (empty($attributes)) {
                return;
            }

            /** @var Step $stepAttribute */
            $stepAttribute = $attributes[0]->newInstance();

            $from   = $this->getFrom($stepAttribute, false);
            $fromUp = $this->getFrom($stepAttribute, true);
            $to     = $this->getTo($stepAttribute);

            $values          = [];
            $titleAttributes = $reflection->getMethod($method)->getAttributes(Title::class);
            if (! empty($titleAttributes)) {
                /** @var Title $attr */
                $attr   = $titleAttributes[0]->newInstance();
                $values = $attr->values;
            }

            $this->cached[$file] = [
                'fileName'  => $file,
                'skip'      => $stepAttribute->skip,
                'title'     => $stepAttribute->title,
                'paths'     => $stepAttribute->paths,
                'values'    => $values,
                'className' => $className,
                'fromUp'    => $fromUp,
                'from'      => $from,
                'to'        => $to,
            ];
        } catch (\Throwable $th) {
            $this->handleError($th, 'k::error.failed_processing_$step', $file);
        }
    }

    protected function getClassNameFromFile($path): ?string
    {
        $contents = file_get_contents($path);
        $tokens   = token_get_all($contents);
        $count    = count($tokens);

        for ($i = 2; $i < $count; $i++) {
            // Buscamos la palabra reservada 'class'
            if ($tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
                return $tokens[$i][1]; // Retorna el nombre de la clase
            }
        }
        return null;
    }

    protected function getFrom(Step $stepAttribute, bool $forceUp): string|array
    {
        $paths = Arr::wrap($stepAttribute->paths);
        $fullPaths = [];
        foreach ($paths as $path) {
            $fullPaths[] = match (true) {
                $this->data->reset && ! $forceUp => normalize_path(join_paths($this->data->pathOriginal, $path)),
                $stepAttribute->isExamplePath    => normalize_path(join_paths($this->data->pathGenExamples, $path)),
                default                          => normalize_path(join_paths($this->data->pathGenBase, $path)),
            };
        }
        return count($fullPaths) === 1 ? $fullPaths[0] : $fullPaths;
    }

    protected function getTo(Step $stepAttribute): string|array
    {
        $paths = Arr::wrap($stepAttribute->paths);
        $fullPaths = [];
        foreach ($paths as $path) {
            $fullPaths[] = normalize_path(base_path($path));
        }
        return count($fullPaths) === 1 ? $fullPaths[0] : $fullPaths;
    }

    protected function handleError(\Throwable $th, string $errorKey, string $step): void
    {
        $message = __($errorKey, ['step' => $step]);
        $this->command?->error($message . ": {$th->getMessage()}");
        Log::error($message, ['exception' => $th]);
    }
}
