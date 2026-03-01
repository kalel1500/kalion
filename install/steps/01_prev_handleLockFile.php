<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Config\Kalion;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\StepBase;

#[Step(
    paths        : 'kalion.lock',
    title        : '',
    skip         : false,
    isExamplePath: false
)]
class PrevHandleLockFile extends StepBase
{
    protected string $packageVersion;
    protected array  $filesRelativePath;

    public function prepare(): void
    {
        $this->packageVersion    = Kalion::getInstalledVersion();
        $this->filesRelativePath = $this->getFilesRelativePath();
    }

    public function up(): void
    {
        // $this->deleteFiles();

        $timestamp = now()->toDateTimeString();
        if ($this->data->developMode && File::exists($this->data->to)) {
            $old       = json_decode(File::get($this->data->to), true);
            $timestamp = $old['timestamp'];
        }
        $payload = [
            'package'   => 'kalel1500/kalion',
            'version'   => $this->packageVersion,
            'timestamp' => $timestamp,
            'stubs'     => $this->filesRelativePath,
        ];

        $body = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
        File::put($this->data->to, $body);
    }

    public function down(): void
    {
        File::delete($this->data->to);
    }

    protected function getFilesRelativePath(): array
    {
        $paths = [
            $this->data->pathGenBase,
        ];

        if ($this->data->withExamples) {
            $paths[] = $this->data->pathGenExamples;
        }

        $relativePaths = [];
        foreach ($paths as $path) {
            $allFiles = File::allFiles($path, true);
            foreach ($allFiles as $file) {
                $relativePaths[] = ltrim(str_replace(normalize_path($path), '', $file->getRealPath()), DIRECTORY_SEPARATOR);
            }
        }
        $relativePaths = array_unique($relativePaths);
        sort($relativePaths);
        return $relativePaths;
    }

    protected function deleteFiles(): void
    {
        if (! File::exists($this->data->to)) {
            return;
        }

        $old         = json_decode(File::get($this->data->to), true);
        $lastVersion = $old['version'];
        $oldFiles    = $old['stubs'];

        if (version_compare($lastVersion, $this->packageVersion, '=')) {
            return;
        }

        $toDelete = array_diff($oldFiles, $this->filesRelativePath);
        foreach ($toDelete as $rel) {
            $full = base_path($rel);
            if (File::exists($full)) {
                File::delete($full);
                $this->print("→ Eliminado obsoleto: $rel");
                // si la carpeta queda vacía, la eliminamos también
                $dir = dirname($full);
                if (File::isDirectory($dir) && count(File::files($dir)) === 0) {
                    File::deleteDirectory($dir);
                    $this->print("→ Carpeta vacía eliminada: " . str_replace(base_path() . '/', '', $dir));
                }
            }
        }
    }
}
