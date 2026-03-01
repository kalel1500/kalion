<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Install;

use Illuminate\Support\Facades\Process;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Exceptions\SkippedStep;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Objects\StepDto;

abstract class StepBase
{
    public function __construct(
        protected readonly StepDto $data,
    )
    {
    }

    public function print(string $message, $style = null): void
    {
        $text = "      $message";
        if ($style === 'warning') {
            $this->data->command?->warn($text);
        } else {
            $this->data->command?->line($text, $style);
        }

    }

    public function prepare(): void
    {
        //
    }

    abstract public function up(): void;

    abstract public function down(): void;

    protected function callUp(): void
    {
        $this->data->from = $this->data->up_from;
        $this->up();
    }

    protected function callDown(): void
    {
        $this->data->from = $this->data->down_from;
        $this->down();
    }

    protected function skipOnDevelop(string $message = 'Skipped step on develop mode'): void
    {
        if ($this->data->developMode) {
            throw new SkippedStep($message);
        }
    }

    protected function skipWithoutExamples(string $message = 'Skipped step because --with-examples is not set'): void
    {
        if (! $this->data->withExamples) {
            throw new SkippedStep($message);
        }
    }

    protected function modifyPackageJsonSection(string $configurationKey, array $items, bool $remove): void
    {
        if (! file_exists($this->data->to)) {
            return;
        }

        $packages = json_decode(file_get_contents($this->data->to), true);

        // Obtenemos la sección que se va a modificar o un array vacío si no existe
        $currentSection = $packages[$configurationKey] ?? [];

        if ($remove) {
            // Eliminamos los elementos especificados
            foreach ($items as $key => $value) {
                unset($currentSection[$key]);
            }

            // Si la sección queda vacía, la eliminamos completamente
            if (empty($currentSection)) {
                unset($packages[$configurationKey]);
            } else {
                $packages[$configurationKey] = $currentSection;
            }
        } else {
            // Añadimos los elementos a la sección
            $packages[$configurationKey] = $items + $currentSection;
            ksort($packages[$configurationKey]);
        }

        // Guardamos los cambios en package.json
        file_put_contents(
            $this->data->to,
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL
        );
    }

    protected function modifyComposerJson(\Closure $callback): void
    {
        if (! file_exists($this->data->to)) {
            return;
        }

        $composer = json_decode(file_get_contents($this->data->to), true); // , 512, JSON_THROW_ON_ERROR
        $composer = $callback($composer);

        $json = json_encode($composer, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL; //  | JSON_THROW_ON_ERROR
        $json = preg_replace_callback(
            '/"keywords": \[\s+([^]]+?)\s+]/s',
            function (array $matches) {
                $keywords = preg_replace('/\s+/', '', $matches[1]);  // Elimina espacios y saltos de línea
                $keywords = str_replace('","', '", "', $keywords);   // Añade un espacio después de cada coma
                return '"keywords": [' . $keywords . ']';
            },
            $json
        );

        file_put_contents($this->data->to, $json);
    }

    protected function execute_Process(array|string $command, string $successMessage, string $failureMessage): void
    {
        try {
            // Ejecutamos el proceso
            $run = Process::run($command);

            // Verificamos si el proceso falló
            if ($run->failed()) {
                throw new \RuntimeException();
            }

            // Imprimimos el mensaje de éxito
            $this->print("=> $successMessage");
        } catch (\Throwable $th) {
            $this->print("=> $failureMessage", 'warning');
            $this->print('=> Please run the following command manually: "' . implode(' ', $command) . '"', 'warning');
            $this->data->command?->error(isset($run) ? $run->errorOutput() : $th->getMessage());
        }
    }
}
